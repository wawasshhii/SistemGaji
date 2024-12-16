<?php
session_start();
include 'config.php';

$error_message = ""; // Variabel untuk menyimpan pesan kesalahan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM karyawan WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['karyawan_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error_message = "Password salah!"; // Set pesan kesalahan
        }
    } else {
        $error_message = "Username tidak ditemukan!"; // Set pesan kesalahan
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <title>Login</title>
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
       }

       h2 {
           margin-bottom: 20px; /* Jarak antara judul dan form */
       }

       /* Gaya Form */
       form {
           background: #ffffff;
           padding: 20px;
           border-radius: 5px;
           box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           width: 300px; /* Lebar form */
           margin: 0 auto; /* Menyelaraskan form di tengah */
       }

       form label {
           display: block;
           margin-bottom: 5px;
       }

       form input[type="text"],
       form input[type="password"] {
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
           padding: 10px;
           border-radius: 5px;
           cursor: pointer;
           width: 100%; /* Lebar tombol */
       }

       form input[type="submit"]:hover {
           background:rgb(22, 48, 46);
       }

       /* Gaya Link Sign Up */
       p {
           margin-top: 20px;
       }

       p a {
           color: #35424a;
           text-decoration: none;
       }

       p a:hover {
           text-decoration: underline;
       }

       /* Gaya Pesan Kesalahan */
       .error-message {
           color: red; /* Warna merah untuk pesan kesalahan */
           margin-top: 10px; /* Jarak atas untuk pesan kesalahan */
       }
   </style>
</head>
<body>
   <div class="container">
        <h1>Selamat Datang di SIPEKA!</h1>
      <h2>Silahkan Login</h2> <!-- Judul di atas form -->
      <form method="post">
         <label for="username">Username:</label>
         <input type="text" name="username" required><br>

         <label for="password">Password:</label>
         <input type="password" name="password" required><br>

         <input type="submit" value="Login">
      </form>

      <!-- Menampilkan pesan kesalahan di bawah form -->
      <?php if ($error_message): ?>
         <div class="error-message"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <p>Belum punya akun? <a href='register.php'>Sign up</a></p