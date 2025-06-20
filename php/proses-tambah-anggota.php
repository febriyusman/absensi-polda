<?php
session_start();
include 'koneksi.php';

$nama = $_POST['nama'];
$pangkat_id = $_POST['pangkat_id'];
$jabatan_id = $_POST['jabatan_id'];
$subsatker_id = $_POST['subsatker_id'];

$query = "INSERT INTO absensi (nama, pangkat_id, jabatan_id, subsatker_id)
          VALUES ('$nama', '$pangkat_id', '$jabatan_id', '$subsatker_id')";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Anggota berhasil ditambahkan'); window.location.href='tambah-anggota.php';</script>";
} else {
    echo "<script>alert('Gagal menambahkan anggota'); window.location.href='tambah-anggota.php';</script>";
}
?>
