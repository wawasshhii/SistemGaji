<?php
$host = "localhost";
$user = "root"; // default user XAMPP
$password = ""; // default password kosong
$dbname = "sistem_gaji";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
