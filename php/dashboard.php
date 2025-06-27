<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// --- PENGATURAN & FILTER RENTANG WAKTU ---
$range = isset($_GET['range']) ? $_GET['range'] : '7'; // Default 7 hari
$days_in_range = (int)$range;

// Validasi untuk memastikan range adalah angka yang diizinkan
if (!in_array($range, ['7', '90', '365'])) {
    $range = '7';
}
$date_filter = date('Y-m-d', strtotime("-$range days"));

// --- PENGAMBILAN DATA DENGAN QUERY EFISIEN & AMAN ---

// 1. Ambil data untuk Grafik (Hadir & Tidak Hadir dalam 1 query)
$chart_labels = [];
$chart_hadir_data = [];
$chart_tidak_hadir_data = [];
$date_iterator = new DatePeriod(
    new DateTime($date_filter),
    new DateInterval('P1D'),
    new DateTime('+1 day')
);

// Inisialisasi array chart dengan 0 untuk setiap hari dalam rentang
foreach ($date_iterator as $date) {
    $formatted_date = $date->format('d M Y');
    $chart_labels[] = $formatted_date;
    $chart_hadir_data[$formatted_date] = 0;
    $chart_tidak_hadir_data[$formatted_date] = 0;
}

// Query tunggal untuk mendapatkan jumlah hadir dan tidak hadir per hari
$query_chart = "
    SELECT 
        DATE_FORMAT(tanggal, '%d %b %Y') as tgl,
        COUNT(CASE WHEN apel = 'Hadir' THEN 1 END) as jumlah_hadir,
        COUNT(CASE WHEN apel = 'Tidak Hadir' THEN 1 END) as jumlah_tidak_hadir
    FROM absensi 
    WHERE tanggal >= ?
    GROUP BY tgl, tanggal
    ORDER BY tanggal ASC
";
$stmt_chart = mysqli_prepare($conn, $query_chart);
mysqli_stmt_bind_param($stmt_chart, 's', $date_filter);
mysqli_stmt_execute($stmt_chart);
$result_chart = mysqli_stmt_get_result($stmt_chart);

while ($row = mysqli_fetch_assoc($result_chart)) {
    $chart_hadir_data[$row['tgl']] = $row['jumlah_hadir'];
    $chart_tidak_hadir_data[$row['tgl']] = $row['jumlah_tidak_hadir'];
}

// 2. Ambil data untuk Ringkasan (Summary Cards)
$total_hadir = array_sum($chart_hadir_data);
$total_tidak_hadir = array_sum($chart_tidak_hadir_data);
$total_absensi = $total_hadir + $total_tidak_hadir;
$persen_hadir = $total_absensi > 0 ? round(($total_hadir / $total_absensi) * 100, 2) : 0;

// 3. Ambil Total Anggota
$query_total_anggota = "SELECT COUNT(*) as total_anggota FROM anggota";
$result_total_anggota = mysqli_query($conn, $query_total_anggota);
$total_anggota = mysqli_fetch_assoc($result_total_anggota)['total_anggota'];

// 4. Perhitungan Statistik Rata-rata Harian
$avg_hadir = $days_in_range > 0 ? $total_hadir / $days_in_range : 0;
$avg_tidak_hadir = $days_in_range > 0 ? $total_tidak_hadir / $days_in_range : 0;

// 5. Ambil data untuk Tabel Absensi (dengan LIMIT untuk performa)
$query_absensi = "SELECT a.tanggal, a.apel, a.keterangan, a.sprint_pdf_path, b.nama 
                  FROM absensi a 
                  JOIN anggota b ON a.anggota_id = b.id 
                  WHERE a.tanggal >= ? 
                  ORDER BY a.tanggal DESC, b.nama ASC 
                  LIMIT 250"; // Batasi 250 data untuk mencegah browser lambat
$stmt_absensi = mysqli_prepare($conn, $query_absensi);
mysqli_stmt_bind_param($stmt_absensi, 's', $date_filter);
mysqli_stmt_execute($stmt_absensi);
$result_absensi = mysqli_stmt_get_result($stmt_absensi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Absensi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">

<?php include 'includes/sidebar.php'; ?>

<div class="flex-1 flex flex-col min-h-screen">
<?php include 'includes/header.php'; ?>

<main class="p-6 flex-1">
  <div class="bg-[#e4d3bb] p-6 rounded-xl shadow mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Dashboard Absensi</h2>
        <a href="absensi-hari-ini.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 shadow transition-transform transform hover:scale-105">
            + Absensi Hari Ini
        </a>
    </div>

    <div class="mb-6 flex items-center gap-4">
      <label for="filterRange" class="font-bold">Tampilkan Data:</label>
      <select id="filterRange" onchange="window.location.href='dashboard.php?range=' + this.value" class="p-2 border rounded-lg shadow-sm">
        <option value="7" <?= $range == '7' ? 'selected' : '' ?>>7 Hari Terakhir</option>
        <option value="90" <?= $range == '90' ? 'selected' : '' ?>>90 Hari Terakhir</option>
        <option value="365" <?= $range == '365' ? 'selected' : '' ?>>365 Hari Terakhir</option>
      </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white p-4 rounded-xl shadow-lg">
        <h3 class="text-lg font-bold text-gray-600">Total Anggota</h3>
        <p class="text-3xl font-semibold"><?= $total_anggota ?> Orang</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-lg">
        <h3 class="text-lg font-bold text-gray-600">Total Kehadiran (Rentang Waktu)</h3>
        <p class="text-3xl font-semibold text-green-600"><?= $total_hadir ?> Hadir (<?= $persen_hadir ?>%)</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-lg">
        <h3 class="text-lg font-bold text-gray-600">Total Tidak Hadir (Rentang Waktu)</h3>
        <p class="text-3xl font-semibold text-red-600"><?= $total_tidak_hadir ?> Tidak Hadir</p>
      </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-lg mb-6">
      <h3 class="font-bold text-lg mb-4">Grafik Kehadiran Harian</h3>
      <canvas id="apelChart"></canvas>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-lg mb-6">
      <h3 class="font-bold text-lg mb-4">Statistik Rata-rata Harian (Rentang Waktu)</h3>
      <div class="flex justify-around">
        <p>Rata-rata Hadir: <span class="font-bold text-green-600"><?= round($avg_hadir, 2) ?></span> / hari</p>
        <p>Rata-rata Tidak Hadir: <span class="font-bold text-red-600"><?= round($avg_tidak_hadir, 2) ?></span> / hari</p>
      </div>
    </div>

    <div class="overflow-x-auto bg-white p-4 rounded-xl shadow-lg">
      <h3 class="font-bold text-lg mb-4">Tabel Riwayat Absensi (Maks. 250 Terbaru)</h3>
      <table id="absensiTable" class="min-w-full text-sm display">
        <thead class="bg-[#4b3b2f] text-white">
          <tr>
            <th class="py-3 px-4 text-left">Tanggal</th>
            <th class="py-3 px-4 text-left">Nama Anggota</th>
            <th class="py-3 px-4 text-left">Apel</th>
            <th class="py-3 px-4 text-left">Keterangan</th>
            <th class="py-3 px-4 text-left">PDF Izin</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result_absensi)) { ?>
            <tr class="border-b hover:bg-gray-50 <?= $row['apel'] == 'Tidak Hadir' ? 'bg-red-50' : '' ?>">
              <td class="py-2 px-4"><?= htmlspecialchars($row['tanggal']) ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
              <td class="py-2 px-4 font-semibold <?= $row['apel'] == 'Hadir' ? 'text-green-700' : 'text-red-700' ?>"><?= htmlspecialchars($row['apel']) ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($row['keterangan']) ?></td>
              <td class="py-2 px-4">
                <?php if (!empty($row['sprint_pdf_path'])) { ?>
                  <a href="<?= htmlspecialchars($row['sprint_pdf_path']) ?>" target="_blank" class="text-blue-600 hover:underline">Lihat PDF</a>
                <?php } else { ?>
                  <span class="text-gray-400">-</span>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
  // Inisialisasi DataTables
  $(document).ready(function() {
    $('#absensiTable').DataTable({
        "order": [[ 0, "desc" ]] // Urutkan berdasarkan tanggal (kolom pertama) secara menurun
    });
  });

  // Inisialisasi Chart.js
  const ctx = document.getElementById('apelChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($chart_labels) ?>,
      datasets: [
        {
          label: 'Hadir',
          data: <?= json_encode(array_values($chart_hadir_data)) ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.6)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        },
        {
          label: 'Tidak Hadir',
          data: <?= json_encode(array_values($chart_tidak_hadir_data)) ?>,
          backgroundColor: 'rgba(255, 99, 132, 0.6)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1 // Pastikan skala y adalah bilangan bulat
          }
        }
      }
    }
  });
</script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>