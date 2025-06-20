<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../index.html");
    exit;
}
include '../koneksi.php';

$table = $_POST['table'];
$column = $_POST['column'];
$value = trim($_POST['value']);
$id = intval($_POST['id']);

if ($table && $column && $value && $id) {
    $stmt = $conn->prepare("UPDATE `$table` SET `$column` = ? WHERE id = ?");
    $stmt->bind_param("si", $value, $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../master-referensi.php?data=$table");
exit;
?>
