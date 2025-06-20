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

list($label, $tableName, $columnName) = $tableMap[$data];
$result = mysqli_query($conn, "SELECT * FROM `$tableName` ORDER BY id ASC");
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
          <input type="hidden" name="table" value="<?= $tableName ?>">
          <input type="hidden" name="column" value="<?= $columnName ?>">
          <input type="text" name="value" required placeholder="Tambah <?= strtolower($label) ?>" class="flex-1 px-4 py-2 rounded border border-[#cbb89f] bg-[#fffaf4]">
          <button type="submit" class="bg-[#9e8a73] text-white px-4 rounded hover:bg-[#7a6a58]">Tambah</button>
        </form>

        <!-- Modal Edit -->
        <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
            <h2 class="text-lg font-semibold mb-4">Edit <?= $label ?></h2>
            <form action="backend/proses-edit-referensi.php" method="POST" class="space-y-4">
              <input type="hidden" name="table" value="<?= $tableName ?>">
              <input type="hidden" name="column" value="<?= $columnName ?>">
              <input type="hidden" name="id" id="edit-id">
              <div>
                <input type="text" name="value" id="edit-value" required class="w-full px-4 py-2 rounded bg-[#fffaf4] border border-[#cbb89f]">
              </div>
              <div class="flex justify-between">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
              </div>
            </form>
          </div>
        </div>

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
                <td class="px-4 py-2"><?= htmlspecialchars($row[$columnName]) ?></td>
                <td class="px-4 py-2 space-x-2">
                  <button onclick="editItem(<?= $row['id'] ?>, '<?= htmlspecialchars($row[$columnName]) ?>')" class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                  <a href="backend/hapus-referensi.php?table=<?= $tableName ?>&id=<?= $row['id'] ?>"
                     onclick="return confirm('Yakin ingin menghapus data ini?')"
                     class="text-red-600 hover:underline">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>
    </main>

    <?php include 'includes/footer.php'; ?>
  </div>

  <script>
    function editItem(id, value) {
      document.getElementById("edit-id").value = id;
      document.getElementById("edit-value").value = value;
      document.getElementById("editModal").classList.remove("hidden");
    }
    function closeModal() {
      document.getElementById("editModal").classList.add("hidden");
    }
  </script>
</body>
</html>
