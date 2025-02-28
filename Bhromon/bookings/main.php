<?php
session_start();

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
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: user_li.php");
    exit();
}

$errors = [];
$search_results = [];

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search)) {
    $locations_query = "SELECT * FROM Location WHERE name LIKE ?";
    $stmt = $conn->prepare($locations_query);
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Booking process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location_id = $_POST['location_id'];
    $number_of_seats = $_POST['number_of_seats'];
    $booking_date = $_POST['booking_date'];
    $duration = $_POST['duration'];
    
    // Validate inputs
    if ($duration <= 0) {
        $errors[] = "Duration must be a positive number.";
    }
    if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
        $errors[] = "Booking date cannot be in the past.";
    }
    
    if (empty($errors)) {
        // Fetch location details
        $stmt = $conn->prepare("SELECT price_per_seats, total_available_seats FROM Location WHERE location_id = ?");
        $stmt->bind_param("i", $location_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $location = $result->fetch_assoc();
        $stmt->close();

        $price_per_seat = $location['price_per_seats'];
        $available_seats = $location['total_available_seats'];
        $total_price = $price_per_seat * $number_of_seats * $duration;

        // Check seat availability
        if ($number_of_seats > $available_seats) {
            $errors[] = "Not enough seats available. Only $available_seats seats left.";
        } else {
            // Check user balance
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT amount FROM balance WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $balance = $result->fetch_assoc()['amount'];
            $stmt->close();

            if ($total_price > $balance) {
                $errors[] = "You do not have enough balance to make this booking.";
            } else {
                // Store the booking details in session for later use
                $_SESSION['booking_details'] = [
                    'location_id' => $location_id,
                    'number_of_seats' => $number_of_seats,
                    'booking_date' => $booking_date,
                    'duration' => $duration,
                    'total_price' => $total_price
                ];

                // Redirect to confirmation page
                header("Location: confirmation.php");
                exit();
            }
        }
    }
}

// Fetch user balance
$user_id = $_SESSION['user_id'];
$sql = "SELECT balance.amount FROM balance INNER JOIN users ON balance.user_id = users.user_id WHERE users.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$balance = $result->num_rows > 0 ? $result->fetch_assoc()['amount'] : 0;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - TripMaker</title>
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
            max-width: 800px;
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

        p {
            margin-bottom: 20px;
        }

        .balance {
            font-weight: bold;
            color: #27ae60;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #2980b9;
        }

        .error-list {
            color: #e74c3c;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fadbd8;
            border-radius: 4px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            width: calc(100% - 100px);
        }

        .search-form button {
            width: 90px;
        }

        .search-results {
            margin-top: 20px;
        }

        .location-item {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .location-item h3 {
            margin-bottom: 5px;
        }

        .location-item p {
            margin-bottom: 5px;
        }

        .available {
            color: #28a745;
        }

        .not-available {
            color: #dc3545;
        }

        .select-location {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .select-location:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book a Location</h1>
        <p>Please search for a location to book.</p>
        <p>
            Your current balance is 
            <span class="balance"><?php echo $balance; ?></span>
        </p>
        <?php
        if (!empty($errors)) {
            echo "<ul class='error-list'>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }
        ?>
        <form class="search-form" method="get">
            <input type="text" name="search" placeholder="Search locations..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($search_results)): ?>
            <div class="search-results">
                <h2>Search Results:</h2>
                <?php foreach ($search_results as $location): ?>
                    <div class="location-item">
                        <h3><?php echo htmlspecialchars($location['name']); ?></h3>
                        <p><?php echo htmlspecialchars($location['description']); ?></p>
                        <p>Price per seat: $<?php echo $location['price_per_seats']; ?></p>
                        <p>Available seats: <?php echo $location['total_available_seats']; ?></p>
                        <?php if ($location['total_available_seats'] > 0): ?>
                            <p class="available">Available</p>
                            <button class="select-location" onclick="selectLocation(<?php echo $location['location_id']; ?>, '<?php echo htmlspecialchars($location['name']); ?>')">Select</button>
                        <?php else: ?>
                            <p class="not-available">Not Available</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="bookingForm" method="post" action="" style="display: none;">
            <input type="hidden" id="location_id" name="location_id" required>
            <p id="selectedLocation"></p>

            <label for="number_of_seats">Number of Seats:</label>
            <input type="number" id="number_of_seats" name="number_of_seats" required>

            <label for="booking_date">Booking Date:</label>
            <input type="date" id="booking_date" name="booking_date" required>

            <label for="duration">Duration (in days):</label>
            <input type="number" id="duration" name="duration" required>

            <button type="submit">Proceed to Confirmation</button>
        </form>
        <a href="../dashboard.php" class="back-link">Back to dashboard</a>
    </div>

    <script>
        function selectLocation(locationId, locationName) {
            document.getElementById('location_id').value = locationId;
            document.getElementById('selectedLocation').textContent = 'Selected Location: ' + locationName;
            document.getElementById('bookingForm').style.display = 'block';
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>


