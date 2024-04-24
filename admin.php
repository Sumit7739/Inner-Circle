<?php
session_start();

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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details
$sql = "SELECT id FROM admin"; // Select only the 'name' column
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $adminName = $row['id']; // Store the admin name in a variable
} else {
  // Handle case when admin details are not found
  $adminName = "Admin Not Found";
}


// Default values
$fullname = "";
$email = "";
$serialnumber = "";
$totalPoints = 0;
$joindate = "";

// Handle Search
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $searchTerm = $_POST["searchTerm"] ?? "";
  $searchTerm = trim($searchTerm);
  $searchTerm = mysqli_real_escape_string($conn, $searchTerm);

  $sql = "SELECT * FROM users WHERE id = '$searchTerm' OR name LIKE '%$searchTerm%'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['name'];
    $email = $row['email'];
    $serialnumber = $row['id'];
    $totalPoints = $row['totalpoints'];
    $joindate = $row['joindate'];
  } else {
    $fullname = "User Not Found";
  }
}

// Fetch total users count
$sqlTotalUsers = "SELECT COUNT(*) AS totalUsersCount FROM users";
$resultTotalUsers = $conn->query($sqlTotalUsers);
$rowTotalUsers = $resultTotalUsers->fetch_assoc();
$totalUsersCount = $rowTotalUsers['totalUsersCount'];


// // Fetch new users count for today (assuming you have a registration date column in your users table)
$today = date("Y-m-d");
$sqlNewUsersToday = "SELECT COUNT(*) AS newUsersTodayCount FROM users WHERE DATE(joindate) = '$today'";
$resultNewUsersToday = $conn->query($sqlNewUsersToday);
$rowNewUsersToday = $resultNewUsersToday->fetch_assoc();
$newUsersTodayCount = $rowNewUsersToday['newUsersTodayCount'];

// // Fetch total points given today (assuming you have a transactions table with points and transaction_date columns)
$sqlTotalPointsToday = "SELECT SUM(points) AS totalPointsToday FROM amounts WHERE DATE(date_added) = '$today'";
$resultTotalPointsToday = $conn->query($sqlTotalPointsToday);
$rowTotalPointsToday = $resultTotalPointsToday->fetch_assoc();
$totalPointsGivenToday = $rowTotalPointsToday['totalPointsToday'];

// // Fetch total sales today (assuming you have a sales table with amount and sale_date columns)
$sqlTotalSalesToday = "SELECT SUM(amount) AS totalSalesToday FROM amounts WHERE DATE(date_added) = '$today'";
$resultTotalSalesToday = $conn->query($sqlTotalSalesToday);
$rowTotalSalesToday = $resultTotalSalesToday->fetch_assoc();
$totalSalesToday = $rowTotalSalesToday['totalSalesToday'];


$sql = "SELECT COUNT(*) as review_count FROM reviews";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $reviewCount = $row['review_count'];
} else {
  $reviewCount = 0;
}
// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style_admin.css">
  <title>Admin</title>
  <style>
    .searchbox a {
      margin-left: 380px;
      font-size: 20px;
    }

    .links {
      position: absolute;
      top: 52%;
      left: 50%;
      margin-top: 20px;
      text-align: center;
    }

    .links a {
      display: inline-block;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      text-align: center;
      text-decoration: none;
      font-size: 16px;
      border-radius: 5px;
      margin: 0 10px;
      /* Added margin for spacing between buttons */
    }

    .links a:hover {
      background-color: #45a049;
    }

    .message {
      display: inline-block;
      margin-right: 10px;
      color: #fff;
      /* You can adjust the color as per your design */
    }
  </style>
</head>

<body>
  <header>
    <div class="navbar-logo">
      <a href="admin.php">
        <img src="image/logo.png" alt="logo" />
      </a>
    </div>
    <div class="navigation">
      <div class="admininfo">
        <a>Welcome,
          <?php echo $adminName; ?>
        </a>
      </div>
      <div class="admindetails">
        <a href="adminacc.php">Account</a>
        <a href="adminlogout.php">Logout</a>
      </div>
    </div>
  </header>
  <div class="border">
    <div class="box1">
      <a href="user_table.php">User's Table</a>
    </div>
    <div class="box1">
      <a href="sales.php">Sales Table</a>
    </div>
    <div class="box1">
      <a href="#">Graph</a>
    </div>
  </div>
  <div class="act">
    <h1>Activity</h1>
    <div class="container1">
      <h2>Total Users -
        <?php echo $totalUsersCount; ?>
      </h2>
      <a href="user_table.php">view table</a>
    </div>
    <div class="container2">
      <h2>New User's Today -
        <?php echo $newUsersTodayCount; ?>
      </h2>
    </div>
    <div class="container3">
      <h2>Total Points Given Today-
        <?php echo $totalPointsGivenToday; ?>
      </h2>
      <a href="sales.php">view table</a>
    </div>
    <div class="container4">
      <h2>Total Sales Today -
        <?php echo $totalSalesToday; ?>
      </h2>
      <a href="sales.php">view table</a>
    </div>

  </div>
  <div class="notice">
    <h1>Announcements</h1>
    <div class="textbox">
      <form method="post" action="add_announcement.php">
        <input type="text" id="textbox" name="announcementText" placeholder="write the notice here!" required />
        <button type="submit">Post</button>
        <a href="adminnotice.php">View All</a>
      </form>
    </div>
  </div>
  <!-- Search Bar -->
  <div class="searchbox">
    <h1>Search User's Information</h1>
    <form method="post" action="">
      <div class="search">
        <input type="search" id="search" name="searchTerm" placeholder="Enter User Id or Name" required />
        <button type="submit">Search</button>
        <a href="admin.php">Reset</a>
      </div>
    </form>
  </div>

  <div class="info">
    <!-- <div class="imgbox">
      <img src="image/pr.jpg" alt="image" />
    </div> -->
    <div class="userinfo">
      <h2>- Id -
        <?php echo $serialnumber; ?>
      </h2>
      <h2>-
        <?php echo $fullname; ?>
      </h2>
      <h2>-
        <?php echo $email; ?>
      </h2>
      <h2>- Total Points -
        <?php echo $totalPoints; ?>
      </h2>
      <h2>- Join Date -
        <?php echo $joindate; ?>
      </h2>
    </div>
  </div>
  <div class="links">
    <?php
    // Check if search term is empty
    if (empty($searchTerm)) {
      echo '<span class="message">Enter User Id</span>';
    } elseif ($serialnumber) {
      // Check if user is present before generating links
      echo '<a href="updatepoint.php?userid=' . $serialnumber . '">Update Points</a>';
      echo '<a href="redeempoint.php?userid=' . $serialnumber . '">Redeem Points</a>';
    } else {
      echo '<span class="message">User Not Found</span>';
    }
    ?>
  </div>

  <div class="reviews">
    <h2>Total Reviews:
      <?php echo $reviewCount; ?>
    </h2>
    <br>
    <a href="view_reviews.php">View Reviews</a>
  </div>

</body>
<style>
  .reviews {
    position: fixed;
    bottom: 20%;
    right: 30%;
    background-image: linear-gradient(to right, #434343 0%, black 100%);
    color: white;
    padding: 20px 50px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
  }
</style>

</html>