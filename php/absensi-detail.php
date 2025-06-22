<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

if (isset($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
} else {
    header("Location: riwayat-absensi.php");
    exit;
}

// Mengambil data absensi berdasarkan tanggal dan shift (Pagi/Sore)
$waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : 'Pagi';  // Default adalah Pagi
$query = "SELECT a.*, b.nama, c.nama_pangkat, d.nama_jabatan, e.nama_subsatker 
          FROM absensi a
          JOIN anggota b ON a.anggota_id = b.id
          LEFT JOIN pangkat c ON b.pangkat_id = c.id
          LEFT JOIN jabatan d ON b.jabatan_id = d.id
          LEFT JOIN subsatker e ON b.subsatker_id = e.id
          WHERE a.tanggal = '$tanggal' AND a.waktu_shift = '$waktu_shift'
          ORDER BY a.waktu";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $absensi_id = $_POST['absensi_id'];
    $apel = $_POST['apel'];
    $keterangan = $_POST['keterangan'];

    // Update data absensi
    $update_query = "UPDATE absensi SET apel='$apel', keterangan='$keterangan' WHERE id='$absensi_id'";
    mysqli_query($conn, $update_query);
    header("Location: absensi-detail.php?tanggal=$tanggal&waktu_shift=$waktu_shift");
}
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
            <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
                <h2 class="text-xl font-bold mb-4">Detail Absensi - <?= $tanggal ?></h2>

                <!-- Dropdown untuk memilih shift Pagi/Sore -->
                <form method="GET" action="absensi-detail.php" class="mb-6">
                    <label for="waktu_shift" class="mr-4">Pilih Shift:</label>
                    <select name="waktu_shift" id="waktu_shift" class="border p-2 rounded">
                        <option value="Pagi" <?= $waktu_shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                        <option value="Sore" <?= $waktu_shift == 'Sore' ? 'selected' : '' ?>>Sore</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">Tampilkan Absensi</button>
                    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                </form>

                <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md">
                    <thead class="bg-[#4b3b2f] text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Anggota</th>
                            <th class="py-3 px-4 text-left">Apel</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-left">Shift</th> <!-- Menampilkan Shift langsung -->
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <form method="POST">
                                <tr class="border-b">
                                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="py-2 px-4">
                                        <select name="apel" class="w-full border p-2 rounded">
                                            <option value="Hadir" <?= $row['apel'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="Tidak Hadir" <?= $row['apel'] == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                        </select>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="text" name="keterangan" value="<?= htmlspecialchars($row['keterangan']) ?>" class="w-full border p-2 rounded">
                                    </td>
                                    <td class="py-2 px-4">
                                        <!-- Menampilkan shift yang sesuai -->
                                        <?= $row['waktu_shift'] ?>
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="hidden" name="absensi_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded">Edit</button>
                                    </td>
                                </tr>
                            </form>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
