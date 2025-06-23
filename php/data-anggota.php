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
      <div class="bg-[#e4d3bb] p-4 rounded-xl shadow">
        <div class="overflow-x-auto">

            <?php
            // Menangani tambah anggota
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
                $nama = $_POST['nama'];
                $pangkat = $_POST['pangkat_id'];
                $jabatan = $_POST['jabatan_id'];
                $subsatker = $_POST['subsatker_id'];
                mysqli_query($conn, "INSERT INTO anggota (nama, pangkat_id, jabatan_id, subsatker_id) VALUES ('$nama', '$pangkat', '$jabatan', '$subsatker')");
                echo "<script>window.location.href='';</script>";
            }

            // Menangani edit anggota
            if (isset($_POST['aksi']) && $_POST['aksi'] === 'edit') {
                $id = $_POST['id'];
                $nama = $_POST['nama'];
                $pangkat = $_POST['pangkat_id'];
                $jabatan = $_POST['jabatan_id'];
                $subsatker = $_POST['subsatker_id'];
                mysqli_query($conn, "UPDATE anggota SET nama='$nama', pangkat_id='$pangkat', jabatan_id='$jabatan', subsatker_id='$subsatker' WHERE id='$id'");
                echo "<script>window.location.href='';</script>";
            }
            }
            ?>

            <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Data Anggota</h2>
            <button onclick="bukaModalTambah()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Tambah Anggota</button>
            </div>

            <table class="min-w-full bg-white text-sm rounded-xl overflow-hidden shadow-md">
            <thead class="bg-[#4b3b2f] text-white">
                <tr>
                <th class="py-3 px-4 text-left">No</th>
                <th class="py-3 px-4 text-left">Nama</th>
                <th class="py-3 px-4 text-left">Pangkat</th>
                <th class="py-3 px-4 text-left">Jabatan</th>
                <th class="py-3 px-4 text-left">Subsatker</th>
                <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $result = mysqli_query($conn, "SELECT a.id, a.nama, p.nama_pangkat, j.nama_jabatan, s.nama_subsatker, a.pangkat_id, a.jabatan_id, a.subsatker_id FROM anggota a 
                LEFT JOIN pangkat p ON a.pangkat_id = p.id 
                LEFT JOIN jabatan j ON a.jabatan_id = j.id 
                LEFT JOIN subsatker s ON a.subsatker_id = s.id");

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr class="border-b">
                    <td class="py-2 px-4"><?= $no++ ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama_pangkat']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($row['nama_subsatker']) ?></td>
                    <td class="py-2 px-4">
                    <button class="text-blue-600 hover:underline mr-2" onclick='bukaModalEdit(<?= json_encode($row) ?>)'>Edit</button>
                    <a href="backend/hapus-anggota.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            </table>

        </div>
      </div>
      <!-- Modal Form Tambah/Edit -->
    <div id="modalAnggota" class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-lg p-6 relative">
        <h2 class="text-lg font-semibold mb-4" id="modalJudul">Tambah Anggota</h2>
        <form method="POST">
        <input type="hidden" name="id" id="anggotaId">
        <input type="hidden" name="aksi" id="aksi">

        <label class="block mb-2">Nama</label>
        <input type="text" name="nama" id="nama" class="w-full border p-2 mb-4 rounded" required>

        <label class="block mb-2">Pangkat</label>
        <select name="pangkat_id" id="pangkat_id" class="w-full border p-2 mb-4 rounded" required>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM pangkat");
            while ($d = mysqli_fetch_assoc($q)) {
            echo "<option value='{$d['id']}'>{$d['nama_pangkat']}</option>";
            }
            ?>
        </select>

        <label class="block mb-2">Jabatan</label>
        <select name="jabatan_id" id="jabatan_id" class="w-full border p-2 mb-4 rounded" required>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM jabatan");
            while ($d = mysqli_fetch_assoc($q)) {
            echo "<option value='{$d['id']}'>{$d['nama_jabatan']}</option>";
            }
            ?>
        </select>

        <label class="block mb-2">Subsatker</label>
        <select name="subsatker_id" id="subsatker_id" class="w-full border p-2 mb-4 rounded" required>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM subsatker");
            while ($d = mysqli_fetch_assoc($q)) {
            echo "<option value='{$d['id']}'>{$d['nama_subsatker']}</option>";
            }
            ?>
        </select>

        <div class="flex justify-end mt-4">
            <button type="button" onclick="tutupModal()" class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </div>
        </form>
    </div>
    </div>

    </main>
    <?php include 'includes/footer.php'; ?>
  </div>

  <script>
  function bukaModalTambah() {
    document.getElementById('modalJudul').innerText = 'Tambah Anggota';
    document.getElementById('aksi').value = 'tambah';
    document.getElementById('anggotaId').value = '';
    document.getElementById('nama').value = '';
    document.getElementById('pangkat_id').value = '';
    document.getElementById('jabatan_id').value = '';
    document.getElementById('subsatker_id').value = '';
    document.getElementById('modalAnggota').classList.remove('hidden');
    document.getElementById('modalAnggota').classList.add('flex');
  }

  function bukaModalEdit(data) {
    document.getElementById('modalJudul').innerText = 'Edit Anggota';
    document.getElementById('aksi').value = 'edit';
    document.getElementById('anggotaId').value = data.id;
    document.getElementById('nama').value = data.nama;
    document.getElementById('pangkat_id').value = data.pangkat_id;
    document.getElementById('jabatan_id').value = data.jabatan_id;
    document.getElementById('subsatker_id').value = data.subsatker_id;
    document.getElementById('modalAnggota').classList.remove('hidden');
    document.getElementById('modalAnggota').classList.add('flex');
  }

  function tutupModal() {
    document.getElementById('modalAnggota').classList.add('hidden');
  }
</script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
