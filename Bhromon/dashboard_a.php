<?php
session_start();
if (!isset($_SESSION['administrator_id'])) {
    header("Location: admin_li.php");
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: acounts/home.php");
    exit();
}

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booking_system';

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $delete_sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard_a.php");
    exit();
}

// Handle user edit
if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $update_sql = "UPDATE users SET name = ?, email_address = ?, phone_number = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard_a.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Management</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #3498db;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-edit {
            background-color: #2ecc71;
            color: #fff;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-back {
            background-color: #3498db;
            color: #fff;
        }

        .btn-logout {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
        }

        input[type="text"],
        input[type="email"] {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            margin-top: 10px;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-buttons">
            <a href="dashboard_a.php" class="btn btn-back">Back to Dashboard</a>
            <form method="post" style="display: inline;">
                <button type="submit" name="logout" class="btn btn-logout">Logout</button>
            </form>
        </div>
        <h1>User Management</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email_address"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["phone_number"]) . "</td>";
                        echo "<td>
                                <button class='btn btn-edit' onclick='openEditModal(" . 
                                    htmlspecialchars($row["user_id"]) . ", \"" . 
                                    htmlspecialchars($row["name"]) . "\", \"" . 
                                    htmlspecialchars($row["email_address"]) . "\", \"" . 
                                    htmlspecialchars($row["phone_number"]) . "\")'>Edit</button>
                                <form method='post' style='display:inline;'>
                                    <input type='hidden' name='user_id' value='" . htmlspecialchars($row["user_id"]) . "'>
                                    <input type='submit' name='delete_user' value='Delete' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this user?\");'>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit User</h2>
            <form method="post">
                <input type="hidden" id="edit_user_id" name="user_id">
                <label for="edit_name">Name:</label>
                <input type="text" id="edit_name" name="name" required>
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
                <label for="edit_phone">Phone:</label>
                <input type="text" id="edit_phone" name="phone" required>
                <input type="submit" name="edit_user" value="Save Changes">
            </form>
        </div>
    </div>

    <script>
        function openEditModal(userId, name, email, phone) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
