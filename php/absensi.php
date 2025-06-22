<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// Menangani parameter GET
$search = isset($_GET['search']) ? $_GET['search'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$waktu_shift = isset($_GET['waktu_shift']) ? $_GET['waktu_shift'] : ''; // Pilihan Shift
$limit = 10; // Menampilkan 10 data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Menyiapkan query berdasarkan filter dan pencarian
$query = "SELECT DISTINCT tanggal FROM absensi WHERE tanggal LIKE '%$search%' ";
if ($start_date && $end_date) {
    $query .= "AND tanggal BETWEEN '$start_date' AND '$end_date' ";
}
if ($waktu_shift) {
    $query .= "AND waktu_shift = '$waktu_shift' "; // Filter berdasarkan shift
}
$query .= "ORDER BY tanggal DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Menghitung jumlah total data untuk pagination
$count_query = "SELECT COUNT(DISTINCT tanggal) FROM absensi WHERE tanggal LIKE '%$search%'";
if ($start_date && $end_date) {
    $count_query .= " AND tanggal BETWEEN '$start_date' AND '$end_date'";
}
if ($waktu_shift) {
    $count_query .= " AND waktu_shift = '$waktu_shift' "; // Filter berdasarkan shift
}
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_row($count_result);
$total_pages = ceil($count_row[0] / $limit);
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
      <!-- Filter dan Pencarian -->
      <div class="mb-6">
        <form method="GET" class="flex gap-4 mb-4">
          <div>
            <label for="start_date" class="mr-2">Dari Tanggal:</label>
            <input type="date" name="start_date" id="start_date" class="px-4 py-2 border rounded" value="<?= $start_date ?>" />
          </div>
          <div>
            <label for="end_date" class="mr-2">Sampai Tanggal:</label>
            <input type="date" name="end_date" id="end_date" class="px-4 py-2 border rounded" value="<?= $end_date ?>" />
          </div>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
        </form>
      </div>

      <!-- Pilih Shift Pagi/Sore -->
      <div class="mb-6">
        <form method="GET" class="flex gap-4 mb-4">
          <div>
            <label for="waktu_shift" class="mr-2">Pilih Shift:</label>
            <select name="waktu_shift" id="waktu_shift" class="px-4 py-2 border rounded">
                <option value="">Semua Shift</option>
                <option value="Pagi" <?= $waktu_shift == 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                <option value="Sore" <?= $waktu_shift == 'Sore' ? 'selected' : '' ?>>Sore</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">Tampilkan Shift</button>
          </div>
        </form>
      </div>

      <!-- Riwayat Absensi -->
      <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
        <div class="overflow-x-auto">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Riwayat Absensi</h2>
            <button onclick="window.location.href='absensi-hari-ini.php'" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Absensi Hari Ini</button>
          </div>

          <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md">
            <thead class="bg-[#4b3b2f] text-white">
              <tr>
                <th class="py-3 px-4 text-left">Tanggal</th>
                <th class="py-3 px-4 text-left">Shift</th> <!-- Kolom Shift -->
                <th class="py-3 px-4 text-left">Jumlah Anggota Hadir</th>
                <th class="py-3 px-4 text-left">Jumlah Anggota Tidak Hadir</th>
                <th class="py-3 px-4 text-left">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while ($row = mysqli_fetch_assoc($result)) {
                $tanggal = $row['tanggal'];
                $hadir_pagi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM absensi WHERE tanggal='$tanggal' AND apel='Hadir' AND waktu_shift='Pagi'"));
                $hadir_sore = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM absensi WHERE tanggal='$tanggal' AND apel='Hadir' AND waktu_shift='Sore'"));
                $tidak_hadir_pagi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM absensi WHERE tanggal='$tanggal' AND apel='Tidak Hadir' AND waktu_shift='Pagi'"));
                $tidak_hadir_sore = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM absensi WHERE tanggal='$tanggal' AND apel='Tidak Hadir' AND waktu_shift='Sore'"));
                ?>
                <tr class="border-b">
                  <td class="py-2 px-4"><?= $tanggal ?></td>
                  <td class="py-2 px-4">Pagi & Sore</td> <!-- Menampilkan Shift -->
                  <td class="py-2 px-4"><?= $hadir_pagi ?> / <?= $hadir_sore ?></td> <!-- Jumlah Hadir Pagi & Sore -->
                  <td class="py-2 px-4"><?= $tidak_hadir_pagi ?> / <?= $tidak_hadir_sore ?></td> <!-- Jumlah Tidak Hadir Pagi & Sore -->
                  <td class="py-2 px-4">
                    <a href="absensi-detail.php?tanggal=<?= $tanggal ?>&waktu_shift=<?= $waktu_shift ?>" class="text-blue-600 hover:underline">Lihat Detail</a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-4">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=$i&search=$search&start_date=$start_date&end_date=$end_date&waktu_shift=$waktu_shift' class='px-4 py-2 mx-1 border rounded hover:bg-gray-200'>$i</a>";
        }
        ?>
      </div>
    </main>

    <?php include 'includes/footer.php'; ?>

  </div>

  <script src="../assets/js/dashboard.js"></script>

</body>
</html>
