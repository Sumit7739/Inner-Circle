<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "chaijn";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch all data from the 'amounts' table
$sql = "SELECT id, user_id, amount, points, date_added FROM amounts";
$result = $conn->query($sql);

$data = []; // Initialize an empty array to store the fetched data

if ($result->num_rows > 0) {
    // Output data of each row and store it in the $data array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        // echo "ID: " . $row["id"] . " - User ID: " . $row["user_id"] . " - Amount: " . $row["amount"] . " - Points: " . $row["points"] . " - Date Added: " . $row["date_added"] . "<br>";
    }
} else {
    echo "0 results";
}

// Check if a specific date is selected
if (isset($_POST['selected_date'])) {

    // Get the selected date from the form
    $selectedDate = $_POST['selected_date'];

    // Modify the SQL query to filter data based on the selected date
    $sql = "SELECT id, user_id, amount, points, date_added FROM amounts WHERE DATE(date_added) = '$selectedDate'";

    // Set the default value for the selected date input field
    $selectedDate = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d'); // Default to today's date if no date is selected

    // Get the minimum and maximum dates from the fetched data
    $minDate = $data ? min(array_column($data, 'date_added')) : date('Y-m-d');
    $maxDate = $data ? max(array_column($data, 'date_added')) : date('Y-m-d');

    // Set the default selected date
    $selectedDate = isset($_POST['selected_date']) ? $_POST['selected_date'] : $minDate;
    // Execute the modified SQL query
    $result = $conn->query($sql);

    // Fetch data and store it in the $data array
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        echo "0 results";
    }
}
// Function to calculate total amount
function calculateTotalAmount($data)
{
    $totalAmount = 0;
    foreach ($data as $row) {
        $totalAmount += $row["amount"];
    }
    return $totalAmount;
}

// Function to calculate total points
function calculateTotalPoints($data)
{
    $totalPoints = 0;
    foreach ($data as $row) {
        $totalPoints += $row["points"];
    }
    return $totalPoints;
}

// Calculate total amount and total points
$totalAmount = calculateTotalAmount($data);
$totalPoints = calculateTotalPoints($data);
// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amounts Data</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #c850c0;
            background: -webkit-linear-gradient(45deg, #4158d0, #c850c0);
            background: -o-linear-gradient(45deg, #4158d0, #c850c0);
            background: -moz-linear-gradient(45deg, #4158d0, #c850c0);
            background: linear-gradient(45deg, #4158d0, #c850c0);
        }

        header {
            position: relative;
            top: 0;
            width: 100%;
            height: 76px;
            padding: 30px 100px;
            display: flex;
            border-radius: 0px 0px 0px 0px;
            border: 1px solid #fff;
            background: #fff;
            box-shadow: 0px 10px 4px 0px rgba(0, 0, 0, 0.25);
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .navbar-logo img {
            position: absolute;
            top: 0%;
            left: 2%;
            width: 300px;
            height: 80px;
            z-index: 1;
        }


        .totals-box {
            display: inline-block;
            margin-bottom: 20px;
            margin-left: 20px;
            border: 1px solid #000;
            background-image: linear-gradient(to right, #434343 0%, black 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        .totals-box2 {
            display: inline-block;
            margin-bottom: 20px;
            margin-left: 20px;
            margin-right: 50px;
            border: 1px solid #000;
            background-image: linear-gradient(to right, #434343 0%, black 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        /* Style the calendar button */
        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Style the button */
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 14px;
        }

        /* Style the anchor tag */
        a {
            padding: 10px 20px;
            background-color: #ccc;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Change anchor tag styles on hover */
        a:hover {
            background-color: #ddd;
        }

        h2 {
            margin-top: 10px;
            color: #fff;
            align-items: center;
            text-align: center;
        }

        .container-table100 {
            margin-top: 10px;
            max-height: 700px;
            overflow-y: auto;
        }

        .container-table100::-webkit-scrollbar {
            width: 12px;
        }

        table {
            border-spacing: 1;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            width: 95%;
            margin: 0 auto;
            position: relative;
        }

        table * {
            position: relative;
        }

        table td,
        table th {
            padding-left: 8px;
        }

        table thead tr {
            height: 60px;
            background: #36304a;
        }

        table tbody tr {
            height: 50px;
        }

        table tbody tr:last-child {
            border: 0;
        }

        table td,
        table th {
            text-align: left;
        }

        table td.l,
        table th.l {
            text-align: right;
        }

        table td.c,
        table th.c {
            text-align: center;
        }

        table td.r,
        table th.r {
            text-align: center;
        }

        .table100-head th {
            font-family: OpenSans-Regular;
            font-size: 18px;
            color: #fff;
            line-height: 1.2;
            font-weight: unset;
        }

        tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        tbody tr {
            font-family: OpenSans-Regular;
            font-size: 15px;
            color: gray;
            line-height: 1.2;
            font-weight: unset;
        }

        tbody tr:hover {
            color: #555;
            background-color: #f5f5f5;
            cursor: pointer;
        }

        .column1 {
            width: 60px;
            padding-left: 40px;
        }

        .column2 {
            width: 60px;
        }

        .column3 {
            width: 100px;
        }

        .column4 {
            width: 110px;
            /* text-align: right; */
        }

        .column5 {
            width: 170px;
            /* text-align: right; */
        }
    </style>
</head>

<body>
    <header>
        <div class="navbar-logo">
            <img src="image/logo.png" alt="logo" />
        </div>
        <div class="nav">
            <a href="admin.php">Home</a>
        </div>
    </header>

    <div class="totals-box">
        <h3>Total Amount Earned:
            <?php echo $totalAmount; ?>
        </h3>
    </div>

    <div class="totals-box2">
        <h3>Total Points Given:
            <?php echo $totalPoints; ?>
        </h3>
    </div>

    <form method="post" action="" style="display: inline-block;">
        <label for="selected_date">Select Date:</label>
        <input type="date" id="selected_date" name="selected_date" value="<?php echo $selectedDate; ?>"
            min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>">
        <button type="submit">Show Data</button>
        <a href="sales.php">Reset</a>
    </form>
    <h2>Amounts Data</h2>
    <div class="container-table100">
        <div class="wrap-table100">
            <table>
                <thead>
                    <tr class="table100-head">
                        <th class="column1">ID</th>
                        <th class="column2">User ID</th>
                        <th class="column3">Amount</th>
                        <th class="column4">Points</th>
                        <th class="column5">Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if data is found
                    if ($data) {
                        // Loop through the $data array and display it in the table
                        foreach ($data as $row) {
                            echo "<tr>";
                            echo "<td class='column1'>" . $row["id"] . "</td>";
                            echo "<td class='column2'>" . $row["user_id"] . "</td>";
                            echo "<td class='column3'>" . $row["amount"] . "</td>";
                            echo "<td class='column4'>" . $row["points"] . "</td>";
                            echo "<td class='column5'>" . $row["date_added"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        // Display an appropriate message if no data is found
                        echo "<tr><td colspan='5'>No results found for the selected date.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>