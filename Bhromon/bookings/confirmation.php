<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    // Redirect to booking form if user is not logged in or booking details are not available
    header("Location: user_li.php");
    exit();
}

$booking_details = $_SESSION['booking_details'];

// Fetch location name
$conn = new mysqli('localhost', 'root', '', 'booking_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$stmt = $conn->prepare("SELECT name FROM Location WHERE location_id = ?");
$stmt->bind_param("i", $booking_details['location_id']);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();
$location_name = htmlspecialchars($location['name']);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - TripMaker</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
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
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .booking-details {
            margin-bottom: 30px;
        }

        .booking-details p {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .booking-details strong {
            font-weight: 600;
            color: #34495e;
        }

        .total-price {
            font-size: 1.2em;
            color: #27ae60;
            font-weight: 600;
        }

        form {
            margin-bottom: 20px;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .booking-details p {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Booking Confirmation</h1>

        <div class="booking-details">
            <p>
                <strong>Location:</strong>
                <span><?php echo $location_name; ?></span>
            </p>
            <p>
                <strong>Number of Seats:</strong>
                <span><?php echo $booking_details['number_of_seats']; ?></span>
            </p>
            <p>
                <strong>Booking Date:</strong>
                <span><?php echo $booking_details['booking_date']; ?></span>
            </p>
            <p>
                <strong>Duration (days):</strong>
                <span><?php echo $booking_details['duration']; ?></span>
            </p>
            <p class="total-price">
                <strong>Total Price:</strong>
                <span>$<?php echo number_format($booking_details['total_price'], 2); ?></span>
            </p>
        </div>

        <form method="post" action="process_booking.php">
            <button type="submit" name="confirm_booking">Confirm Booking</button>
        </form>
        
        <a href="main.php" class="back-link">Back to Main Page</a>
    </div>
</body>
</html>



