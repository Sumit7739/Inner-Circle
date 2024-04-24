<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$updateSuccess = false;

// Check if user is logged in and fetch the user ID from the session
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

// Database Connection Details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chaijn";

// Create connection using PDO with prepared statements
try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// Get user ID from the URL
if (isset($_GET['userid'])) {
  $serialnumber = $_GET['userid'];

  // Fetch user details based on the user ID using prepared statements
  $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
  $stmt->bindParam(':id', $serialnumber);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    $fullname = $row['name'];
    $email = $row['email'];
    $totalPoints = calculateTotalPoints($serialnumber, $conn); // Calculate total points
    $joindate = $row['joindate'];

    // Handle updating points if form is submitted
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['amountToUpdate'])) {
      $amountToUpdate = intval($_POST['amountToUpdate']);
      if ($amountToUpdate > 0) {
        // Calculate points based on the amount
        $points = calculatePoints($amountToUpdate);

        // Insert new transaction record into 'amounts' table using prepared statements
        $dateAdded = date("Y-m-d H:i:s");
        $stmtInsert = $conn->prepare("INSERT INTO amounts (user_id, amount, points, date_added) VALUES (:user_id, :amount, :points, :date_added)");
        $stmtInsert->bindParam(':user_id', $serialnumber);
        $stmtInsert->bindParam(':amount', $amountToUpdate);
        $stmtInsert->bindParam(':points', $points);
        $stmtInsert->bindParam(':date_added', $dateAdded);

        try {
          $conn->beginTransaction();
          $stmtInsert->execute();

          // Calculate total points
          $totalPoints = calculateTotalPoints($serialnumber, $conn);

          // Update total points in 'users' table using prepared statements
          $stmtUpdateTotalPoints = $conn->prepare("UPDATE users SET totalpoints = :totalPoints WHERE id = :id");
          $stmtUpdateTotalPoints->bindParam(':totalPoints', $totalPoints);
          $stmtUpdateTotalPoints->bindParam(':id', $serialnumber);
          $stmtUpdateTotalPoints->execute();

          $conn->commit();
          $updateSuccess = true;
        } catch (Exception $e) {
          $conn->rollBack();
          echo "Error updating points: " . $e->getMessage();
        }
      } else {
        echo "Invalid amount.";
      }
    }

    // Fetch transaction data for the user using prepared statements
    $stmtTransactions = $conn->prepare("SELECT * FROM amounts WHERE user_id = :user_id ORDER BY date_added DESC");
    $stmtTransactions->bindParam(':user_id', $serialnumber);
    $stmtTransactions->execute();
    $transactionData = $stmtTransactions->fetchAll(PDO::FETCH_ASSOC);
  } else {
    // User not found, handle it accordingly
    echo "User not found.";
    exit();
  }
} else {
  // User ID not provided in the URL, handle it accordingly
  echo "User ID not provided.";
  exit();
}

// Calculate Points Function
function calculatePoints($amount)
{
  $points = 0;
  if ($amount >= 50 && $amount <= 100) {
    $points = 2;
  } elseif ($amount >= 101 && $amount <= 150) {
    $points = 4;
  } elseif ($amount >= 151 && $amount <= 200) {
    $points = 8;
  } elseif ($amount >= 201 && $amount <= 300) {
    $points = 12;
  } elseif ($amount >= 301 && $amount <= 400) {
    $points = 18;
  } elseif ($amount >= 401 && $amount <= 500) {
    $points = 30;
  } elseif ($amount > 500) {
    $points = 50;
  }
  return $points;
}

// Calculate Total Points Function
function calculateTotalPoints($userId, $conn)
{
  $totalPoints = 0;
  $stmtAddition = $conn->prepare("SELECT SUM(points) AS addition_points FROM amounts WHERE user_id = :user_id");
  $stmtAddition->bindParam(':user_id', $userId);
  $stmtAddition->execute();
  $additionPoints = $stmtAddition->fetchColumn();

  $stmtRedemption = $conn->prepare("SELECT SUM(redeemed_points) AS redemption_points FROM redeemedpoints WHERE user_id = :user_id");
  $stmtRedemption->bindParam(':user_id', $userId);
  $stmtRedemption->execute();
  $redemptionPoints = $stmtRedemption->fetchColumn();

  $totalPoints = $additionPoints - $redemptionPoints;
  return $totalPoints;
}

// Close connection (not necessary for PDO, as it will be automatically closed when script execution ends)
$conn = null;
?>

<!-- Rest of your HTML code goes here for displaying user information, transaction history, and the form for updating points. -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Update point</title>
  <link rel="stylesheet" href="updatepoints.css">
  <style>
    .search a {
      position: absolute;
      margin-top: 10px;
      margin-left: 140px;
      font-size: 20px;
      color: #fff;
      text-decoration: none;
    }
  </style>
</head>

<body>
  <header>
    <div class="navbar-logo">
      <img src="image/logo.png" alt="logo" />
    </div>
    <div class="navigation">
      <a href="admin.php">Home</a>
    </div>
  </header>
  <div class="info">
    <div class="userinfo">
      <h2>
        Welcome,
        <?php echo $fullname; ?>
      </h2>
      <h2>
        Id -
        <?php echo $serialnumber; ?>
      </h2>
      <h2>
        - Total Points -
        <?php echo $totalPoints; ?>
      </h2>
    </div>
  </div>
  <div class="update">
    <h2>Update Points</h2>
    <div class="updatebox">
      <form method="POST">
        <!-- Hidden field to store the user's serial number -->
        <input type="hidden" name="serialnumber" value="<?php echo $serialnumber; ?>">
        <input type="number" id="amountToUpdate" name="amountToUpdate" min="1" required />
        <button type="submit" class="btnUpdate">Update</button>
      </form>
      <?php
      if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['amountToUpdate'])) {
        if ($updateSuccess) {
          echo "<h3>Points updated successfully. Total points: $totalPoints. Amount: $amountToUpdate.</h3>";
          echo '<script>setTimeout(function(){ window.location.href = "admin.php"; }, 3000);</script>';
          exit();
        } else {
          echo "<h3>Error updating points. Please try again later.</h3>";
          echo '<script>setTimeout(function(){ window.location.href = "admin.php"; }, 3000);</script>';
        }
      }
      ?>
    </div>
  </div>

  <div class="container">
    <h1>Transaction History for
      <?php echo $fullname; ?>
    </h1>
    <table id="transactionTable">
      <thead>
        <tr>
          <th>S.no</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($transactionData) {
          $sno = 1;
          foreach ($transactionData as $transaction) {
            $date = $transaction['date_added'];
            $amount = $transaction['amount'];
            $points = $transaction['points'];
            ?>
            <tr>
              <td>
                <?php echo $sno; ?>
              </td>
              <td>
                <?php echo $date; ?>
              </td>
              <td>
                <?php echo $amount; ?>
              </td>
              <td>
                <?php echo $points; ?>
              </td>
            </tr>
            <?php
            $sno++;
          }
        } else {
          echo '<tr><td colspan="4">No transaction history found for this user.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</body>

</html>