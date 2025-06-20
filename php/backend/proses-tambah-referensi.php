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

if ($table && $column && $value) {
    $stmt = $conn->prepare("INSERT INTO `$table` (`$column`) VALUES (?)");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../master-referensi.php?data=$table");
exit;
?>
