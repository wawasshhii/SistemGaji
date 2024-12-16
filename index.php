<?php
session_start();
if (!isset($_SESSION['karyawan_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

include 'config.php';
$karyawan_id = $_SESSION['karyawan_id'];
$nama_karyawan = $_SESSION['nama']; // Ambil nama karyawan dari sesi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses upload foto absensi
    if (isset($_FILES['foto_absen']) && $_FILES['foto_absen']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["foto_absen"]["name"]);
        move_uploaded_file($_FILES["foto_absen"]["tmp_name"], $target_file);
        // Simpan ke database
        $status = $_POST['status'];
        $tanggal = date('Y-m-d'); // Tanggal hari ini

        // Cek apakah sudah ada absensi hari ini
        $check_sql = "SELECT * FROM absensi WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'";
        if ($conn->query($check_sql)->num_rows > 0) {
            echo "Anda sudah mencatat absensi hari ini.";
        } else {
            // Simpan data absensi ke database
            $absen_sql = "INSERT INTO absensi (karyawan_id, tanggal, status, foto, nama) VALUES ('$karyawan_id', '$tanggal', '$status', '$target_file', '$nama_karyawan')";
            if ($conn->query($absen_sql) === TRUE) {
                echo "Absensi berhasil dicatat!";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>Dashboard User</title>
   <style>
       /* Reset CSS */
       * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
       }

       /* Gaya Umum */
       body {
           font-family: Arial, sans-serif;
           background-color: #f4f4f4;
           color: #333;
           line-height: 1.6;
           display: flex;
           flex-direction: column;
           align-items: center;
           padding: 20px;
       }

       h1, h2, h3 {
           margin-bottom: 20px;
       }

       /* Gaya Form */
       form {
           background: #ffffff;
           padding: 20px;
           border-radius: 5px;
           box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           width: 500px; /* Lebar form */
           margin-bottom: 20px; /* Jarak bawah form */
       }

       form label {
           display: block;
           margin-bottom: 5px;
       }

       form select,
       form input[type="file"],
       form input[type="submit"] {
           width: 100%;
           padding: 10px;
           margin: 10px 0;
           border: 1px solid #ccc;
           border-radius: 5px;
       }

       form input[type="submit"] {
           background: #16a085;
           color: #ffffff;
           border: none;
           cursor: pointer;
       }

       form input[type="submit"]:hover {
           background: rgb(22, 48, 46);
       }

       /* Gaya Tombol */
       .button-container {
           display: flex;
           justify-content: space-between;
           height: 50px;
           width: 300px; /* Sesuaikan dengan lebar form */
           margin-top: 20px; /* Jarak atas tombol */
       }

       .button-container a {
           display: block;
           width: 40%; /* Lebar tombol */
           text-align: center;
           padding: 10px;
           background: #16a085; /* Warna tombol */
           color: #ffffff; /* Teks putih pada tombol */
           border-radius: 5px; /* Sudut melengkung pada tombol */
           text-decoration: none; /* Tanpa garis bawah pada link tombol */
           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Bayangan pada tombol */
           transition: all 0.3s ease; /* Transisi saat hover */
       }

       .button-container a:hover {
           background: #rgb(22, 48, 46); /* Warna lebih gelap saat hover */
           transform: translateY(-2px); /* Efek mengangkat saat hover */
           box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Bayangan lebih dalam saat hover */
       }

       .button-container a:active {
           transform: translateY(0); /* Kembali ke posisi semula saat diklik */
           box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Bayangan normal saat diklik */
       }
   </style>
</head>
<body>

<h1>Absensi Karyawan SIPEKA</h1>
<h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>

<h2>Catat Absensi Anda Hari Ini</h2>
<p>Tanggal <?php echo date('d-m-Y'); ?></p> <!-- Menampilkan tanggal hari ini -->

<form method="post" enctype="multipart/form-data">
   <label for="status">Status:</label>
   <select name="status" required>
       <option value="Hadir">Hadir</option>
       <option value="Sakit">Sakit</option>
       <option value="Izin">Izin</option>
   </select>

   <label for="foto_absen">Foto Lokasi Kerja:</label>
   <input type="file" name="foto_absen" accept="image/*" required><br>

   <input type="submit" value="Catat Absensi">
</form>

<div class="button-container">
   <a href='laporan_absen.php'>Rekap</a>
   <a href='logout.php'>Logout</a>
</div>

</body>
</html>
