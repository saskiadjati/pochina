<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'pochina');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$airline = $_POST['maskapai'];
$departure_city = $_POST['asal'];
$destination_city = $_POST['tujuan'];
$flight_time = $_POST['waktu']; // In 'YYYY-MM-DD HH:MM' format
$visa = $_POST['visa'];
$ticket_type = $_POST['flight_type'];
$total_price = $_POST['total_price'];
$seat_number = $_POST['seat_number'];
$payment_method = $_POST['payment_method'];
$account_number = $_POST['account_number'];

// Handle payment proof upload (optional)
if ($_FILES['payment_proof']['name']) {
    $payment_proof = $_FILES['payment_proof']['name'];
    $upload_dir = "uploads/";
    $upload_file = $upload_dir . basename($payment_proof);
    move_uploaded_file($_FILES['payment_proof']['tmp_name'], $upload_file);
} else {
    $payment_proof = null;
}

// Insert data into the database
$sql = "INSERT INTO riwayat (name, phone, email, airline, departure_city, destination_city, flight_time, visa, ticket_type, total_price, seat_number, payment_method, account_number, payment_proof)
        VALUES ('$name', '$phone', '$email', '$airline', '$departure_city', '$destination_city', '$flight_time', '$visa', '$ticket_type', '$total_price', '$seat_number', '$payment_method', '$account_number', '$payment_proof')";

if (mysqli_query($conn, $sql)) {
    // Redirect to riwayat.php after successful insertion
    header("Location: riwayat.php");
    exit;
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
