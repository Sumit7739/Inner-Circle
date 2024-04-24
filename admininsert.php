<!DOCTYPE html>
<html>
<head>
    <title>Add Admin Login Details</title>
</head>
<body>
    <?php
    // Database connection configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "chaijn";

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $id = $_POST["id"];
        $password = $_POST["password"];

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO admin (id, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $id, $password);

        if ($stmt->execute()) {
            echo "Admin login details inserted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>

    <h2>Add Admin Login Details</h2>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="id">ID:</label>
        <input type="text" id="id" name="id" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>
