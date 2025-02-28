<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booking_system";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch balance
$user_id = $_SESSION['user_id'];
$sql = "SELECT balance.amount FROM balance INNER JOIN users ON balance.user_id = users.user_id 
WHERE users.user_id = $user_id";
$result = $conn->query($sql);

$balance = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $balance = $row["amount"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance - TripMaker</title>
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

        .balance {
            font-size: 36px;
            font-weight: 600;
            color: #27ae60;
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            border: none;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .button.secondary {
            background-color: #95a5a6;
        }

        .button.secondary:hover {
            background-color: #7f8c8d;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Balance</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        <div class="balance">
            $<?php echo number_format($balance, 2); ?>
        </div>
        <form action="add.php" method="get">
            <button type="submit" class="button">Add Balance</button>
        </form>
        <button onclick="window.location.href='../dashboard.php'" class="button secondary">Back to Dashboard</button>
    </div>
</body>
</html>

