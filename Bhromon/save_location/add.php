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

$message = '';
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION["user_id"])) {
        $error = "User not logged in. Please log in to save locations.";
    } else {
        $userId = $_SESSION["user_id"];
        $locationName = $conn->real_escape_string($_POST['location_name']);

        // Get the location_id from the Location table
        $query = "SELECT location_id FROM Location WHERE name = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            $error = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("s", $locationName);
            if (!$stmt->execute()) {
                $error = "Error executing statement: " . $stmt->error;
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $location = $result->fetch_assoc();
                    $locationId = $location['location_id'];

                    // Check if the location is already saved for this user
                    $checkQuery = "SELECT * FROM save_location WHERE user_id = ? AND location_id = ?";
                    $checkStmt = $conn->prepare($checkQuery);
                    if ($checkStmt === false) {
                        $error = "Error preparing check statement: " . $conn->error;
                    } else {
                        $checkStmt->bind_param("ii", $userId, $locationId);
                        if (!$checkStmt->execute()) {
                            $error = "Error executing check statement: " . $checkStmt->error;
                        } else {
                            $checkResult = $checkStmt->get_result();
                            if ($checkResult->num_rows > 0) {
                                $error = "This location is already saved for your account.";
                            } else {
                                // Insert into the save_location table
                                $insertQuery = "INSERT INTO save_location (user_id, location_id) VALUES (?, ?)";
                                $insertStmt = $conn->prepare($insertQuery);
                                if ($insertStmt === false) {
                                    $error = "Error preparing insert statement: " . $conn->error;
                                } else {
                                    $insertStmt->bind_param("ii", $userId, $locationId);
                                    if (!$insertStmt->execute()) {
                                        $error = "Error saving location: " . $insertStmt->error;
                                    } else {
                                        $message = "Location '{$locationName}' saved successfully!";
                                    }
                                    $insertStmt->close();
                                }
                            }
                            $checkStmt->close();
                        }
                    }
                } else {
                    $error = "Location '{$locationName}' not found. Please check the spelling and try again.";
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Location - TripMaker</title>
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
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #34495e;
        }

        input[type="text"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #3498db;
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Save Location</h2>
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="add.php">
            <label for="location_name">Location Name:</label>
            <input type="text" name="location_name" id="location_name" required>
            <button type="submit">Save Location</button>
        </form>
        <a href="index.php" class="back-link">Back to Saved Locations</a>
    </div>
</body>
</html>




