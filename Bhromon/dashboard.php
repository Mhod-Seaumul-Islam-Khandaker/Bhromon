<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_name'])) {
    header("Location: accounts/user_li.php");
    exit();
}

// Get the user's name from the session
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TripMaker</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header styles */
        header {
            background-color: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 2rem;
        }

        h1 span {
            font-weight: 600;
        }

        /* Main content styles */
        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .booking {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .booking:hover {
            background-color: #27ae60;
        }

        /* Top links styles */
        .top-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            background-color: #ecf0f1;
        }

        .top-links a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .top-links a:hover {
            color: #2980b9;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            p {
                font-size: 1rem;
            }

            .booking {
                padding: 10px 20px;
            }

            .top-links {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <span><?php echo htmlspecialchars($user_name); ?></span>!</h1>
    </header>
    <main>
        <p>You have successfully logged in. This is your dashboard.</p>
        <a href="bookings/main.php" class="booking">Book Now</a>
    </main>
    <div class="top-links">
        <a href="save_location/index.php">Wish list</a>
        <a href="payment/home.php">Balance</a>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>


