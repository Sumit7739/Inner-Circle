<?php
session_start();

// Check if the form was submitted
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Establish a database connection
    $servername = "localhost";
    $username = "root";
    $dbPassword = "";
    $dbname = "chaijn";

    $conn = new mysqli($servername, $username, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute a SQL query to retrieve user data
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, check the password
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        $verificationStatus = $row['verification_status'];

        if ($verificationStatus == 0) {

            // Redirect to a particular page when verification_status is 0
            header('Location: otp_verification.php?email=' . $email);
            exit();
        }

        if ($password === $storedPassword) {
            // Password is correct, login successful
            $_SESSION['id'] = $row['id'];
            $stmt->close();
            $conn->close();
            header("Location: profile.php"); // Redirect to the success page
            exit();
        } else {
            // Invalid password
            $error = "Incorrect password";
        }
    } else {
        // User not found
        $error = "User not found";
    }

    $stmt->close(); // Close the statement
    $conn->close(); // Close the database connection
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/icon.ico" type="image/x-icon" sizes="32x32">
    <title>Login</title>
    <style>
        .error-msg {
            position: relative;
            color: red;
            left: 32%;
            margin-top: 10px;
            /* margin-bottom: 20px; */
            font-size: 20px;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        header img {
            width: 250px;
        }

        section {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
            background: url(image/bg1.jpg);
            background-size: cover;
            background-position: center;
            animation: animateBg 5s linear infinite;
        }

        @keyframes animateBg {
            100% {
                filter: hue-rotate(360deg);
            }
        }

        .login-box {
            position: absolute;
            top: 10%;
            width: 98%;
            height: 550px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .5);
            border-radius: 20px;
            display: flex;
            padding: 55px 55px;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(15px);
        }

        h2 {
            font-size: 2em;
            color: #fff;
            text-align: center;
            margin-bottom: 50px;
        }

        .input-box {
            position: relative;
            width: 350px;
            margin: 30px 0;
            border-bottom: 2px solid #fff;
            margin-top: 30px;
        }

        .input-box label {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            font-size: 1em;
            color: #fff;
            pointer-events: none;
            transition: .5s;
        }

        .input-box input:focus~label,
        .input-box input:valid~label {
            top: -5px;
        }

        .input-box input {
            width: 100%;
            height: 50px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1em;
            color: #fff;
            padding: 0 35px 0 5px;
        }

        .input-box .icon {
            position: absolute;
            right: 8px;
            color: #fff;
            font-size: 1.2;
            line-height: 57px;
        }

        .checkbox {
            color: #fff;
            font-weight: 400;
        }

        .forpass a {
            position: absolute;
            text-decoration: none;
            color: #fff;
            margin-top: 10px;
            left: 60%;
        }

        button {
            width: 100%;
            height: 40px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 1em;
            color: #000;
            font-weight: 500;
            margin-top: 60px;
            margin-bottom: 10px;
        }

        .log h4 {
            margin-top: 30px;
            color: white;
            font-size: 14px;
            font-weight: 400;
        }

        .log a {
            /* text-decoration: none; */
            color: #fff;
            font-size: 14px;
            margin-left: 80px;
        }

        @media(max-width:360px) {
            .login-box {
                width: 100vh;
                height: 100vh;
                border: none;
                border-radius: 0;
            }

            .input-box {
                width: 390px;
            }
        }

        .nav .h2 {
            font-size: 2em;
            color: #fff;
            text-align: left;
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
                <h2>User Login</h2>
                <?php if (isset($error)): ?>
                    <p class="error-msg">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>
                <div class="input-box">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Enter Your Email</label>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" required maxlength="8">
                    <label for="password">Enter Your Password</label>
                </div>
                <div class="checkbox">
                    <input type="checkbox"> Remember Me.
                </div>
                <div class="forpass">
                    <a href="send_otp.php">forgot password?</a>
                </div>
                <button type="submit" name="submit">Login</button>
                <div class="log">
                    <h4>Don't have an account?
                        <a href="signup.php">Create Account</a>
                    </h4>
                </div>
            </form>
        </div>
    </section>
</body>

</html>