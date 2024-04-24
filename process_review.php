<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['id'];
    $reviewText = $_POST['review_text'];
    $rating = $_POST['rating'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chaijn";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, review_text, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $userId, $reviewText, $rating);

    if ($stmt->execute()) {
        echo "<h1>Review submitted successfully!</h1>";
        echo '<script>setTimeout(function(){ window.location.href = "profile.php"; }, 3000);</script>';
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>