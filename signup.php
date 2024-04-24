<?php
session_start();
require 'C:\xampp\htdocs\ts\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\ts\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\ts\PHPMailer-master\src\Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    // $phoneNumber = $_POST['phonenumber'];
    $password = $_POST['password'];

    // Establish database connection
    $servername = "localhost";
    $username = "root";
    $dbPassword = ""; // Replace with your database password
    $dbname = "chaijn";

    $conn = new mysqli($servername, $username, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $token = bin2hex(random_bytes(16));
    // Check if the user already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User already exists
        $error = "User already exists";
    } else {

        // Insert the new user into the database
        $sql = "INSERT INTO users (name, email, password, tokenn) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $token);
        $stmt->execute();

        // Check if the user was successfully inserted
        if ($stmt->affected_rows > 0) {
            // User created successfully
            $_SESSION['id'] = $stmt->insert_id;
            $stmt->close();

            // Function to generate a random 6-digit OTP
            function generateOTP()
            {
                $otp = "";
                for ($i = 0; $i < 6; $i++) {
                    $otp .= mt_rand(0, 9);
                }
                return $otp;
            }

            // Retrieve the recipient email from the form
            $recipientEmail = $_POST['email'];

            // Generate OTP
            $otp = generateOTP();

            // Initialize PHPMailer
            $mail = new PHPMailer();

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Username = 'srisinhasumit10@gmail.com'; // Your Gmail email address
            $mail->Password = 'ggtbuofjfdmqcohr'; // Your Gmail password

            // Sender and recipient
            $mail->setFrom('your@gmail.com', 'Chai Junction'); // Sender email and name
            $mail->addAddress($recipientEmail); // Recipient email

            // Save the OTP in the database
            $sql = "UPDATE users SET otp = '$otp' WHERE email = '$recipientEmail'";

            if ($conn->query($sql) === TRUE) {
                // Send email
                $mail->isHTML(true);
                $mail->Subject = 'OTP Verification';
                $mail->Body = 'Your OTP for account verification is: ' . $otp;

                if ($mail->send()) {
                    // Redirect to OTP verification page
                    header('Location: otp_verification.php?email=' . $recipientEmail);
                    exit();
                } else {
                    $error = 'Error sending email: ' . $mail->ErrorInfo;
                }
            } else {
                $error = 'Error updating OTP: ' . $conn->error;
            }

            $conn->close();
        } else {
            $error = "Failed to create user";
        }
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
    <title>Signup</title>
    <link rel="stylesheet" href="stylesignup.css">
    <style>
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
            top: 5%;
            width: 98%;
            height: 700px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .5);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(15px);
        }

        h2 {
            font-size: 2em;
            color: #fff;
            text-align: center;
        }

        .input-box {
            position: relative;
            width: 310px;
            margin: 30px 0;
            border-bottom: 2px solid #fff;
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
            font-size: 1.2em;
            line-height: 57px;
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
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .checkbox {
            color: #fff;
        }

        .log h4 {
            color: white;
            font-size: 12px;
            font-weight: 400;
        }

        .log a {
            /* text-decoration: none; */
            color: #fff;
            font-size: 18px;
            margin-left: 80px;
        }

        #loaderOverlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 9999;
        }

        .loader {
            /* Loader styles */
            display: inline-block;
            width: 80px;
            height: 80px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 40%;
            left: 40%;
            transform: translate(-50%, -50%);
        }

        .loading {
            display: inline-block !important;
            /* Override display property */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 360px) {
            .login-box {
                width: 100vh;
                height: 100vh;
                border: none;
                border-radius: 0;
            }

            .input-box {
                width: 290px;
            }
        }

        .nav .h2 {
            font-size: 2em;
            color: #fff;
            text-align: left;
        }

        .error-msg {
            position: relative;
            color: red;
            font-size: 20px;
            margin-top: 5px;
            margin-left: 68px;
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
            <div id="loaderOverlay">
                <div id="loader" class="loader"></div>
            </div>
            <form method="POST">
                <h2>Signup</h2>
                <?php if (isset($error)) { ?>
                    <p class="error-msg">
                        <?php echo $error; ?>
                    </p>
                <?php } ?>
                <div class="input-box">
                    <input type="text" id="name" name="name" required>
                    <label for="name">Name</label>
                </div>
                <div class="input-box">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" required maxlength="8">
                    <label for="password">Password</label>
                </div>
                <div class="input-box">
                    <input type="confirm password" id="confirmpassword" name="confirm password" required maxlength="8">
                    <label for="confirmpassword">Confirm Password</label>
                </div>
                <p id="password-error" style="color: red;"></p>
                <div class="checkbox">
                    <input type="checkbox" required> I agree to the terms and condition.
                </div>
                <button type="submit" id="submit" name="signup">Signup</button>

                <div class="log">
                    <h4>Already have an account?
                        <a href="login.php">SignIn</a>
                    </h4>
                </div>
            </form>
        </div>
    </section>
    <script>
        // Get references to the password and confirm password input fields
        const passwordInput = document.getElementById("password");
        const confirmPasswordInput = document.getElementById("confirmpassword");

        // Get references to the error message element and submit button
        const passwordError = document.getElementById("password-error");
        const submitButton = document.getElementById("submit");

        // Add an input event listener to the confirm password field
        confirmPasswordInput.addEventListener("input", function () {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Compare the passwords
            if (password === confirmPassword) {
                // Passwords match, clear the error message
                passwordError.textContent = "";
                submitButton.disabled = false; // Enable the submit button
            } else {
                // Passwords don't match, display an error message
                passwordError.textContent = "Passwords do not match!";
                submitButton.disabled = true; // Disable the submit button
            }
        });

        const submit = document.getElementById('submit');
        const emailField = document.getElementById('email');
        const loaderOverlay = document.getElementById('loaderOverlay');

        submit.addEventListener('click', function () {
            const emailValue = emailField.value.trim();

            if (emailField.checkValidity()) {
                loaderOverlay.style.display = 'block'; // Show overlay

                // Simulate asynchronous task (e.g., AJAX request)
                setTimeout(function () {
                }, 2000); // Simulated delay of 2 seconds
            } else {
                emailField.reportValidity();
            }
        });


    </script>
</body>

</html>