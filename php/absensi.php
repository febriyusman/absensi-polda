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
            <!-- Tombol tambah anggota -->
<div class="flex justify-between items-center mb-4">
  <h2 class="text-lg font-semibold">📝 Input Absensi</h2>
  <button onclick="toggleAnggotaForm()" class="bg-[#7a6a58] text-white px-4 py-2 rounded hover:bg-[#5e4f3c]">➕ Tambah Anggota Baru</button>
</div>

<!-- Form tambah anggota baru (toggleable) -->
<div id="formTambahAnggota" class="bg-[#fefae0] p-4 rounded-lg border border-[#cbb89f] mb-6 hidden">
  <h3 class="text-md font-semibold mb-3">Tambah Anggota Baru</h3>
  <form action="proses-tambah-anggota.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm">Nama</label>
      <input name="nama" type="text" required class="w-full rounded border px-3 py-2 bg-white" />
    </div>
    <div>
      <label class="text-sm">Pangkat</label>
      <select name="pangkat_id" required class="w-full rounded border px-3 py-2 bg-white">
        <option value="">-- Pilih Pangkat --</option>
        <?php
        $pangkat = mysqli_query($conn, "SELECT * FROM pangkat");
        while ($p = mysqli_fetch_assoc($pangkat)) {
          echo "<option value='{$p['id']}'>{$p['nama_pangkat']}</option>";
        }
        ?>
      </select>
    </div>
    <div>
      <label class="text-sm">Jabatan</label>
      <select name="jabatan_id" required class="w-full rounded border px-3 py-2 bg-white">
        <option value="">-- Pilih Jabatan --</option>
        <?php
        $jabatan = mysqli_query($conn, "SELECT * FROM jabatan");
        while ($j = mysqli_fetch_assoc($jabatan)) {
          echo "<option value='{$j['id']}'>{$j['nama_jabatan']}</option>";
        }
        ?>
      </select>
    </div>
    <div>
      <label class="text-sm">Subsatker</label>
      <select name="subsatker_id" required class="w-full rounded border px-3 py-2 bg-white">
        <option value="">-- Pilih Subsatker --</option>
        <?php
        $subsatker = mysqli_query($conn, "SELECT * FROM subsatker");
        while ($s = mysqli_fetch_assoc($subsatker)) {
          echo "<option value='{$s['id']}'>{$s['nama_subsatker']}</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-span-2 text-right">
      <button type="submit" class="bg-[#9e8a73] text-white px-6 py-2 rounded hover:bg-[#7a6a58]">Simpan</button>
    </div>
  </form>
</div>


        </div>
      </div>
    </main>

    <?php include 'includes/footer.php'; ?>

  </div>

  <script src="../assets/js/dashboard.js"></script>
  <script>
  function toggleAnggotaForm() {
    const form = document.getElementById('formTambahAnggota');
    form.classList.toggle('hidden');
    }
  </script>

</body>
</html>
