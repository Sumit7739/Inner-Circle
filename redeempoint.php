<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$redeemSuccess = false;

if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chaijn";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}


$serialnumber = "";
$fullname = "";
$email = "";
$totalPoints = 0;

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

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['amountToredeem']) && !empty($_POST['amountToredeem'])) {
      $amountToRedeem = intval($_POST['amountToredeem']);

      // Fetch user's total points
      $totalPoints = calculateTotalPoints($serialnumber, $conn);


      // Validate amountToRedeem input and check if it's not empty
      if (!empty($amountToRedeem) && $amountToRedeem > 0 && $amountToRedeem <= $totalPoints) {
        $conn->beginTransaction();

        // Deduct points from total points
        $newTotalPoints = $totalPoints - $amountToRedeem;

        // Update total points in the users table
        $stmtUpdateTotalPoints = $conn->prepare("UPDATE users SET totalpoints = :totalpoints WHERE id = :user_id");
        $stmtUpdateTotalPoints->bindParam(':totalpoints', $newTotalPoints, PDO::PARAM_INT);
        $stmtUpdateTotalPoints->bindParam(':user_id', $serialnumber, PDO::PARAM_INT);
        $stmtUpdateTotalPoints->execute();

        // Record redemption in redemption_transactions table
        $stmtRedeem = $conn->prepare("INSERT INTO redeemedpoints (user_id, redeemed_points, date_redeemed) VALUES (:user_id, :redeemed_points, NOW())");
        $stmtRedeem->bindParam(':user_id', $serialnumber, PDO::PARAM_INT);
        $stmtRedeem->bindParam(':redeemed_points', $amountToRedeem, PDO::PARAM_INT);
        $stmtRedeem->execute();

        $conn->commit();
        $redeemSuccess = true;
      } else {
        // Invalid redemption amount or empty input
        $redeemSuccess = false;
      }
    }

    // Fetch redeem points data from the database
    $stmtRedeem = $conn->prepare("SELECT * FROM redeemedpoints WHERE user_id = :user_id ORDER BY date_redeemed DESC");
    $stmtRedeem->bindParam(':user_id', $serialnumber);
    $stmtRedeem->execute();
    $redeemPointsData = $stmtRedeem->fetchAll(PDO::FETCH_ASSOC);
  } else {
    // Invalid or missing user id in the URL
    echo "Invalid user id.";
  }
} else {
  // User ID not provided in the URL, handle it accordingly
  echo "User ID not provided.";
  exit();
}
// Function to calculate total points
function calculateTotalPoints($userId, $conn)
{
  $stmtAddition = $conn->prepare("SELECT SUM(points) FROM amounts WHERE user_id = :user_id");
  $stmtAddition->bindParam(':user_id', $userId, PDO::PARAM_INT);
  $stmtAddition->execute();
  $additionPoints = $stmtAddition->fetchColumn();

  $stmtRedemption = $conn->prepare("SELECT SUM(redeemed_points) FROM redeemedpoints WHERE user_id = :user_id");
  $stmtRedemption->bindParam(':user_id', $userId, PDO::PARAM_INT);
  $stmtRedemption->execute();
  $redemptionPoints = $stmtRedemption->fetchColumn();

  $totalPoints = $additionPoints - $redemptionPoints;
  return $totalPoints;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redeem Points</title>
  <link rel="stylesheet" href="redeempoints.css">
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
      <img src="image/logo.png" alt="logo">
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
        ...Id -
        <?php echo $serialnumber; ?>
      </h2>
      <h2>
        - Total Points -
        <?php echo $totalPoints; ?>
      </h2>
    </div>
  </div>
  <div class="redeem">
    <h2>Redeem Points</h2>
    <div class="redeembox">
      <form method="POST">
        <!-- Hidden field to store the user's serial number -->
        <input type="hidden" name="serialnumber" value="<?php echo $serialnumber; ?>">
        <input type="number" id="amountToredeem" name="amountToredeem" min="1" required>
        <button type="submit" class="btnredeem">Redeem</button>
      </form>
      <?php
      if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['amountToredeem'])) {
        if ($redeemSuccess) {
          echo "<h2>Points redeemed successfully. Redeemed Points: $amountToRedeem. Remaining Points: $newTotalPoints.</h2>";
          echo '<script>setTimeout(function(){ window.location.href = "admin.php"; }, 3000);</script>';
        } else {
          echo "<p>Points redemption failed. Please check the input and try again.</p>";
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
          <th>Points</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($redeemPointsData) {
          $sno = 1;
          foreach ($redeemPointsData as $redeems) {
            $date = $redeems['date_redeemed'];
            $points = $redeems['redeemed_points'];
            ?>
            <tr>
              <td>
                <?php echo $sno; ?>
              </td>
              <td>
                <?php echo $date; ?>
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