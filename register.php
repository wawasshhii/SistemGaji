<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jabatan = $_POST['jabatan'];
    $role = 'user'; // Set role sebagai user

    // Upload foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
        $foto = $target_file;
    }

    $sql = "INSERT INTO karyawan (nama, username, password, jabatan, role, foto) VALUES ('$nama', '$username', '$password', '$jabatan', '$role', '$foto')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registrasi berhasil!";
        header("Location: login.php");
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
    <title>Registrasi Karyawan</title>
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
            justify-content: center;
            align-items: center;
            height: 100vh; /* Mengatur tinggi viewport */
        }

        .container {
            text-align: center; /* Menyelaraskan teks di tengah */
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px; /* Lebar form */
        }

        /* Gaya Form */
        form {
            margin-top: 20px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
        }

        form input[type="text"],
        form input[type="password"],
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form input[type="submit"] {
            background: #35424a;
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%; /* Lebar tombol */
        }

        form input[type="submit"]:hover {
            background: #e8491d;
        }

        /* Gaya Link Kembali */
        a {
            display: block;
            margin-top: 20px;
            color: #35424a;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registrasi Karyawan</h2>
        <form method="post" enctype="multipart/form-data">
            <label for="nama">Nama:</label>
            <input type="text" name="nama" required><br>

            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <label for="jabatan">Jabatan:</label>
            <input type="text" name="jabatan" required><br>

            <label for="foto">Foto:</label>
            <input type="file" name="foto" accept="image/*"><br>

            <input type="submit" value="Daftar">
        </form>
        <a href='login.php'>Kembali ke Login</a><br>
    </div>
</body>
</html>