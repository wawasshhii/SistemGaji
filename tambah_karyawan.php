<?php
session_start();
if (!isset($_SESSION['karyawan_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $jabatan = $_POST['jabatan'];
    $role = $_POST['role'];

    // Upload foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
        $foto = $target_file;
    }

    // Menyimpan data karyawan ke database
    $sql = "INSERT INTO karyawan (nama, username, password, jabatan, role, foto) VALUES ('$nama', '$username', '$password', '$jabatan', '$role', '$foto')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Karyawan berhasil ditambahkan!";
        header("Location: admin_dashboard.php"); // Redirect ke dashboard admin setelah sukses
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Karyawan</title>
    <style>
        /* Reset beberapa gaya default */
        body, h1, h2, a, form {
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
            margin-top: 40px;
            margin-bottom: 20px; /* Jarak bawah subjudul */
        }

        /* Gaya untuk form tambah karyawan */
        form {
            background-color: white; /* Warna latar belakang form */
            padding: 20px; /* Ruang dalam form */
            border-radius: 8px; /* Sudut melengkung pada form */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Bayangan pada form */
            width: 500px; /* Lebar form */
        }

        input[type="text"], input[type="password"], input[type="file"], select {
            width: calc(100% - 20px); /* Lebar penuh dengan margin */
            padding: 10px; /* Ruang dalam inputan */
            margin-bottom: 15px; /* Jarak bawah inputan */
            border-radius: 4px; /* Sudut melengkung */
            border: 1px solid #ccc; /* Garis batas */
        }

        input[type="submit"] {
            background-color: #16a085; /* Hijau untuk tombol submit */
            color: white; /* Teks putih pada tombol submit */
            padding: 10px; /* Ruang dalam tombol submit */
            border-radius: 4px; /* Sudut melengkung pada tombol submit */
            border: none; /* Tanpa garis batas pada tombol submit */
            cursor: pointer; /* Pointer saat hover pada tombol submit */
        }

        input[type="submit"]:hover {
            background-color: rgb(22, 48, 46);; /* Hijau lebih gelap saat hover pada tombol submit */
        }

        a.back-link {
            margin-top: 20px; /* Jarak atas link kembali dari elemen sebelumnya */
            color: #35424a; /* Warna hijau untuk link kembali */
            text-decoration: none; /* Tanpa garis bawah pada link kembali */
        }

        a.back-link:hover {
            text-decoration: underline; /* Garis bawah saat hover pada link kembali */
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

<h2>Tambah Karyawan Baru</h2>

<form method="post" enctype="multipart/form-data">
    Nama:<br>
    <input type="text" name="nama" required><br>

    Username:<br>
    <input type="text" name="username" required><br>

    Password:<br>
    <input type="password" name="password" required><br>

    Jabatan:<br>
    <input type="text" name="jabatan" required><br>

    Role:<br>
    <select name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option> <!-- Jika Anda ingin admin juga bisa ditambahkan -->
    </select><br>

    Foto:<br>
    <input type="file" name="foto" accept="image/*"><br>

    <input type="submit" value="Tambah Karyawan">
</form>

<a href='admin_dashboard.php' class='back-link'>Kembali ke Dashboard Admin</a><br>

</body>
</html>

