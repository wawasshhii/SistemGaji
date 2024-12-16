<?php
session_start();
if (!isset($_SESSION['karyawan_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Tanggal hari ini
$tanggal_hari_ini = date('Y-m-d');

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

// Query untuk mengambil data karyawan hanya dengan role 'user'
$sql_karyawan = "
SELECT k.id, k.nama, k.jabatan, k.role, k.foto, 
IFNULL(a.status, 'Belum Absen') AS keterangan  -- Mengambil status dari absensi atau 'Belum Absen' jika tidak ada
FROM karyawan k 
LEFT JOIN absensi a ON k.id = a.karyawan_id AND a.tanggal = '$tanggal_hari_ini'  -- Mengambil absensi hari ini
$search_query 
GROUP BY k.id";

$result_karyawan = $conn->query($sql_karyawan);

// Proses penghapusan
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Hapus entri terkait di tabel absensi terlebih dahulu
    $delete_absensi_sql = "DELETE FROM absensi WHERE karyawan_id='$delete_id'";
    $conn->query($delete_absensi_sql); // Eksekusi penghapusan dari tabel absensi
    
    // Kemudian hapus dari tabel karyawan
    $delete_sql = "DELETE FROM karyawan WHERE id='$delete_id'";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Karyawan berhasil dihapus.";
        header("Location: ".$_SERVER['PHP_SELF']); // Refresh halaman setelah penghapusan
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Proses update
if (isset($_POST['update'])) {
    $update_id = intval($_POST['id']);
    $update_nama = $_POST['nama'];
    $update_jabatan = $_POST['jabatan'];
    
    $update_sql = "UPDATE karyawan SET nama='$update_nama', jabatan='$update_jabatan' WHERE id='$update_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "Data karyawan berhasil diperbarui.";
        header("Location: ".$_SERVER['PHP_SELF']); // Refresh halaman setelah update
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Karyawan</title>
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
        h2 {
            margin-bottom: 20px; /* Jarak bawah subjudul */
        }

        /* Gaya untuk tabel */
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

        /* Gaya untuk tombol tampilkan semua karyawan yang lebih baik */
        .show-all-button {
            display: inline-block;
            background-color: #16a085; /* Hijau untuk tombol */
            color: white; /* Teks putih pada tombol */
            padding: 10px 15px; /* Ruang dalam tombol */
            border-radius: 5px; /* Sudut melengkung pada tombol */
            text-decoration: none; /* Tanpa garis bawah pada link tombol */
            margin-top: 20px; /* Jarak atas tombol dari elemen sebelumnya */
        }

        .show-all-button:hover {
            background-color: #45a049; /* Hijau lebih gelap saat hover pada tombol */
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

<h2>Data Karyawan</h2>

<!-- Form Pencarian -->
<form method="post">
    <input type="text" name="search_term" placeholder="Cari Karyawan..." required>
    <input type="submit" name="search" value="Cari">
</form>

<!-- Tombol untuk menampilkan semua data hanya jika pencarian dilakukan -->
<?php if ($show_reset_button): ?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="show-all-button">Tampilkan Semua Karyawan</a>
<?php endif; ?>

<table>
<tr>
    <th>ID Karyawan</th>
    <th>Foto</th>
    <th>Nama</th>
    <th>Jabatan</th>
    <th>Role</th>
    <th>Keterangan</th> <!-- Kolom baru untuk keterangan hadir -->
    <th>Aksi</th> <!-- Kolom untuk aksi -->
</tr>

<?php while ($row_karyawan = $result_karyawan->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row_karyawan['id']); ?></td>
    <td><img src="<?php echo htmlspecialchars($row_karyawan['foto']); ?>" width='50' height='50'></td>
    <td><?php echo htmlspecialchars($row_karyawan['nama']); ?></td>
    <td><?php echo htmlspecialchars($row_karyawan['jabatan']); ?></td>
    <td><?php echo htmlspecialchars($row_karyawan['role']); ?></td>

<td><?php echo htmlspecialchars($row_karyawan['keterangan']); ?></td> <!-- Menampilkan status absensi -->

<td>
        <!-- Link untuk hapus -->
        <a href="?delete_id=<?php echo $row_karyawan['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a> |
        <!-- Link untuk update -->
        <a href="#updateModal<?php echo $row_karyawan['id']; ?>" onclick="document.getElementById('updateModal<?php echo $row_karyawan['id']; ?>').style.display='block'">Update</a>

        <!-- Modal Update -->
        <div id="updateModal<?php echo $row_karyawan['id']; ?>" style="display:none;">
            <form method="post">
                <input type="hidden" name="id" value="<?php echo $row_karyawan['id']; ?>">
                Nama: <input type="text" name="nama" value="<?php echo htmlspecialchars($row_karyawan['nama']); ?>" required><br>
                Jabatan: <input type="text" name="jabatan" value="<?php echo htmlspecialchars($row_karyawan['jabatan']); ?>" required><br>
                <input type="submit" name="update" value="Update">
                <button type="button" onclick="document.getElementById('updateModal<?php echo $row_karyawan['id']; ?>').style.display='none'">Batal</button>
            </form>
        </div>

</td>

</tr>

<?php endwhile; ?>

</table>

<script>
// Script untuk menutup modal jika klik di luar modal
window.onclick = function(event) {
  var modals = document.querySelectorAll('[id^=updateModal]');
  modals.forEach(function(modal) {
      if (event.target == modal) {
          modal.style.display = "none";
      }
  });
}
</script>

</body>
</html>

