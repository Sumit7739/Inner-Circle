<?php


// Database Connection Details
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

// Fetch reviews from the database along with user names
$sql = "SELECT r.user_id, r.review_text, r.rating, r.review_date, u.name 
        FROM reviews r
        INNER JOIN users u ON r.user_id = u.id
        ORDER BY r.review_date DESC";
$result = $conn->query($sql);

// Initialize an empty array to store reviews
$reviews = [];

if ($result->num_rows > 0) {
    // Fetch reviews and store them in the $reviews array
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
} else {
    echo "No reviews found.";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
    <style>
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

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .review-container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .review {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        .review h3 {
            margin-bottom: 10px;
        }

        .review .date {
            color: #555;
        }

        .rating {
            color: #ffcc00;
            margin-bottom: 10px;
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
    <div class="review-container">
        <?php foreach ($reviews as $review): ?>
            <div class="review">
                <h2>
                    <?php echo $review['name']; ?>
                </h2>
                <h3>User ID:
                    <?php echo $review['user_id']; ?>
                </h3>
                <p>
                    <?php echo htmlspecialchars($review['review_text']); ?>
                </p>
                <div class="rating">Rating:
                    <?php echo $review['rating']; ?>
                </div>
                <div class="date">Date:
                    <?php echo $review['review_date']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>