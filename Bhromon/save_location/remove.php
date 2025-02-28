<?php
session_start();
// Database connection
$host = 'localhost';
$dbname = 'booking_system';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION["user_id"];
    $locationName = $conn->real_escape_string($_POST['location_name']);
    
    // Get the location_id from the Location table
    $query = "SELECT location_id FROM Location WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $locationName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $location = $result->fetch_assoc();
        $locationId = $location['location_id'];
        
        // Remove the location from save_location table
        $query = "DELETE FROM save_location WHERE user_id = ? AND location_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $locationId);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Location removed successfully!";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
    } else {
        $_SESSION['error'] = "Location not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Location</title>
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

        h2 {
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
            background-color: #e74c3c;
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
            background-color: #c0392b;
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
        <h2>Remove Location</h2>
        <form method="POST" action="remove.php">
            <div class="form-group">
                <label for="location_name">Location Name:</label>
                <input type="text" name="location_name" id="location_name" required>
            </div>
            <button type="submit">Remove Location</button>
        </form>
        <a href="index.php" class="back-link">Back to Locations</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>