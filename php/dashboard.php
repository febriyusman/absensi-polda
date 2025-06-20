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

        </div>
      </div>
    </main>

    <?php include 'includes/footer.php'; ?>

  </div>

  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
