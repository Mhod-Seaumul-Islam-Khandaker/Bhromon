<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    header("Location: ../account/login.php");
    exit();
}

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booking_system';

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$booking_details = $_SESSION['booking_details'];
$user_id = $_SESSION['user_id'];
$total_price = $booking_details['total_price'];

$success = false;
$error_message = '';

// Process the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Fetch current balance
        $stmt = $conn->prepare("SELECT amount FROM balance WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_balance = $result->fetch_assoc()['amount'];
        $stmt->close();

        if ($current_balance < $total_price) {
            throw new Exception("Insufficient balance.");
        }

        // Update balance
        $new_balance = $current_balance - $total_price;
        $stmt = $conn->prepare("UPDATE balance SET amount = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $new_balance, $user_id);
        $stmt->execute();
        $stmt->close();

        // Insert booking data
        $stmt = $conn->prepare("INSERT INTO Booking (user_id, location_id, number_of_seats, booking_date, duration) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisi", $user_id, $booking_details['location_id'], $booking_details['number_of_seats'], $booking_details['booking_date'], $booking_details['duration']);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Clear session after successful booking
        unset($_SESSION['booking_details']);
        $success = true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}

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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .tick-mark {
            font-size: 72px;
            color: #27ae60;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <div class="tick-mark">✓</div>
            <h1>Booking Completed</h1>
            <p>Your trip has been successfully booked and the amount has been deducted from your balance.</p>
            <a href="../dashboard.php" class="button">Return to Dashboard</a>
        <?php elseif ($error_message): ?>
            <h1>Booking Failed</h1>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <a href="main.php" class="button">Try Again</a>
        <?php else: ?>
            <h1>Confirm Booking</h1>
            <p>Total Price: $<?php echo number_format($total_price, 2); ?></p>
            <form method="post" action="">
                <button type="submit" name="confirm_booking" class="button">Confirm and Pay</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

