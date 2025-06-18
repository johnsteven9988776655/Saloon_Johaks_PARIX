<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "salon_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $service = isset($_POST['service']) ? $conn->real_escape_string($_POST['service']) : '';
    $stylist = isset($_POST['stylist']) ? $conn->real_escape_string($_POST['stylist']) : '';
    $date = isset($_POST['date']) ? $conn->real_escape_string($_POST['date']) : '';
    $time = isset($_POST['time']) ? $conn->real_escape_string($_POST['time']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    $notes = isset($_POST['notes']) ? $conn->real_escape_string($_POST['notes']) : '';
    
    // Validate required fields
    if (empty($service) || empty($stylist) || empty($date) || empty($time) || empty($name) || empty($email) || empty($phone)) {
        die("Error: All required fields must be filled.");
    }
    
    // Check if the time slot is already booked
    $check_sql = "SELECT id FROM bookings WHERE booking_date = ? AND booking_time = ? AND stylist = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("sss", $date, $time, $stylist);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        die("Error: This time slot is already booked. Please choose another time.");
    }
    
    // Insert booking into database
    $sql = "INSERT INTO bookings (service, stylist, booking_date, booking_time, price, customer_name, customer_email, customer_phone, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdssss", $service, $stylist, $date, $time, $price, $name, $email, $phone, $notes);
    
    if ($stmt->execute()) {
        // Send confirmation email (in a real application)
        // $to = $email;
        // $subject = "Your Salon Booking Confirmation";
        // $message = "Dear $name,\n\nYour booking for $service with $stylist on $date at $time has been confirmed.\n\nTotal: $$price\n\nThank you for choosing our salon!";
        // $headers = "From: bookings@glamoursalon.com";
        // mail($to, $subject, $message, $headers);
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Booking confirmed!',
            'booking_id' => $stmt->insert_id,
            'details' => [
                'service' => $service,
                'stylist' => $stylist,
                'date' => $date,
                'time' => $time,
                'price' => $price,
                'name' => $name
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $sql . '<br>' . $conn->error
        ]);
    }
    
    $stmt->close();
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    // Handle AJAX requests for checking availability
    if ($_GET['action'] == 'check_availability') {
        $date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';
        $stylist = isset($_GET['stylist']) ? $conn->real_escape_string($_GET['stylist']) : '';
        
        if (empty($date)) {
            die(json_encode(['status' => 'error', 'message' => 'Date parameter is required']));
        }
        
        $sql = "SELECT booking_time FROM bookings WHERE booking_date = ?";
        if (!empty($stylist) && $stylist != 'no-preference') {
            $sql .= " AND stylist = ?";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($stylist) && $stylist != 'no-preference') {
            $stmt->bind_param("ss", $date, $stylist);
        } else {
            $stmt->bind_param("s", $date);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $booked_times = [];
        while ($row = $result->fetch_assoc()) {
            $booked_times[] = $row['booking_time'];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'booked_times' => $booked_times
        ]);
        
        $stmt->close();
    }
}

$conn->close();
?>