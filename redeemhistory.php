<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and fetch the user ID from the session

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
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

// Create connection using PDO with prepared statements
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user ID from the URL
if (isset($_GET['userid'])) {
    $serialnumber = $_GET['userid'];

    // Fetch user details based on the user ID using prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $serialnumber);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $serialNo = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $totalPoints = $row['totalpoints'];
        // Fetch redeem points data from the database
        $stmtRedeem = $conn->prepare("SELECT * FROM redeemedpoints WHERE user_id = :user_id ORDER BY date_redeemed DESC");
        $stmtRedeem->bindParam(':user_id', $serialnumber);
        $stmtRedeem->execute();
        $redeemPointsData = $stmtRedeem->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // User not found, handle it accordingly
        echo "User not found.";
        exit();
    }
} else {
    // User ID not provided in the URL, handle it accordingly
    echo "User ID not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <link rel="icon" href="image/icon.ico" type="image/x-icon" sizes="32x32" />
    <title>history</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        section {
            position: relative;
            width: 100%;
            /* width: 440px; */
            /* height: 722px; */
            height: 100%;
            left: 0%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            background: url(image/bg11.jpeg);
            background-size: 100%;
            background-position: center;
            overflow: hidden;
        }

        header {
            position: relative;
            top: 0;
            width: 100%;
            height: 76px;
            padding: 30px 100px;
            display: flex;
            border-radius: 2px 2px 16px 16px;
            background: #D9D9D9;
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
            justify-content: space-between;
            align-items: center;
        }

        label {
            display: none;
        }

        .navbar-logo img {
            position: absolute;
            top: 2%;
            left: 2%;
            width: 270px;
            height: 70px;
            z-index: 1;
        }

        .head {
            position: absolute;
            top: 2%;
            left: 0%;
            width: 100%;
            /* height: 177px; */
        }

        #check {
            z-index: 3;
            display: none;
        }

        header .navigation a {
            display: inline;
            color: #ffffff;
            font-size: 18px;
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 1px;
            padding: 2px 15px;
            border-radius: 10px;
            transition: 0.3s;
            transition-property: background;
        }


        header .navigation a:not(:last-child) {
            margin-right: 30px;
        }

        */ header .navigation a:hover {
            background: #ffffff;
            color: #000;
        }

        .container {
            position: absolute;
            top: 13%;
            left: 2%;
            width: 95%;
            max-height: 500px;
            overflow-y: auto;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0px 4px 4px 4px rgba(0, 0, 0, 0.55);
            border-radius: 10px;
            padding: 3px 3px;
        }

        .container::-webkit-scrollbar {
            width: 12px;
        }

        h1 {
            margin-top: 10px;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-left: 10px;
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

        .link1 a {
            position: absolute;
            top: 80%;
            left: 2%;
            width: 60px;
            height: 58px;
            border-radius: 12px;
            border: 1px solid #DCDCDC;
            background: rgba(217, 217, 217, 0.50);
            box-shadow: 0px 8px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .link1 img {
            margin-left: 7px;
        }

        .link2 a {
            position: absolute;
            top: 80%;
            left: 22%;
            width: 60px;
            height: 58px;
            border-radius: 10px;
            border: 1px solid rgba(186, 186, 186, 0.80);
            background: rgba(217, 217, 217, 0.50);
            box-shadow: 5px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .link2 img {
            background: lightgray 50% / cover no-repeat;
            mix-blend-mode: darken;
            margin-top: 5px;
            margin-left: 9px;
        }

        .link3 a {
            position: absolute;
            top: 80%;
            left: 42%;
            width: 60px;
            height: 58px;
            border-radius: 10px;
            border: 1px solid rgba(186, 186, 186, 0.80);
            background: #FFF;
            box-shadow: 5px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .link3 img {
            mix-blend-mode: darken;
            margin-top: 10px;
            margin-left: 7px;
        }

        .link4 a {
            position: absolute;
            top: 80%;
            left: 62%;
            width: 60px;
            height: 58px;
            border-radius: 10px;
            border: 1px solid rgba(186, 186, 186, 0.80);
            background: rgba(217, 217, 217, 0.50);
            box-shadow: 5px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .link4 img {
            background: lightgray 50% / cover no-repeat;
            mix-blend-mode: darken;
            margin-top: 8px;
            margin-left: 9px;
        }

        .link5 a {
            position: absolute;
            top: 80%;
            left: 82%;
            width: 60px;
            height: 58px;
            border-radius: 10px;
            border: 1px solid rgba(186, 186, 186, 0.80);
            background: rgba(217, 217, 217, 0.50);
            box-shadow: 5px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .link5 img {
            background: lightgray 50% / cover no-repeat;
            mix-blend-mode: darken;
            margin-top: 8px;
            margin-left: 10px;
        }

        .footer {
            position: absolute;
            bottom: 0;
            text-align: center;
            color: #000;
            font-size: 15px;
            bottom: 0;
            width: 100%;
            padding: 5px;
        }

        .footer hr {
            border: 1.5px solid #BDBDBD;
            ;
            margin: 15px 0;
        }

        @media (max-width: 960px) {
            header .navigation {
                display: none;
            }

            label {
                position: absolute;
                margin-bottom: 20px;
                left: 85%;
                display: block;
                font-size: 33px;
                cursor: pointer;
                transition: 0.3s;
                transition-property: color;
            }

            label .close-btn {
                display: none;
            }

            #check:checked~header .navigation {
                z-index: 4;
                position: fixed;
                top: 0%;
                bottom: 30%;
                left: 55%;
                right: 0%;
                border-radius: 10px;
                background: rgba(0, 0, 0, 0.918);
                box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
                border-radius: 70px 0px 0px 70px;
                border: 1px solid #737373;
                background: #82746C;
                box-shadow: 8px 8px 4px 0px rgba(0, 0, 0, 0.25);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            #check:checked~header .navigation a {
                font-weight: 700;
                margin-right: 0;
                margin-top: 45px;
                margin-bottom: 20px;
                letter-spacing: 2px;
            }

            #check:checked~header label .close-btn {
                z-index: 4;
                position: fixed;
                display: block;
            }

            #check:checked~header label .menu-btn {
                display: none;
            }

            label .menu-btn {
                position: absolute;
            }

            header .logo {
                position: absolute;
                bottom: -6px;
            }
        }
    </style>
</head>

<body>
    <section>
        <input type="checkbox" id="check" />
        <header>
            <div class="navbar-logo">
                <img src="image/logo.png" alt="logo">
            </div>
            <div class="navigation">
                <a href="#">Points Info</a>
                <a href="#">T&C</a>
                <a href="#">Updates</a>
                <a href="#">Developer.</a>
                <a href="userlogout.php">Log Out</a>
            </div>
            <label for="check">
                <i class="fas fa-bars menu-btn"></i>
                <i class="fas fa-times close-btn"></i>
            </label>
        </header>
        <div class="container">
            <h1>Redeem History for
                <?php echo $name; ?>
            </h1>
            <table id="transactionTable">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Date</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($redeemPointsData) {
                        $sno = 1;
                        foreach ($redeemPointsData as $redeems) {
                            $date = $redeems['date_redeemed'];
                            $points = $redeems['redeemed_points'];
                            ?>
                            <tr>
                                <td>
                                    <?php echo $sno; ?>
                                </td>
                                <td>
                                    <?php echo $date; ?>
                                </td>
                                <td>
                                    <?php echo $points; ?>
                                </td>
                            </tr>
                            <?php
                            $sno++;
                        }
                    } else {
                        echo '<tr><td colspan="4">No transaction history found for this user.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="links">
            <?php
            if (empty($serialNo)) {
                echo '<span class="message">No User Id Found</span>';
            } elseif ($serialNo) {
                ?>
                <div class="link1">
                    <a href="profile.php"><img src="image/home.png" alt="home"></a>
                </div>
                <div class="link2">
                    <a href="history.php?userid=<?php echo $serialNo; ?>"><img src="image/his.png" alt="his"></a>
                </div>
                <div class="link3">
                    <a href="redeemhistory.php?userid=<?php echo $serialNo; ?>"><img src="image/redeem-coupons-512 1.png"
                            alt="Redeem"></a>
                </div>
                <div class="link4">
                    <a href="notice.php?userid=<?php echo $serialNo; ?>"><img src="image/bell.png" alt="info"></a>
                </div>
                <div class="link5">
                    <a href="usert&c.html"><img src="image/info.png" alt="T&C"></a>
                </div>
                <?php
            }
            ?>
        </div>
        <footer class="footer">
            <hr>
            <p>T&C &copy; 2023 Chai Junction. All rights reserved.</p>
            <p>Designed and Created by</p>
            <p>Sumit Srivastava and Diwakar Sharma</p>
            <p>version - 2.1.1</p>
    </section>
</body>

</html>