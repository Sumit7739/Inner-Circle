<?php
session_start();

// Check if admin is already authenticated, redirect to admin page
if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chaijn";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

// Check if admin details exist
if ($result && $result->num_rows > 0) {
    $adminRow = $result->fetch_assoc();
    $adminID = $adminRow['id'];
    $adminPassword = $adminRow['password'];

    // Validate admin login credentials
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $enteredID = $_POST["id"] ?? "";
        $enteredPassword = $_POST["password"] ?? "";

        $enteredID = trim($enteredID);
        $enteredPassword = trim($enteredPassword);

        // Check if entered ID and password match the admin credentials
        if ($enteredID === $adminID && $enteredPassword === $adminPassword) {
            // Admin login successful, set admin session
            $_SESSION['admin'] = true;
            header("Location: admin.php");
            exit();
        } else {
            // Invalid admin login credentials
            $error = "Invalid login credentials";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleadmin.css">
    <title>Admin Login</title>
    <style>
        header img {
            width: 250px;
        }
    </style>
</head>

<body>
    <div class="nav">
        <header>
            <a href="index.html"><img src="image/logo.png" alt="Logo" /></a>
        </header>
    </div>

    <section>
        <div class="login-box">
            <form method="POST">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <p>
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>
                <div class="input-box">
                    <input type="text" id="id" name="id" required>
                    <label for="id">ID</label>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </section>
</body>

</html>