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
  <title>Tambah Anggota</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#fff2e3] text-[#4b3b2f] min-h-screen flex">

  <?php include 'includes/sidebar.php'; ?>

  <div class="flex-1 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="p-6 flex-1">
      <div class="bg-[#e4d3bb] p-6 rounded-xl shadow max-w-xl mx-auto">
        <h2 class="text-lg font-semibold mb-4">➕ Tambah Anggota</h2>
        <form action="proses-tambah-anggota.php" method="POST" class="space-y-4">

          <div>
            <label class="block text-sm mb-1">Nama Lengkap</label>
            <input type="text" name="nama" required class="w-full px-4 py-2 rounded bg-[#fffaf4] border border-[#cbb89f] focus:outline-none">
          </div>

          <div>
            <label class="block text-sm mb-1">Pangkat</label>
            <select name="pangkat_id" required class="w-full px-4 py-2 rounded bg-[#fffaf4] border border-[#cbb89f]">
              <option value="">-- Pilih Pangkat --</option>
              <?php
              $pangkat = mysqli_query($conn, "SELECT * FROM pangkat");
              while ($row = mysqli_fetch_assoc($pangkat)) {
                echo "<option value='{$row['id']}'>{$row['nama_pangkat']}</option>";
              }
              ?>
            </select>
          </div>

          <div>
            <label class="block text-sm mb-1">Jabatan</label>
            <select name="jabatan_id" required class="w-full px-4 py-2 rounded bg-[#fffaf4] border border-[#cbb89f]">
              <option value="">-- Pilih Jabatan --</option>
              <?php
              $jabatan = mysqli_query($conn, "SELECT * FROM jabatan");
              while ($row = mysqli_fetch_assoc($jabatan)) {
                echo "<option value='{$row['id']}'>{$row['nama_jabatan']}</option>";
              }
              ?>
            </select>
          </div>

          <div>
            <label class="block text-sm mb-1">Subsatker</label>
            <select name="subsatker_id" required class="w-full px-4 py-2 rounded bg-[#fffaf4] border border-[#cbb89f]">
              <option value="">-- Pilih Subsatker --</option>
              <?php
              $subsatker = mysqli_query($conn, "SELECT * FROM subsatker");
              while ($row = mysqli_fetch_assoc($subsatker)) {
                echo "<option value='{$row['id']}'>{$row['nama_subsatker']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="text-right">
            <button type="submit" class="bg-[#9e8a73] text-white px-6 py-2 rounded hover:bg-[#7a6a58]">Simpan</button>
          </div>

        </form>
      </div>
    </main>

    <?php include 'includes/footer.php'; ?>
  </div>

  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
