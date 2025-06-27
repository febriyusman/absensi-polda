<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

$tanggal = $_GET['tanggal'];
$waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : 'Pagi'; // Default Pagi
$search_nama = isset($_GET['search_nama']) ? $_GET['search_nama'] : '';  // Filter Nama
$apel_status = isset($_GET['apel_status']) ? $_GET['apel_status'] : '';    // Filter Apel (Hadir / Tidak Hadir)

// Menangani Update Absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['anggota_id'] as $index => $anggota_id) {
        $apel = $_POST['apel'][$index];
        $keterangan = $_POST['keterangan'][$index];
        $pdf_file = null;

        // Cek apakah ada file PDF yang di-upload, jika ya simpan file baru
        if ($_FILES['pdf_izin']['error'][$index] == 0) {
            $pdf_name = $_FILES['pdf_izin']['name'][$index];
            $pdf_tmp_name = $_FILES['pdf_izin']['tmp_name'][$index];
            $pdf_dest = '../uploads/' . $pdf_name;
            if (move_uploaded_file($pdf_tmp_name, $pdf_dest)) {
                $pdf_file = $pdf_dest;
            }
        } else {
            // Jika tidak ada file PDF yang di-upload, gunakan file yang lama
            $pdf_file = $_POST['existing_pdf'][$index]; // Ambil file yang sudah ada dari input tersembunyi
        }

        // Update Absensi
        $update_query = "UPDATE absensi SET apel='$apel', keterangan='$keterangan', sprint_pdf_path='$pdf_file' 
                         WHERE anggota_id='$anggota_id' AND tanggal='$tanggal' AND waktu_shift='$waktu_shift'";
        mysqli_query($conn, $update_query);
    }
    header("Location: absensi-detail.php?tanggal=$tanggal&waktu_shift=$waktu_shift");
    exit;
}

// Ambil data absensi untuk tanggal dan shift tertentu
$query = "SELECT a.*, b.nama FROM absensi a JOIN anggota b ON a.anggota_id = b.id WHERE a.tanggal = '$tanggal' AND a.waktu_shift = '$waktu_shift'";

// Tambahkan filter nama
if ($search_nama) {
    $query .= " AND b.nama LIKE '%$search_nama%'";
}

// Tambahkan filter apel (Hadir/Tidak Hadir)
if ($apel_status) {
    $query .= " AND a.apel = '$apel_status'";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Absensi - <?= $tanggal ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">
    <?php include 'includes/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-h-screen">
        <?php include 'includes/header.php'; ?>
        <main class="p-6 flex-1">
            <h2 class="text-xl font-bold mb-4">Detail Absensi - <?= $tanggal ?></h2>

            <!-- Filter untuk Nama, Status Apel, dan Waktu Shift -->
            <form method="GET" class="mb-6">
                <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                <div class="flex gap-4 mb-4">
                    <!-- Filter Nama -->
                    <div>
                        <label for="search_nama" class="mr-2">Nama Anggota:</label>
                        <input type="text" name="search_nama" id="search_nama" class="px-4 py-2 border rounded" value="<?= $search_nama ?>" />
                    </div>

                    <!-- Filter Status Apel -->
                    <div>
                        <label for="apel_status" class="mr-2">Status Apel:</label>
                        <select name="apel_status" id="apel_status" class="px-4 py-2 border rounded">
                            <option value="">Semua</option>
                            <option value="Hadir" <?= $apel_status == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                            <option value="Tidak Hadir" <?= $apel_status == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                        </select>
                    </div>

                    <!-- Filter Waktu Shift -->
                    <div>
                        <label for="waktu_shift" class="mr-2">Waktu Shift:</label>
                        <select name="waktu_shift" id="waktu_shift" class="px-4 py-2 border rounded">
                            <option value="Pagi" <?= $waktu_shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                            <option value="Sore" <?= $waktu_shift == 'Sore' ? 'selected' : '' ?>>Sore</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tampilkan</button>
            </form>

            <!-- Form untuk Mengedit Absensi -->
            <form method="POST" enctype="multipart/form-data">
                <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md mt-4">
                    <thead class="bg-[#4b3b2f] text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Anggota</th>
                            <th class="py-3 px-4 text-left">Apel</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-left">PDF Izin</th> <!-- Kolom Upload PDF -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="border-b <?= $row['apel'] == 'Tidak Hadir' ? 'bg-red-100' : '' ?>"> <!-- Merah untuk Tidak Hadir -->
                                <td class="py-2 px-4"><?= $row['nama'] ?></td>
                                <td class="py-2 px-4">
                                    <select name="apel[]" class="w-full border p-2 rounded">
                                        <option value="Hadir" <?= $row['apel'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="Tidak Hadir" <?= $row['apel'] == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                    </select>
                                </td>
                                <td class="py-2 px-4">
                                    <input type="text" name="keterangan[]" value="<?= $row['keterangan'] ?>" class="w-full border p-2 rounded">
                                </td>
                                <td class="py-2 px-4">
                                    <!-- Input File untuk Upload PDF -->
                                    <div class="relative">
                                        <input type="file" name="pdf_izin[]" class="w-full border p-2 rounded bg-white" id="pdf_izin" />
                                        <label for="pdf_izin" class="absolute top-0 left-0 w-full h-full flex items-center justify-center cursor-pointer text-sm text-gray-600"></label>
                                    </div>

                                    <!-- Menampilkan Link PDF jika ada -->
                                    <?php if (isset($row['sprint_pdf_path']) && !empty($row['sprint_pdf_path'])) { ?>
                                        <div class="mt-2">
                                            <a href="<?= $row['sprint_pdf_path'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                <i class="fas fa-file-pdf"></i> Lihat PDF
                                            </a>
                                            <!-- Input tersembunyi untuk file yang sudah ada -->
                                            <input type="hidden" name="existing_pdf[]" value="<?= $row['sprint_pdf_path'] ?>" />
                                        </div>
                                    <?php } else { ?>
                                        <span class="text-red-500 text-sm">Belum Ada PDF</span>
                                    <?php } ?>
                                </td>
                                <input type="hidden" name="anggota_id[]" value="<?= $row['anggota_id'] ?>">
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="mt-4 text-center">
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded">Simpan Perubahan</button>
                </div>
            </form>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
