<?php
session_start();
if (!isset($_SESSION['karyawan_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Ambil ID karyawan dari parameter URL
if (isset($_GET['id'])) {
    $karyawan_id = intval($_GET['id']);

    // Gaji per hari
    $gaji_per_hari = 70000;

    // Query untuk mendapatkan data karyawan dan menghitung gaji
    $sql = "
    SELECT k.id, k.nama, 
    COUNT(a.status = 'Hadir' OR NULL) AS jumlah_hadir,
    (COUNT(a.status = 'Hadir' OR NULL) * $gaji_per_hari) AS total_gaji 
    FROM karyawan k 
    LEFT JOIN absensi a ON k.id = a.karyawan_id 
    WHERE k.id = $karyawan_id 
    GROUP BY k.id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    echo "ID Karyawan tidak valid.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji Karyawan</title>
    <style>
        /* Reset beberapa gaya default */
        body, h1, a, table, th, td {
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
            text-align: center;
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

        /* Gaya untuk tabel slip gaji */
        table {
            width: 80%; /* Lebar tabel 80% dari lebar halaman */
            max-width: 800px; /* Maksimal lebar tabel */
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

        button {
            background-color: #16a085; /* Hijau untuk tombol cetak */
            color: white; /* Teks putih pada tombol cetak */
            padding: 10px 15px; /* Ruang dalam tombol cetak */
            border-radius: 4px; /* Sudut melengkung pada tombol cetak */
            border: none; /* Tanpa garis batas pada tombol cetak */
            cursor: pointer; /* Pointer saat hover pada tombol cetak */
            margin-top: 20px; /* Jarak atas tombol cetak dari elemen sebelumnya */
        }

        button:hover {
            background-color: #rgb(22, 48, 46); /* Hijau lebih gelap saat hover pada tombol cetak */
        }

        a.back-link {
           margin-top: 20px;
           color: #35424a;
           text-decoration: none;
       }
       
       a.back-link:hover {
           text-decoration: underline;
       }

        @media print {
            .header, .tombol, .back-link, .aidi {
                display: none;
            }
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

<h1>Slip Gaji Karyawan</h1>

<div>
    <h2>Azura Store</h2>
    <p>Jl. Angsa Putih, Simpang Tiga,Kec. Bukit Raya <br>Kota Pekanbaru, Riau 28289</p>
</div>

<table>
<tr>
    <th class="aidi">ID Karyawan</th>
    <th>Nama</th>
    <th>Jumlah Hadir</th>
    <th>Total Gaji</th>
</tr>
<tr>
    <td class="aidi"><?php echo htmlspecialchars($row['id']); ?></td>
    <td><?php echo htmlspecialchars($row['nama']); ?></td>
    <td><?php echo htmlspecialchars($row['jumlah_hadir']); ?></td>
    <td><?php echo "Rp " . number_format($row['total_gaji'], 0, ',', '.'); ?></td>
</tr>
</table>

<button onclick="window.print()" class="tombol">Cetak Slip Gaji</button> <!-- Tombol untuk mencetak slip gaji -->

<a href='laporan_gaji.php' class='back-link'>Kembali ke Laporan Gaji</a><br>

</body>
</html>

