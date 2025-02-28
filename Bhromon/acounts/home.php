<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Sign Up - TripMaker</title>
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

        .management {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .management strong {
            display: block;
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .management a {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            width: 100%;
            font-size: 16px;
        }

        .management a:hover {
            background-color: #2980b9;
        }

        @media (max-width: 480px) {
            .management {
                padding: 20px;
            }

            .management strong {
                font-size: 20px;
            }

            .management a {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="management">
        <strong>Sign up or login</strong>
        <a href="user_su.php" role="link">User Sign Up</a>
        <a href="user_li.php" role="link">User Login</a>
        <a href="admin_li.php" role="link">Admin Login</a>
    </div>
</body>
</html>