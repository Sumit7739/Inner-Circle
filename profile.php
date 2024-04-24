<?php
session_start();

// Check if user is logged in and fetch the user ID from the session
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

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

    $sql = "SELECT id, name, email, totalpoints FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Error: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $serialNo = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $totalPoints = $row['totalpoints'];
    } else {
        // Handle case when user details are not found
        $serialNo = "";
        $name = "";
        $email = "";
        $totalPoints = 0;
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/icon.ico" type="image/x-icon" sizes="32x32">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />
    <title>Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap');

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
            background-size: cover;
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
            z-index: 1;
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
            font-size: 20px;
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

        header .navigation a:hover {
            background: #ffffff;
            color: #000;
        }

        .container {
            position: absolute;
            /* top: 8%; */
            width: 100%;
            height: 350px;
            margin-left: px;
            border-radius: 0px 0px 30px 30px;
            /* background: rgba(211, 211, 211, 0.50); */
            box-shadow: 5px 10px 4px 0px rgba(0, 0, 0, 0.25);
            border: 0.4px solid #B6B6B6;
            z-index: 0;
            animation: slideIn 2s ease-in-out;
        }

        @keyframes slideIn {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInFromLeft {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .id h2 {
            position: absolute;
            top: 30%;
            left: 3%;
            /* text-align: center; */
            padding: 0px 5px;
            width: 128px;
            height: 33px;
            border-radius: 10px;
            border: 0.2px solid #898989;
            background: #fff;
            box-shadow: 0px 8px 4px 0px rgba(74, 0, 0, 0.25);
        }

        .id2 h2 {
            position: absolute;
            top: 45%;
            left: 3%;
            padding: 3px 5px;
            font-size: 20px;
            width: 320px;
            height: 33px;
            border-radius: 10px;
            border: 0.2px solid #898989;
            background: #fff;
            box-shadow: 0px 8px 4px 0px rgba(74, 0, 0, 0.25);
        }

        .id3 h2 {
            position: absolute;
            top: 60%;
            left: 3%;
            /* text-align: center; */
            font-size: 20px;
            width: 320px;
            height: 33px;
            padding: 3px 5px;
            border-radius: 10px;
            border: 0.2px solid #898989;
            background: #fff;
            box-shadow: 0px 8px 4px 0px rgba(74, 0, 0, 0.25);
        }

        .id4 h2 {
            position: absolute;
            top: 75%;
            left: 3%;
            /* text-align: center; */
            font-size: 20px;
            width: 320px;
            height: 33px;
            padding: 3px 5px;
            border-radius: 10px;
            border: 0.2px solid #898989;
            background: #fff;
            box-shadow: 0px 8px 4px 0px rgba(74, 0, 0, 0.25);
        }

        .id12 h4 {
            position: absolute;
            top: 88%;
            left: 3%;
            /* text-align: center; */
            font-size: 12px;
            width: 150px;
            height: 22px;
            padding: 2px 15px;
            border-radius: 50px;
            border: 0.2px solid #6a6a6a;
            background: #D9D9D9;
            box-shadow: 8px 8px 4px 4px rgba(74, 0, 0, 0.25);
        }

        .link1 a {
            position: absolute;
            top: 80%;
            left: 2%;
            width: 60px;
            height: 58px;
            border-radius: 12px;
            border: 1px solid #DCDCDC;
            background: #FFF;
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
            background: rgba(217, 217, 217, 0.50);
            box-shadow: 5px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .link3 img {
            background: lightgray 50% / cover no-repeat;
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
            color: #fff;
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

        @keyframes slideIn {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInFromLeft {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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
                bottom: 40%;
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
                z-index: 5;
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

        @media (max-width: 560px) {}
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
                <a href="review.html">Review</a>
                <a href="updates.html">Updates</a>
                <a href="#">Developer.</a>
                <a href="userlogout.php">Log Out</a>
            </div>
            <label for="check">
                <i class="fas fa-bars menu-btn"></i>
                <i class="fas fa-times close-btn"></i>
            </label>
        </header>
        <div class="container">
            <div class="id">
                <h2>Id -
                    <?php echo htmlspecialchars($serialNo); ?>
                </h2>
                <div class="id2">
                    <h2>Name -
                        <?php echo htmlspecialchars($name); ?>
                    </h2>
                </div>
                <div class="id3">
                    <h2>Email -
                        <?php echo htmlspecialchars($email); ?>
                    </h2>
                </div>
                <div class="id4">
                    <h2>Total Points -
                        <?php echo htmlspecialchars($totalPoints); ?>
                    </h2>
                </div>
                <div class="id12">
                    <h4>point expires 30th</h4>
                </div>
            </div>
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
            <p>Sumit Srivastava</p>
            <p>version - 2.1.1</p>
    </section>
</body>

</html>