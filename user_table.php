<?php

session_start();

// Establish a database connection (modify these with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chaijn";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Create an array to hold user data
$users = [];

// Populate the array with user data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'username' => $row['name'],
            'email' => $row['email'],
            'totalpoints' => $row['totalpoints'],
            'joindate' => $row['joindate'],
        ];
    }
}
$sqlTotalUsers = "SELECT COUNT(*) AS totalUsersCount FROM users";
$resultTotalUsers = $conn->query($sqlTotalUsers);
$rowTotalUsers = $resultTotalUsers->fetch_assoc();
$totalUsersCount = $rowTotalUsers['totalUsersCount'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            /* display: flex; */
            /* justify-content: center; */
            /* align-items: center; */
            height: 100vh;
        }

        header {
            position: relative;
            top: 0;
            width: 100%;
            height: 36px;
            padding: 30px 100px;
            /* display: flex; */
            border-radius: 0px 0px 0px 0px;
            border: 1px solid #fff;
            background: #fff;
            box-shadow: 0px 10px 4px 0px rgba(0, 0, 0, 0.25);
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo img {
            position: absolute;
            top: 10%;
            left: 2%;
            width: 300px;
            height: 80px;
            z-index: 1;
        }

        .nav a {
            position: absolute;
            left: 65%;
            text-decoration: none;
            color: #000;
            font-size: 20px;
            padding: 5px 12px;
            width: 60px;
            height: 20px;
            border-radius: 5px;
            border: 1px solid #E2E2E2;
            background: linear-gradient(90deg, #47C3F8 0%, rgba(152, 223, 255, 0.91) 100%);
            box-shadow: 4px 6px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .border {
            margin-top: 20px;
            margin-left: 10px;
            width: 250px;
            height: 80px;
            border-radius: 15px;
            border: 1px solid #E2E2E2;
            background: linear-gradient(90deg, #47C3F8 0%, rgba(152, 223, 255, 0.91) 100%);
            box-shadow: 4px 6px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .search-container {
            position: absolute;
            top: 15%;
            margin-left: 50%;
            width: 250px;
            height: 30px;
            border-radius: 10px;
            padding: 5px 5px;
            border: 1px solid rgba(239, 239, 239, 0.80);
            background: #FFF;
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);

        }

        .search-container input[type="text"] {
            border: none;
            outline: none;
        }

        button {
            position: absolute;
            margin-left: 110px;
            width: 85px;
            height: 38px;
            border-radius: 6px;
            color: #fff;
            border: 1px solid rgba(227, 227, 227, 0.80);
            background: #000;
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        button:hover {
            cursor: pointer;
        }

        .search-container a {
            position: absolute;
            margin-left: 220px;
            text-decoration: none;
            color: red;
            padding: 10px 10px;
            border-radius: 6px;
            border: 1px solid rgba(227, 227, 227, 0.80);
            background: #000;
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .container {
            width: 80%;
            margin-top: 20px;
            margin-left: 10px;
            background-color: white;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
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
    <div class="border">
        <div class="box1">
            <h1>Total Users -
                <?php echo $totalUsersCount; ?>
            </h1>
        </div>
    </div>
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by ID or Name">
        <button onclick="searchUsers()">Search</button>
        <a href="user_table.php">Reset</a>
    </div>

    <div class="container">
        <h1>Users List</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Total Points</th>
                    <th>Join Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <?php echo $user["id"]; ?>
                        </td>
                        <td>
                            <?php echo $user["username"]; ?>
                        </td>
                        <td>
                            <?php echo $user["email"]; ?>
                        </td>
                        <td>
                            <?php echo $user["totalpoints"]; ?>
                        </td>
                        <td>
                            <?php echo $user["joindate"]; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function searchUsers() {
            var searchTerm = $('#searchInput').val().toLowerCase();
            $('table tbody tr').hide();

            $('table tbody tr').each(function () {
                var id = $(this).find('td:eq(0)').text().toLowerCase();
                var username = $(this).find('td:eq(1)').text().toLowerCase();

                if (id.includes(searchTerm) || username.includes(searchTerm)) {
                    $(this).show();
                }
            });
        }
    </script>
</body>

</html>