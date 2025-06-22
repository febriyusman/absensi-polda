<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

$tanggal_hari_ini = date("Y-m-d");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpan semua absensi
    if (isset($_POST['save_all'])) {
        $waktu_shift = $_POST['waktu_shift'];  // Pilihan Pagi atau Sore
        $tanggal = $tanggal_hari_ini;
        
        // Loop untuk menyimpan setiap absensi
        foreach ($_POST['anggota_id'] as $index => $anggota_id) {
            $apel = $_POST['apel'][$index];
            $keterangan = $_POST['keterangan'][$index];

            // Menangani upload PDF (keterangan izin/keperluan lainnya)
            $pdf_file = null;
            if ($_FILES['pdf_izin']['error'][$index] == 0) {
                $pdf_name = $_FILES['pdf_izin']['name'][$index];
                $pdf_tmp_name = $_FILES['pdf_izin']['tmp_name'][$index];
                $pdf_dest = '../uploads/' . $pdf_name;

                if (move_uploaded_file($pdf_tmp_name, $pdf_dest)) {
                    $pdf_file = $pdf_dest;
                } else {
                    echo "Error uploading PDF for anggota $anggota_id.<br>";
                }
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

            // Jika absensi belum ada pada shift yang dipilih, simpan absensi
            if (mysqli_num_rows($cek_absensi_result) == 0) {
                $sql = "INSERT INTO absensi (anggota_id, apel, keterangan, waktu, tanggal, waktu_shift, sprint_pdf_path) 
                        VALUES ('$anggota_id', '$apel', '$keterangan', '$waktu', '$tanggal', '$waktu_shift', '$pdf_file')";
                mysqli_query($conn, $sql);
            } else {
                // Jika sudah ada absensi untuk shift ini, beri pesan atau tindakan yang sesuai
                echo "Absensi sudah tercatat untuk anggota $anggota_id pada shift $waktu_shift pada tanggal $tanggal.<br>";
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
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">
    <?php include 'includes/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-h-screen">
        <?php include 'includes/header.php'; ?>
        <main class="p-6 flex-1">
            <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
                <h2 class="text-xl font-bold mb-4">Absensi Hari Ini - <?= $tanggal_hari_ini ?></h2>
                
                <!-- Form untuk memilih shift pagi atau sore -->
                <form method="GET" action="absensi-hari-ini.php">
                    <label for="waktu_shift" class="mr-4">Pilih Shift:</label>
                    <select name="waktu_shift" class="border p-2 rounded">
                        <option value="Pagi">Pagi</option>
                        <option value="Sore">Sore</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">Tampilkan Absensi</button>
                </form>

                <form method="POST" enctype="multipart/form-data">
                    <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md mt-4">
                        <thead class="bg-[#4b3b2f] text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Nama Anggota</th>
                                <th class="py-3 px-4 text-left">Apel</th>
                                <th class="py-3 px-4 text-left">Keterangan</th>
                                <th class="py-3 px-4 text-left">Shift</th>
                                <th class="py-3 px-4 text-left">PDF Izin</th> <!-- Kolom untuk Upload PDF -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Menangani shift pagi atau sore
                            $waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : 'Pagi';
                            // Mengambil anggota dari database
                            $query = "SELECT id, nama FROM anggota";
                            $result = mysqli_query($conn, $query);
                            $anggota_ids = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                $anggota_ids[] = $row['id'];
                            ?>
                                <tr class="border-b">
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="py-2 px-4">
                                        <select name="apel[]" class="w-full border p-2 rounded">
                                            <option value="Hadir">Hadir</option>
                                            <option value="Tidak Hadir">Tidak Hadir</option>
                                        </select>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" name="keterangan[]" class="w-full border p-2 rounded">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="hidden" name="waktu_shift" value="<?= $waktu_shift ?>">
                                        <?= $waktu_shift ?>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="file" name="pdf_izin[]" class="w-full border p-2 rounded">
                                    </td>
                                    <input type="hidden" name="anggota_id[]" value="<?= $row['id'] ?>">
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <!-- Tombol Simpan Semua di bawah tabel -->
                    <div class="mt-4 text-center">
                        <button type="submit" name="save_all" class="bg-green-600 text-white px-6 py-3 rounded">Simpan Semua</button>
                    </div>
                </form>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
