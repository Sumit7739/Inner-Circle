<!DOCTYPE html>
<html>

<head>
    <title>Reddem Data</title>
    <link rel="stylesheet" href="styleredeemtable.css">
</head>
<style>
    body {
    background-color: skyblue;
}
</style>

<body>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>User ID</th>
            <th>Redeem Points</th>
            <th>Redeem Date</th>
        </tr>
        <?php
        // Establish a connection to the database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "chaijn";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch data from the "reddem" table
        $sql = "SELECT id, user_id, redeemed_points, date_redeemed FROM redeemedpoints ORDER BY date_redeemed DESC";
        $result = $conn->query($sql);

        $totalRedeemPoints = 0;


        if ($result->num_rows > 0) {
            // Output each row of data
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["user_id"] . "</td>";
                echo "<td>" . $row["redeemed_points"] . "</td>";
                echo "<td>" . $row["date_redeemed"] . "</td>";
                echo "</tr>";
              
                // Calculate total redeem points
                $totalRedeemPoints += $row["redeemed_points"];
                
            }
        } else {
            echo "<tr><td colspan='5'>No data found</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
    <div>
        <h2>Total Redeem Points: <?php echo $totalRedeemPoints; ?>
        </h2>
    </div>
    <form action="admin.php" method="POST" class="home">
        <button type="submit" class="btnHome">GO BACK</button>
    </form>
</body>

</html>