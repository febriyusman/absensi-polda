<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../index.html");
    exit;
}
include '../koneksi.php';

$table = $_GET['table'];
$id = intval($_GET['id']);

if ($table && $id) {
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../master-referensi.php?data=$table");
exit;
?>
