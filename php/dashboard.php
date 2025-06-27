<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// Menangani Filter Rentang Waktu
$range = isset($_GET['range']) ? $_GET['range'] : '7'; // Default 7 hari
$tanggal_hari_ini = date("Y-m-d");

switch ($range) {
    case '7':
        $date_filter = date('Y-m-d', strtotime('-7 days'));
        break;
    case '90':
        $date_filter = date('Y-m-d', strtotime('-3 months'));
        break;
    case '365':
        $date_filter = date('Y-m-d', strtotime('-1 year'));
        break;
    default:
        $date_filter = date('Y-m-d', strtotime('-7 days'));
        break;
}

$query_absensi = "SELECT a.*, b.nama FROM absensi a JOIN anggota b ON a.anggota_id = b.id WHERE a.tanggal >= '$date_filter' ORDER BY a.tanggal DESC";
$result_absensi = mysqli_query($conn, $query_absensi);

$query_hadir = "SELECT COUNT(*) as hadir, DATE_FORMAT(a.tanggal, '%Y-%m-%d') as tgl FROM absensi a WHERE a.apel = 'Hadir' AND a.tanggal >= '$date_filter' GROUP BY tgl";
$query_tidak_hadir = "SELECT COUNT(*) as tidak_hadir, DATE_FORMAT(a.tanggal, '%Y-%m-%d') as tgl FROM absensi a WHERE a.apel = 'Tidak Hadir' AND a.tanggal >= '$date_filter' GROUP BY tgl";

$hadir_result = mysqli_query($conn, $query_hadir);
$tidak_hadir_result = mysqli_query($conn, $query_tidak_hadir);

$hadir_data = [];
$tidak_hadir_data = [];

while ($row = mysqli_fetch_assoc($hadir_result)) {
    $hadir_data[$row['tgl']] = $row['hadir'];
}
while ($row = mysqli_fetch_assoc($tidak_hadir_result)) {
    $tidak_hadir_data[$row['tgl']] = $row['tidak_hadir'];
}

$total_hadir = array_sum($hadir_data);
$total_tidak_hadir = array_sum($tidak_hadir_data);

$query_total_anggota = "SELECT COUNT(*) as total_anggota FROM anggota";
$result_total_anggota = mysqli_query($conn, $query_total_anggota);
$row_total_anggota = mysqli_fetch_assoc($result_total_anggota);
$total_anggota = $row_total_anggota['total_anggota'];

$persen_hadir = $total_anggota > 0 ? round(($total_hadir / $total_anggota) * 100, 2) : 0;

$minggu_ini_hadir = $total_hadir / 7;
$minggu_ini_tidak_hadir = $total_tidak_hadir / 7;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Absensi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-[#f4f7f6] text-[#4b3b2f] min-h-screen flex">

<?php include 'includes/sidebar.php'; ?>

<div class="flex-1 flex flex-col min-h-screen">
<?php include 'includes/header.php'; ?>

<main class="p-6 flex-1">
  <div class="bg-[#e4d3bb] p-4 rounded-xl shadow mb-6">
    <h2 class="text-xl font-bold mb-4">Dashboard Absensi</h2>

    <div class="grid grid-cols-3 gap-4 mb-6">
      <div>
        <a href="absensi-hari-ini.php" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 w-full text-center block">Tambah Absensi Baru</a>
      </div>
    </div>

    <div class="mb-6">
      <label for="filterRange" class="font-bold">Filter Rentang Waktu</label>
      <select id="filterRange" onchange="window.location.href='dashboard.php?range=' + this.value" class="p-2 border rounded">
        <option value="7" <?= $range == '7' ? 'selected' : '' ?>>7 Hari Terakhir</option>
        <option value="90" <?= $range == '90' ? 'selected' : '' ?>>3 Bulan Terakhir</option>
        <option value="365" <?= $range == '365' ? 'selected' : '' ?>>1 Tahun Terakhir</option>
      </select>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="bg-white p-4 rounded-xl shadow">
        <h3 class="text-lg font-bold">Total Anggota</h3>
        <p class="text-2xl"><?= $total_anggota ?> Anggota</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow">
        <h3 class="text-lg font-bold">Hadir</h3>
        <p class="text-2xl"><?= $total_hadir ?> Hadir (<?= $persen_hadir ?>%)</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow">
        <h3 class="text-lg font-bold">Tidak Hadir</h3>
        <p class="text-2xl"><?= $total_tidak_hadir ?> Tidak Hadir</p>
      </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow mb-6">
      <h3 class="font-bold text-lg mb-4">Perbandingan Apel Hadir dan Tidak Hadir</h3>
      <canvas id="apelChart"></canvas>
      <script>
        const ctx = document.getElementById('apelChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: <?= json_encode(array_keys($hadir_data)) ?>,
            datasets: [
              {
                label: 'Hadir',
                data: <?= json_encode(array_values($hadir_data)) ?>,
                backgroundColor: '#4CAF50'
              },
              {
                label: 'Tidak Hadir',
                data: <?= json_encode(array_values($tidak_hadir_data)) ?>,
                backgroundColor: '#FF5733'
              }
            ]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      </script>
    </div>

    <div class="bg-white p-4 rounded-xl shadow mb-6">
      <h3 class="font-bold text-lg mb-4">Statistik Mingguan</h3>
      <p>Rata-rata Kehadiran Minggu Ini: <?= round($minggu_ini_hadir, 2) ?> Hari</p>
      <p>Rata-rata Tidak Hadir Minggu Ini: <?= round($minggu_ini_tidak_hadir, 2) ?> Hari</p>
    </div>

    <div class="overflow-x-auto bg-white p-4 rounded-xl shadow">
      <h3 class="font-bold text-lg mb-4">Tabel Absensi</h3>
      <table id="absensiTable" class="min-w-full text-sm">
        <thead class="bg-[#4b3b2f] text-white">
          <tr>
            <th class="py-3 px-4 text-left">Nama Anggota</th>
            <th class="py-3 px-4 text-left">Apel</th>
            <th class="py-3 px-4 text-left">Keterangan</th>
            <th class="py-3 px-4 text-left">PDF Izin</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result_absensi)) { ?>
            <tr class="border-b <?= $row['apel'] == 'Tidak Hadir' ? 'bg-red-100' : '' ?>">
              <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($row['apel']) ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($row['keterangan']) ?></td>
              <td class="py-2 px-4">
                <?php if (!empty($row['sprint_pdf_path'])) { ?>
                  <a href="<?= $row['sprint_pdf_path'] ?>" target="_blank" class="text-blue-600 hover:underline">Lihat PDF</a>
                <?php } else { ?>
                  <span class="text-red-500">Belum Ada PDF</span>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</div>

<script>
  $(document).ready(function() {
    $('#absensiTable').DataTable();
  });
</script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
