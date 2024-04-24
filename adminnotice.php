<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}
// Fetch user details from the userdetails table based on user ID
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chaijn";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


// Fetch announcements from the database
$stmt = $conn->prepare("SELECT * FROM announcements ORDER BY announcement_date DESC");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ff8a00, #da1b60);
        }

        header {
            position: relative;
            width: 100%;
            height: 76px;
            padding: 20px 100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            /* Text Color */
        }

        .navbar-logo img {
            width: 200px;
            height: 50px;
        }

        .admindetails a {
            text-decoration: none;
            color: #fff;
            font-size: 20px;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 6px;
            background: #000;
            margin-left: 20px;
        }

        h1 {
            margin-top: 50px;
            text-align: center;
            font-size: 36px;
            color: #333;
        }

        .notice-container {
            width: 90%;
            margin: 3% auto;
            max-height: 500px;
            overflow-y: auto;
        }

        .notice {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            box-shadow: 0px 4px 4px 4px rgba(0, 0, 0, 0.55);
            border-radius: 10px;
            margin-bottom: 10px;
            padding: 10px;
        }

        .notice .date {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        /* Custom scrollbar styles */
        .notice-container::-webkit-scrollbar {
            width: 5px;
            background-color: #f5f5f5;
        }

        .notice-container::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 6px;
        }

        .notice-container::-webkit-scrollbar-thumb:hover {
            background-color: #555;
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
            <div class="admindetails">
                <a href="admin.php">Home</a>
            </div>
        </div>
    </header>
    <h1>All Announcements</h1>
    <div class="notice-container">
        <?php foreach ($announcements as $announcement): ?>
            <div class="notice">
                <div class="date">
                    <?php echo htmlspecialchars($announcement['announcement_date']); ?>
                </div>
                <div class="text">
                    <?php echo htmlspecialchars($announcement['announcement_text']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>