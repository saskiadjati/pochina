<?php
// Memulai session untuk menangani login
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

// Koneksi ke database untuk mengambil data pengguna
$conn = mysqli_connect('localhost', 'root', '', 'pochina');
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

// Ambil email berdasarkan user_id yang terlogin
$sql = "SELECT email FROM pelanggan WHERE id = $user_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $user_email = $row['email'];  // Ambil email dari database
} else {
    $user_email = ''; // Jika email tidak ditemukan, kosongkan
}

mysqli_close($conn); // Tutup koneksi database

// Default values untuk penerbangan (dapat disesuaikan atau divalidasi)
$departure_city = isset($_GET['departure_city']) ? $_GET['departure_city'] : "Jakarta";
$destination_city = isset($_GET['destination_city']) ? $_GET['destination_city'] : "Bali";
$ticket_type = isset($_GET['ticket_type']) ? $_GET['ticket_type'] : "reguler";
$price = isset($_GET['price']) ? $_GET['price'] : 1000000; // Default price
$maskapai = isset($_GET['airline']) ? $_GET['airline'] : '';

// Waktu penerbangan (pastikan format yang benar)
$flight_time = isset($_GET['flight_time']) ? $_GET['flight_time'] : '2025-02-21 14:00:00';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - Pochina Airplane</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

       /* Sosial Media */
        .medsos {
            background-color: #FBB4A5;
            padding: 10px 0;
            display: flex;
            justify-content: space-between; /* Added space between the elements */
            align-items: center; /* Center items vertically */
        }
        .medsos ul {
            display: flex;
            justify-content: left;
            gap: 30px;
        }
        .medsos ul li {
            display: inline-block;
        }
        .medsos ul li a {
            font-size: 18px;
            color: black;
            transition: transform 0.3s ease;
        }
        .medsos ul li a:hover {
            transform: scale(1.2);
        
        }
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #FB9EC6;
            margin-bottom: 30px;
            margin-top: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #8B0000;
            text-align: left;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .save-button {
            background: linear-gradient(135deg, #FBB4A5, #FFE893);
            color: black;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            width: 100%;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px; 
        }

        .save-button:hover {
            background: linear-gradient(135deg, #FFE893, #FBB4A5); /* Ubah arah gradien saat hover */
            transform: scale(1.05); /
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .save-button:active {
            transform: scale(1); /* Mengembalikan ukuran tombol ke normal saat ditekan */
        }

        footer {
            background-color: #FBB4A5;
            color: black;
            padding: 20px 0;
            text-align: center;
            margin-top: 100px;
        }

        footer small {
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- Sosial Media Links -->
<div class="medsos">
        <div class="container">
            <ul>
                <li><a href="https://www.bki.co.id/halamanstatis-63.html#"><i class="fa-solid fa-globe"></i></a></li>
                <li><a href="https://www.instagram.com/bki_untukindonesia?igsh=NjlzZWFlZmp5NHVp"><i class="fa-brands fa-instagram"></i></a></li>
                <li><a href="https://youtube.com/@bki1964?si=tsr_YWtY18qhGWgT"><i class="fa-brands fa-youtube"></i></a></li>
                <li><a href="flight.php" class="back-logo"><i class="fa fa-arrow-left" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </div>

<h1>Booking Form - Pochina Airplane</h1>

    <div class="form-container">
        <form action="process_booking.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nama Lengkap:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="phone">Nomor Telepon:</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
        <div class="form-group">
            <label for="maskapai">Maskapai:</label>
            <input type="text" name="maskapai" value="<?php echo htmlspecialchars($maskapai); ?>" readonly required>
        </div>

        <div class="form-group">
            <label for="asal">Kota Asal:</label>
            <input type="text" name="asal" value="<?php echo $departure_city; ?>" readonly required>
        </div>

        <div class="form-group">
            <label for="tujuan">Kota Tujuan:</label>
            <input type="text" name="tujuan" value="<?php echo $destination_city; ?>" readonly required>
        </div>

        <div class="form-group">
            <label for="waktu">Jam Penerbangan:</label>
            <input type="text" name="waktu" value="<?php echo $flight_time; ?>" readonly required>
        </div>

        <div class="form-group">
            <label for="visa">Nomor Visa:</label>
            <input type="text" name="visa" required>
        </div>

        <div class="form-group">
            <label for="flight_type">Jenis Tiket:</label>
            <select name="flight_type" required>
                <option value="reguler" <?php echo ($ticket_type == 'reguler') ? 'selected' : ''; ?>>Reguler</option>
                <option value="vip" <?php echo ($ticket_type == 'vip') ? 'selected' : ''; ?>>VIP</option>
            </select>
        </div>

        <div class="form-group">
            <label for="total-price">Total Harga:</label>
            <input id="total-price" type="text" value="IDR <?php echo number_format($price, 0, ',', '.'); ?>" readonly />
            <input type="hidden" name="total_price" id="total-price-hidden" value="<?php echo $price; ?>" />
        </div>

        <div class="form-group">
            <label for="seat_number">Pilih Kursi:</label>
            <select name="seat_number" required>
                <option value="A1">A1</option>
                <option value="A2">A2</option>
                <option value="B1">B1</option>
                <option value="B2">B2</option>
            </select>
        </div>

        <div class="form-group">
            <label for="payment_method">Metode Pembayaran:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="" disabled selected>Pilih Metode Pembayaran</option>
                <option value="BCA">BCA</option>
                <option value="BNI">BNI</option>
                <option value="BRI">BRI</option>
                <option value="Mandiri">Mandiri</option>
            </select>
        </div>

        <div class="form-group" id="account-number-field" style="display: none;">
            <label for="account_number">Nomor Rekening:</label>
            <input type="text" id="account_number" name="account_number" readonly>
        </div>

        <div class="form-group" id="payment-proof-field" style="display: none;">
            <label for="payment_proof">Upload Bukti Pembayaran:</label>
            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf">
        </div>

        <button type="submit" id="save-button" class="save-button">
            Success
        </button>
    </form>

    <!-- footer -->
    <footer>
        <div class="container">
            <small> Copyright &copy; 2025 - Pochina Airplane, All Right Reserved.</small>
        </div>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var flightTypeSelect = document.querySelector('select[name="flight_type"]');
        var totalPriceInput = document.getElementById('total-price');
        var price = <?php echo $price; ?>;

        function updatePrice() {
            // Set price based on flight type selection
            totalPriceInput.value = "IDR " + price.toLocaleString();
            document.getElementById('total-price-hidden').value = price;
        }

        flightTypeSelect.addEventListener('change', updatePrice);

        // Initial price update
        updatePrice();

        var paymentMethodSelect = document.getElementById('payment_method');
        var accountNumberField = document.getElementById('account-number-field');
        var paymentProofField = document.getElementById('payment-proof-field');

        paymentMethodSelect.addEventListener('change', function () {
            var selectedPaymentMethod = paymentMethodSelect.value;

            if (selectedPaymentMethod === 'BCA' || selectedPaymentMethod === 'BNI' || selectedPaymentMethod === 'BRI' || selectedPaymentMethod === 'Mandiri') {
                accountNumberField.style.display = 'block';
                paymentProofField.style.display = 'block';

                if (selectedPaymentMethod === 'BCA') {
                    document.getElementById('account_number').value = '123-456-7890';
                } else if (selectedPaymentMethod === 'BNI') {
                    document.getElementById('account_number').value = '987-654-3210';
                } else if (selectedPaymentMethod === 'BRI') {
                    document.getElementById('account_number').value = '112-233-4455';
                } else if (selectedPaymentMethod === 'Mandiri') {
                    document.getElementById('account_number').value = '555-666-7777';
                }
            } else {
                accountNumberField.style.display = 'none';
                paymentProofField.style.display = 'none';
            }
        });

        if (paymentMethodSelect.value) {
            paymentMethodSelect.dispatchEvent(new Event('change'));
        }
    });
</script>

</body>
</html>
