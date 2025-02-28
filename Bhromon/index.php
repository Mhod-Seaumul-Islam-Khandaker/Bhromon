<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Maker</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        /* Typography */
        .head_text {
            font-size: 3rem;
            color: #2c3e50;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-out;
        }

        .subheading {
            font-size: 1.2rem;
            color: #34495e;
            font-weight: 300;
            display: block;
            margin-top: 10px;
        }

        /* Button styles */
        .cta-button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 30px;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .cta-button:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .head_text {
                font-size: 2.5rem;
            }

            .subheading {
                font-size: 1rem;
            }

            .container {
                padding: 30px;
            }
        }

        @media (max-width: 480px) {
            .head_text {
                font-size: 2rem;
            }

            .cta-button {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="head_text">
            Bhromon <br />
            <span class="subheading">Solution for your entertainment and adventure</span>
        </h1>
        <a href="acounts/home.php" class="cta-button">Login or signup</a>
    </div>
</body>
</html>

