<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// --- AWAL LOGIKA FILTER & PAGINATION ---

// Ambil parameter utama dan filter
$tanggal = $_GET['tanggal'];
$waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : 'Pagi'; // Default Pagi
$search_nama = isset($_GET['search_nama']) ? $_GET['search_nama'] : '';
$apel_status = isset($_GET['apel_status']) ? $_GET['apel_status'] : '';

// Pengaturan pagination
$per_halaman = isset($_GET['per_halaman']) ? (int)$_GET['per_halaman'] : 10;
$limit = $per_halaman;
if ($per_halaman === 0) { // Jika "Semua" (value=0) dipilih
    $limit = 999999; // Angka yang sangat besar untuk menampilkan semua
}
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menangani Update Absensi (hanya untuk data yang ditampilkan di halaman aktif)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anggota_id'])) {
    foreach ($_POST['anggota_id'] as $index => $anggota_id) {
        $apel = $_POST['apel'][$index];
        $keterangan = $_POST['keterangan'][$index];
        $pdf_file = $_POST['existing_pdf'][$index]; // Default ke file yang sudah ada

        // Cek jika ada file baru di-upload
        if (isset($_FILES['pdf_izin']['error'][$index]) && $_FILES['pdf_izin']['error'][$index] == 0) {
            $pdf_name = time() . '_' . basename($_FILES['pdf_izin']['name'][$index]);
            $pdf_tmp_name = $_FILES['pdf_izin']['tmp_name'][$index];
            $pdf_dest = '../uploads/' . $pdf_name;
            if (move_uploaded_file($pdf_tmp_name, $pdf_dest)) {
                $pdf_file = $pdf_dest; // Gunakan path file baru jika upload berhasil
            }
        }

        // Update Absensi
        $update_query = "UPDATE absensi SET apel='$apel', keterangan='$keterangan', sprint_pdf_path='$pdf_file' 
                         WHERE anggota_id='$anggota_id' AND tanggal='$tanggal' AND waktu_shift='$waktu_shift'";
        mysqli_query($conn, $update_query);
    }
    // Tambahkan parameter filter saat redirect agar tampilan tidak berubah
    $redirect_url = "absensi-detail.php?tanggal=$tanggal&waktu_shift=$waktu_shift&page=$page&per_halaman=$per_halaman&search_nama=$search_nama&apel_status=$apel_status";
    header("Location: " . $redirect_url);
    exit;
}

// Menyiapkan query dengan filter
$base_query = "FROM absensi a JOIN anggota b ON a.anggota_id = b.id";
$where_clauses = [];
$where_clauses[] = "a.tanggal = '$tanggal'";
$where_clauses[] = "a.waktu_shift = '$waktu_shift'";
if ($search_nama) {
    $where_clauses[] = "b.nama LIKE '%$search_nama%'";
}
if ($apel_status) {
    $where_clauses[] = "a.apel = '$apel_status'";
}
$where_query = " WHERE " . implode(' AND ', $where_clauses);

// Query untuk menghitung total data (untuk pagination)
$count_query = "SELECT COUNT(*) as total " . $base_query . $where_query;
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ($per_halaman > 0) ? ceil($total_records / $per_halaman) : 1;

// Query utama untuk mengambil data dengan LIMIT
$query = "SELECT a.*, b.nama " . $base_query . $where_query . " ORDER BY b.nama ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Absensi - <?= htmlspecialchars($tanggal) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">
    <?php include 'includes/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-h-screen">
        <?php include 'includes/header.php'; ?>
        <main class="p-6 flex-1">
            <h2 class="text-xl font-bold mb-4">Detail Absensi - <?= htmlspecialchars($tanggal) ?> (Shift: <?= htmlspecialchars($waktu_shift) ?>)</h2>

            <div class="bg-white p-4 rounded-lg shadow-sm mb-4">
                <form method="GET">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="search_nama" class="block text-sm font-medium mb-1">Nama Anggota:</label>
                            <input type="text" name="search_nama" id="search_nama" class="w-full px-3 py-2 border rounded" value="<?= htmlspecialchars($search_nama) ?>" />
                        </div>
                        <div>
                            <label for="apel_status" class="block text-sm font-medium mb-1">Status Apel:</label>
                            <select name="apel_status" id="apel_status" class="w-full px-3 py-2 border rounded">
                                <option value="">Semua</option>
                                <option value="Hadir" <?= $apel_status == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="Tidak Hadir" <?= $apel_status == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                            </select>
                        </div>
                        <div>
                            <label for="waktu_shift" class="block text-sm font-medium mb-1">Waktu Shift:</label>
                            <select name="waktu_shift" id="waktu_shift" class="w-full px-3 py-2 border rounded">
                                <option value="Pagi" <?= $waktu_shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                                <option value="Sore" <?= $waktu_shift == 'Sore' ? 'selected' : '' ?>>Sore</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 h-10">Terapkan Filter</button>
                    </div>
                </form>
            </div>

            <div class="my-4">
                <form method="GET" class="flex items-center">
                    <label for="per_halaman" class="mr-2">Tampilkan:</label>
                    <select name="per_halaman" id="per_halaman" class="px-3 py-1 border rounded" onchange="this.form.submit()">
                        <option value="10" <?= $per_halaman == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $per_halaman == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $per_halaman == 50 ? 'selected' : '' ?>>50</option>
                        <option value="0"  <?= $per_halaman == 0  ? 'selected' : '' ?>>Semua</option>
                    </select>
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <input type="hidden" name="waktu_shift" value="<?= htmlspecialchars($waktu_shift) ?>">
                    <input type="hidden" name="search_nama" value="<?= htmlspecialchars($search_nama) ?>">
                    <input type="hidden" name="apel_status" value="<?= htmlspecialchars($apel_status) ?>">
                </form>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md mt-4">
                    <thead class="bg-[#4b3b2f] text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Anggota</th>
                            <th class="py-3 px-4 text-left">Apel</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-left">Upload/Lihat PDF Izin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="border-b <?= $row['apel'] == 'Tidak Hadir' ? 'bg-red-100' : '' ?>">
                                <td class="py-2 px-4 align-top"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="py-2 px-4 align-top">
                                    <select name="apel[]" class="w-full border p-2 rounded">
                                        <option value="Hadir" <?= $row['apel'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="Tidak Hadir" <?= $row['apel'] == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                    </select>
                                </td>
                                <td class="py-2 px-4 align-top">
                                    <input type="text" name="keterangan[]" value="<?= htmlspecialchars($row['keterangan']) ?>" class="w-full border p-2 rounded">
                                </td>
                                <td class="py-2 px-4 align-top">
                                    <input type="file" name="pdf_izin[]" class="w-full border p-1 rounded text-xs">
                                    <?php if (!empty($row['sprint_pdf_path'])) { ?>
                                        <div class="mt-2">
                                            <a href="<?= htmlspecialchars($row['sprint_pdf_path']) ?>" target="_blank" class="text-blue-600 hover:underline text-xs">
                                                <i class="fas fa-file-pdf"></i> Lihat PDF Saat Ini
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <input type="hidden" name="existing_pdf[]" value="<?= htmlspecialchars($row['sprint_pdf_path']) ?>" />
                                </td>
                                <input type="hidden" name="anggota_id[]" value="<?= $row['anggota_id'] ?>">
                            </tr>
                        <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">Tidak ada data yang cocok dengan filter yang diterapkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="mt-6 text-center">
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">Simpan Perubahan di Halaman Ini</button>
                </div>
                <?php endif; ?>
            </form>

            <div class="mt-6 flex justify-between items-center">
                <span class="text-sm">Menampilkan <?= mysqli_num_rows($result) ?> dari <?= $total_records ?> data</span>
                <?php if ($total_pages > 1): ?>
                <div class="flex">
                    <?php if ($page > 1) : ?>
                        <a href="?page=<?= $page - 1 ?>&per_halaman=<?= $per_halaman ?>&tanggal=<?= $tanggal ?>&waktu_shift=<?= $waktu_shift ?>&search_nama=<?= $search_nama ?>&apel_status=<?= $apel_status ?>" class="px-3 py-1 bg-white border rounded-l-md hover:bg-gray-200">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <a href="?page=<?= $i ?>&per_halaman=<?= $per_halaman ?>&tanggal=<?= $tanggal ?>&waktu_shift=<?= $waktu_shift ?>&search_nama=<?= $search_nama ?>&apel_status=<?= $apel_status ?>" class="px-3 py-1 border-t border-b <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-200' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages) : ?>
                        <a href="?page=<?= $page + 1 ?>&per_halaman=<?= $per_halaman ?>&tanggal=<?= $tanggal ?>&waktu_shift=<?= $waktu_shift ?>&search_nama=<?= $search_nama ?>&apel_status=<?= $apel_status ?>" class="px-3 py-1 bg-white border rounded-r-md hover:bg-gray-200">Next</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>