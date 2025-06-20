<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';
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

  <!-- Konten -->
  <div class="flex-1 flex flex-col min-h-screen">
    
    <?php include 'includes/header.php'; ?>

    <main class="p-6 flex-1">
      <!-- Isi tabel dashboard seperti sebelumnya -->
      <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
        <div class="overflow-x-auto">

        <?php
// Menangani tambah absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $aksi = $_POST['aksi'] ?? '';
  $anggota_id = $_POST['anggota_id'];
  $apel = $_POST['apel'];
  $keterangan = $_POST['keterangan'];
  $waktu = $_POST['waktu'];
  $tanggal = $_POST['tanggal'];
  $sprint = $_FILES['sprint_pdf_path']['name'];
  $sprint_tmp = $_FILES['sprint_pdf_path']['tmp_name'];
  
  if ($sprint) {
    $path = '../uploads/' . $sprint;
    move_uploaded_file($sprint_tmp, $path);
  } else {
    $path = null;
  }

  if ($aksi === 'tambah') {
    $sql = "INSERT INTO absensi (anggota_id, apel, keterangan, waktu, tanggal, sprint_pdf_path)
            VALUES ('$anggota_id', '$apel', '$keterangan', '$waktu', '$tanggal', '$path')";
  } elseif ($aksi === 'edit') {
    $id = $_POST['id'];
    $sql = "UPDATE absensi SET anggota_id='$anggota_id', apel='$apel', keterangan='$keterangan', waktu='$waktu', tanggal='$tanggal'";
    if ($path) $sql .= ", sprint_pdf_path='$path'";
    $sql .= " WHERE id='$id'";
  }

  mysqli_query($conn, $sql);
  echo "<script>window.location.href='';</script>";
}
?>

<div class="flex justify-between items-center mb-4">
  <h2 class="text-xl font-bold">Data Absensi</h2>
  <button onclick="bukaModalAbsensi()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Tambah Absensi</button>
</div>

<table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md">
  <thead class="bg-[#4b3b2f] text-white">
    <tr>
      <th class="py-3 px-4 text-left">No</th>
      <th class="py-3 px-4 text-left">Nama</th>
      <th class="py-3 px-4 text-left">Apel</th>
      <th class="py-3 px-4 text-left">Keterangan</th>
      <th class="py-3 px-4 text-left">Waktu</th>
      <th class="py-3 px-4 text-left">Tanggal</th>
      <th class="py-3 px-4 text-left">Sprint</th>
      <th class="py-3 px-4 text-left">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;
    $query = "SELECT absensi.*, anggota.nama FROM absensi 
              JOIN anggota ON absensi.anggota_id = anggota.id 
              ORDER BY tanggal DESC, waktu DESC";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <tr class="border-b">
      <td class="py-2 px-4"><?= $no++ ?></td>
      <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
      <td class="py-2 px-4"><?= $row['apel'] ?></td>
      <td class="py-2 px-4"><?= htmlspecialchars($row['keterangan']) ?></td>
      <td class="py-2 px-4"><?= $row['waktu'] ?></td>
      <td class="py-2 px-4"><?= $row['tanggal'] ?></td>
      <td class="py-2 px-4">
        <?php if ($row['sprint_pdf_path']): ?>
          <a href="<?= $row['sprint_pdf_path'] ?>" target="_blank" class="text-blue-600 underline">Lihat</a>
        <?php else: ?>
          Tidak ada
        <?php endif; ?>
      </td>
      <td class="py-2 px-4">
        <button onclick='bukaModalEditAbsensi(<?= json_encode($row) ?>)' class="text-blue-600 hover:underline mr-2">Edit</button>
        <a href="backend/hapus.php?table=absensi&id=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')" class="text-red-600 hover:underline">Hapus</a>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<!-- Modal Tambah/Edit Absensi -->
<div id="modalAbsensi" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded-lg w-full max-w-xl p-6 relative">
    <h2 id="judulModalAbsensi" class="text-lg font-semibold mb-4">Tambah Absensi</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="aksi" id="aksiAbsensi">
      <input type="hidden" name="id" id="absensiId">

      <label>Nama Anggota</label>
      <select name="anggota_id" id="anggota_id" class="w-full border p-2 mb-3 rounded" required>
        <?php
        $anggota = mysqli_query($conn, "SELECT id, nama FROM anggota");
        while ($a = mysqli_fetch_assoc($anggota)) {
          echo "<option value='{$a['id']}'>{$a['nama']}</option>";
        }
        ?>
      </select>

      <label>Apel</label>
      <select name="apel" id="apel" class="w-full border p-2 mb-3 rounded" required>
        <option value="Hadir">Hadir</option>
        <option value="Tidak Hadir">Tidak Hadir</option>
      </select>

      <label>Keterangan</label>
      <input type="text" name="keterangan" id="keterangan" class="w-full border p-2 mb-3 rounded">

      <label>Waktu</label>
      <input type="time" name="waktu" id="waktu" class="w-full border p-2 mb-3 rounded" required>

      <label>Tanggal</label>
      <input type="date" name="tanggal" id="tanggal" class="w-full border p-2 mb-3 rounded" required>

      <label>Sprint (PDF)</label>
      <input type="file" name="sprint_pdf_path" class="w-full border p-2 mb-3 rounded" accept=".pdf">

      <div class="flex justify-end">
        <button type="button" onclick="tutupModalAbsensi()" class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

        </div>
      </div>
    </main>

    <?php include 'includes/footer.php'; ?>

  </div>

  <script src="../assets/js/dashboard.js"></script>

  <script>
function bukaModalAbsensi() {
  document.getElementById('judulModalAbsensi').innerText = 'Tambah Absensi';
  document.getElementById('aksiAbsensi').value = 'tambah';
  document.getElementById('absensiId').value = '';
  document.getElementById('anggota_id').value = '';
  document.getElementById('apel').value = 'Hadir';
  document.getElementById('keterangan').value = '';
  document.getElementById('waktu').value = '';
  document.getElementById('tanggal').value = '';
  document.getElementById('modalAbsensi').classList.remove('hidden');
  document.getElementById('modalAbsensi').classList.add('flex');
}

function bukaModalEditAbsensi(data) {
  document.getElementById('judulModalAbsensi').innerText = 'Edit Absensi';
  document.getElementById('aksiAbsensi').value = 'edit';
  document.getElementById('absensiId').value = data.id;
  document.getElementById('anggota_id').value = data.anggota_id;
  document.getElementById('apel').value = data.apel;
  document.getElementById('keterangan').value = data.keterangan;
  document.getElementById('waktu').value = data.waktu;
  document.getElementById('tanggal').value = data.tanggal;
  document.getElementById('modalAbsensi').classList.remove('hidden');
  document.getElementById('modalAbsensi').classList.add('flex');
}

function tutupModalAbsensi() {
  document.getElementById('modalAbsensi').classList.add('hidden');
}
</script>

</body>
</html>
