<?php
// backend/hapus-referensi.php

session_start();
if (!isset($_SESSION['admin'])) {
    // Jika bukan admin, hentikan proses
    http_response_code(403);
    echo "Akses ditolak.";
    exit;
}

include '../koneksi.php';

// Ambil data dari URL dan pastikan tidak kosong
$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? 0;
$data_type = $_GET['data'] ?? 'pangkat'; // Untuk redirect kembali ke tab yang benar

if (empty($table) || empty($id)) {
    $_SESSION['notification'] = ['type' => 'error', 'message' => 'Permintaan tidak valid.'];
    header("Location: ../master-referensi.php?data=" . urlencode($data_type));
    exit;
}

// Mapping tabel referensi ke kolom di tabel 'anggota'
$dependencyMap = [
    'jabatan' => 'jabatan_id',
    'pangkat' => 'pangkat_id',
    'subsatker' => 'subsatker_id'
];

$is_deletable = true;

// 1. Cek Ketergantungan (Dependency Check)
if (array_key_exists($table, $dependencyMap)) {
    $column_to_check = $dependencyMap[$table];
    
    // Gunakan prepared statement untuk keamanan
    $stmt_check = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM anggota WHERE $column_to_check = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row = mysqli_fetch_assoc($result_check);

    if ($row['count'] > 0) {
        $is_deletable = false;
        // Set notifikasi error karena data masih digunakan
        $label = ucfirst($table);
        $_SESSION['notification'] = [
            'type' => 'error', 
            'message' => "Gagal menghapus! $label ini masih digunakan oleh {$row['count']} anggota."
        ];
    }
    mysqli_stmt_close($stmt_check);
}

// 2. Proses Hapus Jika Memungkinkan
if ($is_deletable) {
    // Gunakan prepared statement untuk keamanan
    $stmt_delete = mysqli_prepare($conn, "DELETE FROM `$table` WHERE id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        // Set notifikasi sukses
        $_SESSION['notification'] = [
            'type' => 'success', 
            'message' => ucfirst($table) . ' berhasil dihapus.'
        ];
    } else {
        // Set notifikasi error jika query gagal
        $_SESSION['notification'] = [
            'type' => 'error', 
            'message' => 'Terjadi kesalahan saat menghapus data.'
        ];
    }
    mysqli_stmt_close($stmt_delete);
}

// 3. Redirect Kembali ke Halaman Master Referensi
header("Location: ../master-referensi.php?data=" . urlencode($data_type));
exit;
?>