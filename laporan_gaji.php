<?php
session_start();
if (!isset($_SESSION['karyawan_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Gaji per hari
$gaji_per_hari = 70000;

// Proses pencarian
$search_query = "";
$show_reset_button = false; // Variabel untuk mengontrol tampilan tombol reset

if (isset($_POST['search'])) {
    $search_term = $conn->real_escape_string($_POST['search_term']);
    $search_query = "WHERE k.nama LIKE '%$search_term%' AND k.role = 'user'";
    $show_reset_button = true; // Set true jika pencarian dilakukan
} else {
    // Jika tidak ada pencarian, ambil semua karyawan dengan role 'user'
    $search_query = "WHERE k.role = 'user'";
}

// Query untuk menghitung gaji karyawan berdasarkan absensi, hanya untuk karyawan dengan role 'user'
$sql_gaji = "
SELECT k.id, k.nama, 
COUNT(a.status = 'Hadir' OR NULL) AS jumlah_hadir,
(COUNT(a.status = 'Hadir' OR NULL) * $gaji_per_hari) AS total_gaji 
FROM karyawan k 
LEFT JOIN absensi a ON k.id = a.karyawan_id 
$search_query 
GROUP BY k.id";

$result_gaji = $conn->query($sql_gaji);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Gaji Karyawan</title>
    <style>
        /* Reset beberapa gaya default */
        body, h1, a, form, table, th, td {
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

        /* Gaya untuk header */
        .header {
            width: 100%;
            background-color: #16a085; /* Warna latar belakang header */
            color: white; /* Warna teks header */
            display: flex;
            justify-content: space-between; /* Menyebar konten ke kiri dan kanan */
            align-items: center; /* Pusatkan isi vertikal */
            padding: 10px 20px; /* Ruang dalam header */
            position: sticky; /* Membuat header tetap di atas saat scroll */
            top: 0; /* Posisi atas dari viewport */
            z-index: 1000; /* Memastikan header berada di atas elemen lainnya */
        }

        .header h1 {
            margin: 0; /* Menghilangkan margin judul */
        }

        /* Gaya untuk tombol di header */
        .header a {
            color: white; /* Warna teks tombol */
            text-decoration: none; /* Tanpa garis bawah pada link */
            margin-left: 20px; /* Jarak antara tombol */
        }

        .header a:hover {
            text-decoration: underline; /* Garis bawah saat hover pada link */
        }

        /* Gaya untuk judul halaman */
        h1 {
            margin-bottom: 20px; /* Jarak bawah judul */
            margin-top: 40px;
        }

        /* Gaya untuk form pencarian */
        form {
            margin-bottom: 20px; /* Jarak bawah form pencarian */
        }

        input[type="text"] {
            padding: 10px;
            margin-right: 10px; /* Jarak antara input dan tombol submit */
            border-radius: 4px; /* Sudut melengkung */
            border: 1px solid #ccc; /* Garis batas */
        }

        input[type="submit"] {
            background-color: #16a085; /* Hijau untuk tombol submit */
            color: white; /* Teks putih pada tombol submit */
            padding: 10px 15px; /* Ruang dalam tombol submit */
            border-radius: 4px; /* Sudut melengkung pada tombol submit */
            border: none; /* Tanpa garis batas pada tombol submit */
            cursor: pointer; /* Pointer saat hover pada tombol submit */
        }

        input[type="submit"]:hover {
            background-color: rgb(22, 48, 46); /* Hijau lebih gelap saat hover pada tombol submit */
        }

        /* Gaya untuk tabel */
        table {
            width: 90%; /* Lebar tabel 80% dari lebar halaman */
            max-width: 900px; /* Maksimal lebar tabel */
            border-collapse: collapse; /* Menghilangkan jarak antara sel */
            margin-top: 20px; /* Jarak atas tabel */
            background-color: white; /* Warna latar belakang tabel */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan tabel */
        }

        th, td {
            border: 1px solid #ddd; /* Garis batas sel */
            padding: 10px; /* Ruang dalam sel */
            text-align: left; /* Rata kiri teks dalam sel */
        }

        th {
            background-color: #16a085; /* Warna latar belakang header tabel */
            color: white; /* Warna teks header tabel */
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Warna latar belakang baris genap */
        }

        tr:hover {
            background-color: #ddd; /* Warna latar belakang saat hover pada baris */
        }

        a.back-link {
           margin-top: 20px;
           color: #35424a;
           text-decoration: none;
       }
       
       a.back-link:hover {
           text-decoration: underline;
       }
    </style>
</head>
<body>

<div class="header">
    <h1>Sistem Penggajian Karyawan (SIPEKA)</h1>
    <div>
        <a href='tambah_karyawan.php'>Tambah Karyawan Baru</a>
        <a href='laporan_gaji.php'>Lihat Laporan Gaji</a>
        <a href='logout.php'>Logout</a>
    </div>
</div>

<h1>Laporan Gaji Karyawan</h1>

<!-- Form Pencarian -->
<form method="post">
    <input type="text" name="search_term" placeholder="Cari Karyawan..." required>
    <input type="submit" name="search" value="Cari">
</form>

<a href='admin_dashboard.php' class='back-link'>Kembali ke Dashboard Admin</a><br>

<!-- Tombol untuk menampilkan semua data hanya jika pencarian dilakukan -->
<?php if ($show_reset_button): ?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="show-all-button">Tampilkan Semua Karyawan</a>
<?php endif; ?>

<table>
<tr>
    <th>ID Karyawan</th>
    <th>Nama</th>
    <th>Jumlah Hadir</th>
    <th>Total Gaji (Rincian)</th>
    <th>Slip Gaji</th> <!-- Kolom baru untuk slip gaji -->
</tr>

<?php while ($row_gaji = $result_gaji->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row_gaji['id']); ?></td>
    <td><?php echo htmlspecialchars($row_gaji['nama']); ?></td>
    <td><?php echo htmlspecialchars($row_gaji['jumlah_hadir']); ?></td>
    <td><?php echo "Rp " . number_format($row_gaji['total_gaji'], 0, ',', '.') . " (Jumlah Hadir: " . htmlspecialchars($row_gaji['jumlah_hadir']) . " x Rp " . number_format($gaji_per_hari, 0, ',', '.') . ")"; ?></td>

   <!-- Link untuk melihat slip gaji -->
   <td><a href="slip_gaji.php?id=<?php echo $row_gaji['id']; ?>">Lihat Slip Gaji</a></td> 
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
