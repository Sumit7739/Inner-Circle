<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['serialnumber'])) {
    header("Location: adminlogin.php");
    exit();
}

$serialnumber = $_SESSION['serialnumber'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chaijn";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the amounts table
$sql = "SELECT user_id, amount, date_added, points FROM amounts ORDER BY date_added DESC";
$result = $conn->query($sql);

// "SELECT user_id, redeem_points, redeem_date FROM reddem WHERE redeem_date >= ? AND redeem_date <= ? ORDER BY redeem_date DESC");
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Amounts Data</title>
    <link rel="stylesheet" href="styleamounttable.css">
   </head>
<body>
    <h2>Amounts Data</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Date Added</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalAmount = 0;
            $totalPoints = 0;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["amount"] . "</td>";
                    echo "<td>" . $row["date_added"] . "</td>";
                    echo "<td>" . $row["points"] . "</td>";
                    echo "</tr>";

                    // Calculate total amount and total points
                    $totalAmount += $row["amount"];
                    $totalPoints += $row["points"];
                }
            } else {
                echo "<tr><td colspan='4'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div>
        <h2>Total Amount:
            <?php echo $totalAmount; ?>
        </h2>
    </div>
    <div>
    <h2> Total Points:</strong>
        <?php echo $totalPoints; ?>
    </h2>
    </div>
        <form action="admin.php" method="POST" class="home">
        <button type="submit" class="btnHome">GO BACK</button>
    </form>
</body>

</html>