<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// --- AWAL LOGIKA PAGINATION & FILTER ---

// Menangani parameter GET
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : '';

// 1. Tentukan jumlah data per halaman dari GET, defaultnya 10
$per_halaman = isset($_GET['per_halaman']) ? (int)$_GET['per_halaman'] : 10;
$limit = $per_halaman;
if ($per_halaman === 0) { // Jika "Semua" dipilih (value=0)
    $limit = 999999; // Set angka yang sangat besar untuk menampilkan semua
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menyiapkan query berdasarkan filter
$base_query = "FROM absensi";
$where_clauses = [];

if ($start_date && $end_date) {
    $where_clauses[] = "tanggal BETWEEN '$start_date' AND '$end_date'";
}
if ($waktu_shift) {
    $where_clauses[] = "waktu_shift = '$waktu_shift'";
}

$where_query = "";
if (!empty($where_clauses)) {
    $where_query = " WHERE " . implode(" AND ", $where_clauses);
}

// Query untuk mengambil data unik per tanggal dengan LIMIT
$query = "SELECT DISTINCT tanggal, waktu_shift " . $base_query . $where_query . " ORDER BY tanggal DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Query untuk menghitung total data untuk pagination
$count_query = "SELECT COUNT(DISTINCT tanggal) as total " . $base_query . $where_query;
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ($per_halaman > 0) ? ceil($total_records / $per_halaman) : 1;

// --- AKHIR LOGIKA PAGINATION & FILTER ---
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-h-screen">
        <?php include 'includes/header.php'; ?>

        <main class="p-6 flex-1">
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <form method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="start_date" class="block text-sm font-medium mb-1">Dari Tanggal:</label>
                            <input type="date" name="start_date" id="start_date" class="w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($start_date) ?>" />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium mb-1">Sampai Tanggal:</label>
                            <input type="date" name="end_date" id="end_date" class="w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($end_date) ?>" />
                        </div>
                        <div>
                            <label for="waktu_shift" class="block text-sm font-medium mb-1">Pilih Shift:</label>
                            <select name="waktu_shift" id="waktu_shift" class="w-full px-3 py-2 border rounded">
                                <option value="">Semua Shift</option>
                                <option value="Pagi" <?= $waktu_shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                                <option value="Sore" <?= $waktu_shift == 'Sore' ? 'selected' : '' ?>>Sore</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                             <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Terapkan</button>
                             <a href="absensi.php" class="w-full text-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Reset</a>
                        </div>
                    </div>
                </form>
            </div>


            <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
                <div class="overflow-x-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Riwayat Absensi</h2>
                        <button onclick="window.location.href='absensi-hari-ini.php'" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Absensi Hari Ini</button>
                    </div>

                    <div class="mb-4">
                        <form method="GET" class="flex items-center">
                            <label for="per_halaman" class="mr-2">Tampilkan:</label>
                            <select name="per_halaman" id="per_halaman" class="px-3 py-1 border rounded" onchange="this.form.submit()">
                                <option value="10" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '10') ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '25') ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '50') ? 'selected' : '' ?>>50</option>
                                <option value="0"  <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '0') ? 'selected' : '' ?>>Semua</option>
                            </select>
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
                            <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
                            <input type="hidden" name="waktu_shift" value="<?= htmlspecialchars($waktu_shift) ?>">
                        </form>
                    </div>


                    <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md">
                        <thead class="bg-[#4b3b2f] text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal</th>
                                <th class="py-3 px-4 text-left">Shift</th>
                                <th class="py-3 px-4 text-left">Jumlah Hadir</th>
                                <th class="py-3 px-4 text-left">Jumlah Tidak Hadir</th>
                                <th class="py-3 px-4 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $tanggal = $row['tanggal'];
                                    $shift_tabel = $row['waktu_shift'];

                                    // Hitung jumlah hadir
                                    $query_hadir = "SELECT COUNT(*) FROM absensi WHERE tanggal='$tanggal' AND waktu_shift='$shift_tabel' AND apel='Hadir'";
                                    $hadir_result = mysqli_query($conn, $query_hadir);
                                    $hadir_count = mysqli_fetch_row($hadir_result)[0];

                                    // Hitung jumlah tidak hadir
                                    $query_tidak_hadir = "SELECT COUNT(*) FROM absensi WHERE tanggal='$tanggal' AND waktu_shift='$shift_tabel' AND apel='Tidak Hadir'";
                                    $tidak_hadir_result = mysqli_query($conn, $query_tidak_hadir);
                                    $tidak_hadir_count = mysqli_fetch_row($tidak_hadir_result)[0];
                                    ?>
                                    <tr class="border-b">
                                        <td class="py-2 px-4"><?= htmlspecialchars($tanggal) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($shift_tabel) ?></td>
                                        <td class="py-2 px-4"><?= $hadir_count ?></td>
                                        <td class="py-2 px-4"><?= $tidak_hadir_count ?></td>
                                        <td class="py-2 px-4">
                                            <a href="absensi-detail.php?tanggal=<?= $tanggal ?>&waktu_shift=<?= $shift_tabel ?>" class="text-blue-600 hover:underline">Lihat Detail</a>
                                        </td>
                                    </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4'>Tidak ada data riwayat absensi yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-between items-center">
                    <span class="text-sm">Menampilkan data unik per tanggal (Total: <?= $total_records ?>)</span>
                    <?php if ($total_pages > 1): ?>
                    <div class="flex">
                        <?php if ($page > 1) : ?>
                            <a href="?page=<?= $page - 1 ?>&per_halaman=<?= $per_halaman ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&waktu_shift=<?= $waktu_shift ?>" class="px-3 py-1 bg-white border rounded-l-md hover:bg-gray-200">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <a href="?page=<?= $i ?>&per_halaman=<?= $per_halaman ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&waktu_shift=<?= $waktu_shift ?>" class="px-3 py-1 border-t border-b <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-200' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages) : ?>
                            <a href="?page=<?= $page + 1 ?>&per_halaman=<?= $per_halaman ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&waktu_shift=<?= $waktu_shift ?>" class="px-3 py-1 bg-white border rounded-r-md hover:bg-gray-200">Next</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>