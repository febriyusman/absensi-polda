<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// Tentukan jenis data
$data = $_GET['data'] ?? 'pangkat';
$tableMap = [
    'pangkat' => ['Pangkat', 'pangkat', 'nama_pangkat'],
    'jabatan' => ['Jabatan', 'jabatan', 'nama_jabatan'],
    'subsatker' => ['Subsatker', 'subsatker', 'nama_subsatker']
];

if (!array_key_exists($data, $tableMap)) {
    $data = 'pangkat';
}

list($label, $table, $column) = $tableMap[$data];
$result = mysqli_query($conn, "SELECT * FROM `$table` ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Master Referensi - <?= $label ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">
  <?php include 'includes/sidebar.php'; ?>
  <div class="flex-1 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="p-6 flex-1">
      <div class="bg-[#e4d3bb] p-6 rounded-xl shadow max-w-2xl mx-auto">
        <h2 class="text-xl font-semibold mb-4">Master Referensi: <?= $label ?></h2>

        <!-- Navigasi Tab -->
        <div class="mb-4 space-x-2">
          <a href="?data=pangkat" class="px-4 py-2 rounded bg-<?= $data === 'pangkat' ? '[#7a6a58]' : '[#9e8a73]' ?> text-white">Pangkat</a>
          <a href="?data=jabatan" class="px-4 py-2 rounded bg-<?= $data === 'jabatan' ? '[#7a6a58]' : '[#9e8a73]' ?> text-white">Jabatan</a>
          <a href="?data=subsatker" class="px-4 py-2 rounded bg-<?= $data === 'subsatker' ? '[#7a6a58]' : '[#9e8a73]' ?> text-white">Subsatker</a>
        </div>

        <!-- Form Tambah -->
        <form action="backend/proses-tambah-referensi.php" method="POST" class="mb-4 flex gap-2">
          <input type="hidden" name="table" value="<?= $table ?>">
          <input type="hidden" name="column" value="<?= $column ?>">
          <input type="text" name="value" required placeholder="Tambah <?= strtolower($label) ?>" class="flex-1 px-4 py-2 rounded border border-[#cbb89f] bg-[#fffaf4]">
          <button type="submit" class="bg-[#9e8a73] text-white px-4 rounded hover:bg-[#7a6a58]">Tambah</button>
        </form>

        <!-- Tabel Data -->
        <table class="w-full text-sm border">
          <thead class="bg-[#7a6a58] text-white">
            <tr>
              <th class="px-4 py-2">No</th>
              <th class="px-4 py-2">Nama <?= $label ?></th>
              <th class="px-4 py-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
              <tr class="border-b">
                <td class="px-4 py-2"><?= $no++ ?></td>
                <td class="px-4 py-2"><?= $row[$column] ?></td>
                <td class="px-4 py-2">
                  <!-- Bisa dikembangkan untuk ubah/hapus -->
                  <a href="backend/hapus-referensi.php?table=<?= $table ?>&id=<?= $row['id'] ?>" class="text-red-600 hover:underline">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>
    </main>

    <?php include 'includes/footer.php'; ?>
  </div>
</body>
</html>
