<?php
session_start();
include 'koneksi.php';

// Tangkap data dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Query ke tabel admin
$query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $_SESSION['admin'] = $username;
    header("Location: dashboard.php");
    exit;
} else {
    echo "<script>
        alert('Username atau Password salah!');
        window.location.href = '../index.html';
    </script>";
}
?>