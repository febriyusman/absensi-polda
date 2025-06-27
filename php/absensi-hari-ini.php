<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

$tanggal_hari_ini = date("Y-m-d");
$waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : 'Pagi';
$search_nama = isset($_GET['search_nama']) ? $_GET['search_nama'] : '';  // Filter Nama
$apel_status = isset($_GET['apel_status']) ? $_GET['apel_status'] : '';    // Filter Apel (Hadir / Tidak Hadir)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpan atau update absensi
    if (isset($_POST['save_all'])) {
        $waktu_shift = $_POST['waktu_shift'];  // Pilihan Pagi atau Sore
        $tanggal = $tanggal_hari_ini;

        // Loop untuk menyimpan setiap absensi
        foreach ($_POST['anggota_id'] as $index => $anggota_id) {
            $apel = $_POST['apel'][$index];
            $keterangan = $_POST['keterangan'][$index];
            $pdf_file = ''; // Default jika tidak ada file yang di-upload

            // Cek apakah ada file baru yang di-upload
            if ($_FILES['pdf_izin']['error'][$index] == 0) {
                // Menambahkan ID anggota dan timestamp untuk nama file yang unik
                $pdf_name = $anggota_id . '_' . time() . '_' . $_FILES['pdf_izin']['name'][$index];
                $pdf_tmp_name = $_FILES['pdf_izin']['tmp_name'][$index];
                $pdf_dest = '../uploads/' . $pdf_name;

                // Cek apakah file berhasil di-upload
                if (move_uploaded_file($pdf_tmp_name, $pdf_dest)) {
                    $pdf_file = $pdf_dest;
                } else {
                    echo "Error uploading PDF for anggota $anggota_id.<br>";
                }
            } else {
                // Jika tidak ada file baru, pertahankan file sebelumnya
                $pdf_file = $_POST['existing_pdf'][$index];
            }

            // Tentukan waktu sesuai shift yang dipilih
            if ($waktu_shift == 'Pagi') {
                $waktu = $tanggal . ' 07:00:00';  // Waktu untuk shift Pagi
            } else {
                $waktu = $tanggal . ' 15:00:00';  // Waktu untuk shift Sore
            }

            // Cek apakah absensi sudah ada untuk anggota ini di tanggal yang sama
            $cek_absensi_query = "SELECT * FROM absensi WHERE anggota_id = '$anggota_id' AND tanggal = '$tanggal' AND waktu_shift = '$waktu_shift'";
            $cek_absensi_result = mysqli_query($conn, $cek_absensi_query);

            if (mysqli_num_rows($cek_absensi_result) == 0) {
                // Jika absensi belum ada pada shift yang dipilih, simpan absensi
                $sql = "INSERT INTO absensi (anggota_id, apel, keterangan, waktu, tanggal, waktu_shift, sprint_pdf_path) 
                        VALUES ('$anggota_id', '$apel', '$keterangan', '$waktu', '$tanggal', '$waktu_shift', '$pdf_file')";
                mysqli_query($conn, $sql);
            } else {
                // Jika sudah ada absensi, update absensi yang ada
                $sql = "UPDATE absensi SET apel = '$apel', keterangan = '$keterangan', sprint_pdf_path = '$pdf_file' 
                        WHERE anggota_id = '$anggota_id' AND tanggal = '$tanggal' AND waktu_shift = '$waktu_shift'";
                mysqli_query($conn, $sql);
            }
        }

        header("Location: absensi-hari-ini.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Absensi Hari Ini</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">
    <?php include 'includes/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-h-screen">
        <?php include 'includes/header.php'; ?>
        <main class="p-6 flex-1">
            <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
                <h2 class="text-xl font-bold mb-4">Absensi Hari Ini - <?= $tanggal_hari_ini ?></h2>
                
                <!-- Form Filter -->
                <form method="GET" action="absensi-hari-ini.php" class="mb-6">
                    <div class="flex gap-4 mb-4">
                        <!-- Filter Nama -->
                        <div>
                            <label for="search_nama" class="mr-2">Nama Anggota:</label>
                            <input type="text" name="search_nama" id="search_nama" class="px-4 py-2 border rounded" value="<?= $search_nama ?>" />
                        </div>

                        <!-- Filter Apel -->
                        <div>
                            <label for="apel_status" class="mr-2">Status Apel:</label>
                            <select name="apel_status" id="apel_status" class="px-4 py-2 border rounded">
                                <option value="">Semua</option>
                                <option value="Hadir" <?= $apel_status == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="Tidak Hadir" <?= $apel_status == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                            </select>
                        </div>

                        <!-- Filter Shift -->
                        <div>
                            <label for="waktu_shift" class="mr-2">Pilih Shift:</label>
                            <select name="waktu_shift" id="waktu_shift" class="px-4 py-2 border rounded">
                                <option value="Pagi" <?= $waktu_shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                                <option value="Sore" <?= $waktu_shift == 'Sore' ? 'selected' : '' ?>>Sore</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tampilkan Absensi</button>
                </form>

                <!-- Form Absensi -->
                <form method="POST" enctype="multipart/form-data">
                    <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md mt-4">
                        <thead class="bg-[#4b3b2f] text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Nama Anggota</th>
                                <th class="py-3 px-4 text-left">Apel</th>
                                <th class="py-3 px-4 text-left">Keterangan</th>
                                <th class="py-3 px-4 text-left">Shift</th>
                                <th class="py-3 px-4 text-left">PDF Izin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT id, nama FROM anggota WHERE nama LIKE '%$search_nama%'";
                            if ($apel_status) {
                                $query .= " AND EXISTS (SELECT 1 FROM absensi WHERE anggota_id = anggota.id AND apel = '$apel_status' AND tanggal = '$tanggal_hari_ini' AND waktu_shift = '$waktu_shift')";
                            }

                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $anggota_id = $row['id'];
                                // Mengecek absensi anggota pada tanggal dan shift tertentu
                                $cek_absensi_query = "SELECT * FROM absensi WHERE anggota_id = '$anggota_id' AND tanggal = '$tanggal_hari_ini' AND waktu_shift = '$waktu_shift'";
                                $absensi_result = mysqli_query($conn, $cek_absensi_query);
                                $absensi_data = mysqli_fetch_assoc($absensi_result);
                            ?>
                                <tr class="border-b <?= $absensi_data && $absensi_data['apel'] == 'Tidak Hadir' ? 'bg-red-100' : '' ?>">
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="py-2 px-4">
                                        <select name="apel[]" class="w-full border p-2 rounded">
                                            <option value="Hadir" <?= $absensi_data && $absensi_data['apel'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="Tidak Hadir" <?= $absensi_data && $absensi_data['apel'] == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                        </select>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" name="keterangan[]" class="w-full border p-2 rounded" value="<?= $absensi_data['keterangan'] ?? '' ?>">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="hidden" name="waktu_shift" value="<?= $waktu_shift ?>">
                                        <?= $waktu_shift ?>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="file" name="pdf_izin[]" class="w-full border p-2 rounded" />
                                        
                                        <!-- Menampilkan Link PDF jika ada -->
                                        <?php if (isset($absensi_data['sprint_pdf_path']) && !empty($absensi_data['sprint_pdf_path'])) { ?>
                                            <div class="mt-2">
                                                <a href="<?= $absensi_data['sprint_pdf_path'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                    <i class="fas fa-file-pdf"></i> Lihat PDF
                                                </a>
                                                <!-- Menambahkan input tersembunyi untuk file yang sudah ada -->
                                                <input type="hidden" name="existing_pdf[]" value="<?= $absensi_data['sprint_pdf_path'] ?>" />
                                            </div>
                                        <?php } else { ?>
                                            <span class="text-red-500 text-sm">Belum Ada PDF</span>
                                        <?php } ?>
                                    </td>
                                    <input type="hidden" name="anggota_id[]" value="<?= $row['id'] ?>">
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="mt-4 text-center">
                        <button type="submit" name="save_all" class="bg-green-600 text-white px-6 py-3 rounded">Simpan Semua</button>
                    </div>
                </form>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
