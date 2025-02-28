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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $userId = $_SESSION['user_id'];

    if ($amount < 0) {
        $_SESSION['error'] = "Negative balance cannot be added.";
        header('Location: home.php');
        exit();
    }

    // Check if the user already has a balance entry
    $checkQuery = "SELECT * FROM balance WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('i', $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    // If no entry exists, insert a new row for the user
    if ($result->num_rows == 0) {
        $insertQuery = "INSERT INTO balance (user_id, amount) VALUES (?, 0)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param('i', $userId);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $checkStmt->close();

    if (!empty($amount) && is_numeric($amount)) {
        $query = "UPDATE balance SET amount = amount + ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('di', $amount, $userId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Balance updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update balance.";
        }
    } else {
        $_SESSION['error'] = "Invalid amount.";
    }
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Balance</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin-top: 20px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #34495e;
            font-weight: 600;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 20px;
        }

        button {
            background-color: #3498db;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: inline-block;
            color: #3498db;
            text-decoration: none;
            margin-top: 20px;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #2980b9;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            width: 100%;
            max-width: 500px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']);
    }
    ?>
    <div class="container">
        <h1>Add Balance</h1>
        <form action="add.php" method="post">
            <div class="form-group">
                <label for="bank_name">Bank Name:</label>
                <input type="text" id="bank_name" name="bank_name" required>
            </div>

            <div class="form-group">
                <label for="account_number">Account Number:</label>
                <input type="text" id="account_number" name="account_number" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="text" id="amount" name="amount" required>
            </div>

            <button type="submit">Add Balance</button>
        </form>
        <a href="home.php" class="back-link">Back to Balance Page</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>