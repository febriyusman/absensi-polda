<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include '../koneksi.php';

$table = $_GET['table'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$allowed_tables = ['anggota', 'pangkat', 'jabatan', 'subsatker', 'absensi'];

if (in_array($table, $allowed_tables) && $id > 0) {
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../data-anggota.php");
exit;
