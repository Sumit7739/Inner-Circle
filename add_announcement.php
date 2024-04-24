<?php
// Establish database connection
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

// Get announcement text from POST request
$announcementText = $_POST['announcementText'];

// Prepare SQL statement for insertion
$stmt = $conn->prepare("INSERT INTO announcements (announcement_text, announcement_date) VALUES (?, NOW())");
$stmt->bind_param("s", $announcementText);


if ($stmt->execute()) {
    echo '<h1>"Announcement added successfully!"</h1>';
    // echo '<h2'
    echo '<script>setTimeout(function(){ window.location.href = "admin.php"; }, 3000);</script>';
    exit();
} else {
    echo "Error: " . $stmt->error;
}


// // Execute the statement
// if ($stmt->execute()) {
//     echo "Announcement added successfully!";
// } else {
//     echo "Error: " . $stmt->error;
// }

// Close connection
$stmt->close();
$conn->close();