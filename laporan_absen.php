<?php
session_start();
if (!isset($_SESSION['karyawan_id'])) {
   header("Location: login.php");
}

include 'config.php';
$karyawan_id = $_SESSION['karyawan_id'];

$sql_absen = "SELECT * FROM absensi WHERE karyawan_id='$karyawan_id'";
$result_absen = $conn->query($sql_absen);
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>Laporan Absensi Anda</title>
   <style>
       /* Reset beberapa gaya default */
       body, h2, table, th, td {
           margin: 0;
           padding: 0;
       }

       body {
           font-family: Arial, sans-serif;
           background-color: #f4f4f4; /* Warna latar belakang */
           color: #333; /* Warna teks */
           display: flex;
           flex-direction: column;
           align-items: center; /* Pusatkan konten secara horizontal */
           padding: 20px; /* Ruang dalam halaman */
       }

       /* Gaya untuk judul */
       h2 {
           margin-bottom: 20px; /* Jarak bawah judul */
       }

       /* Gaya untuk tabel */
       table {
           width: 80%; /* Lebar tabel 80% dari lebar halaman */
           max-width: 600px; /* Maksimal lebar tabel */
           border-collapse: collapse; /* Menghilangkan jarak antara sel */
           margin-top: 20px; /* Jarak atas tabel */
           background-color: white; /* Warna latar belakang tabel */
           box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan tabel */
       }

       th, td {
           border: 1px solid #ddd; /* Garis batas sel */
           padding: 8px; /* Ruang dalam sel (lebih kecil dari sebelumnya) */
           text-align: left; /* Rata kiri teks dalam sel */
       }

       th {
           background-color: #16a085; /* Warna latar belakang header tabel */
           color: white; /* Warna teks header tabel */
       }

       tr:nth-child(even) {
           background-color: #f2f2f2; /* Warna latar belakang baris genap */
       }

       tr:hover {
           background-color: #ddd; /* Warna latar belakang saat hover pada baris */
       }

       img {
           border-radius: 4px; /* Sudut melengkung untuk gambar */
       }

       /* Gaya untuk link kembali ke dashboard user */
       a {
           margin-top: 20px; /* Jarak atas link */
           color: #35424a; /* Warna hijau untuk link */
           text-decoration: none; /* Tanpa garis bawah pada link */
       }

       a:hover {
           text-decoration: underline; /* Garis bawah saat hover pada link */
       }
   </style>
</head>

<body>

<h2>Laporan Absensi <?php echo htmlspecialchars($_SESSION['nama']); ?></h2>

<table>
<tr>
    <th>Tanggal</th>
    <th>Status</th>
    <th>Foto</th>
</tr>

<?php while ($row_absen = $result_absen->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row_absen["tanggal"]); ?></td>
    <td><?php echo htmlspecialchars($row_absen["status"]); ?></td>
    <td><img src="<?php echo htmlspecialchars($row_absen["foto"]); ?>" width='50' height='50'></td>
</tr>
<?php endwhile; ?>

</table>

<a href='user_dashboard.php'>Kembali ke Dashboard User</a><br>

</body>
</html>
