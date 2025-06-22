<!-- SIDEBAR -->
<div id="sidebar" class="w-64 bg-[#9e8a73] min-h-screen px-4 py-6 flex flex-col justify-between transition-all duration-300">
  <div>
    <div class="flex items-center gap-2 mb-8">
      <img src="../foto/logopolda.png" class="w-10 h-10" alt="Logo Polda">
      <h1 class="text-white font-bold text-lg">Absensi Apel</h1>
    </div>
        <nav class="space-y-2 text-sm">
        <a href="dashboard.php" class="block py-2 px-3 rounded hover:bg-[#7a6a58] text-white">📊 Dashboard</a>
        <a href="absensi.php" class="block py-2 px-3 rounded hover:bg-[#7a6a58] text-white">📝 Input Absensi</a>
        <a href="data-anggota.php" class="block py-2 px-3 rounded hover:bg-[#7a6a58] text-white">👥 Data Anggota</a>
        <!-- <a href="rekap-absensi.php" class="block py-2 px-3 rounded hover:bg-[#7a6a58] text-white">📁 Rekap Absensi</a> -->
        <a href="master-referensi.php" class="block py-2 px-3 rounded hover:bg-[#7a6a58] text-white">⚙️ Master Referensi</a>
        </nav>

  </div>

  <div class="relative text-sm text-white mt-10">
    <button onclick="toggleAccount()" class="w-full text-left py-2 px-3 bg-[#7a6a58] rounded hover:bg-[#655745] flex items-center justify-between">
      <span>👤 <?php echo $_SESSION['admin']; ?></span>
      <svg class="w-4 h-4 ml-2 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 15l-7-7-7 7" />
      </svg>
    </button>
    <div id="accountMenu" class="hidden absolute bottom-full mb-2 bg-white shadow rounded text-[#4b3b2f] w-full z-10">
      <a href="#" class="block px-4 py-2 hover:bg-gray-100">⚙️ Setting</a>
      <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">🚪 Logout</a>
    </div>
  </div>
</div>
