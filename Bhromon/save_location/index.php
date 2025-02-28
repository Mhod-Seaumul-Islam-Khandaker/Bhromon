<?php
session_start();
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "booking_system";
$conn = new mysqli($host, $user, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Ensure the user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    header("Location: ../accounts/user_login.php");
    exit();
}
$username = $_SESSION['user_name'];
// Fetch all locations
$sql = "SELECT l.name, l.location_id
        FROM save_location sl
        JOIN location l ON sl.location_id = l.location_id
        WHERE sl.user_id = '" . $conn->real_escape_string($_SESSION['user_id']) . "'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Locations</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
        }
        h1 {
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #ecf0f1;
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .empty-state {
            text-align: center;
            color: #7f8c8d;
            margin-top: 20px;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn-dashboard {
            background-color: #2ecc71;
        }
        .btn-dashboard:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Your Saved Locations</h1>
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <?php if ($result->num_rows == 0): ?>
            <div class="empty-state">
                <h3>You haven't saved any locations yet.</h3>
                <p>Click the "Add Location" button below to get started!</p>
            </div>
        <?php else: ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <?php echo htmlspecialchars($row['name']); ?>
                        <form action="remove.php" method="post" style="display: inline;">
                            <input type="hidden" name="location_id" value="<?php echo $row['location_id']; ?>">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
        <div class="button-group">
            <a href="add.php" class="btn">Add Location</a>
            <a href="../dashboard.php" class="btn btn-dashboard">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>
<?php
// Close connection
$conn->close();
?>