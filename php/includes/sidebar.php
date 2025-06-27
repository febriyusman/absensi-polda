<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div id="sidebar" class="w-64 bg-[#9e8a73] min-h-screen px-4 py-6 flex flex-col justify-between transition-all duration-300">
  <div>
    <div class="flex items-center gap-2 mb-8">
      <img src="../foto/logopolda.png" class="w-10 h-10" alt="Logo Polda">
      <h1 class="text-white font-bold text-lg">Absensi Apel</h1>
    </div>

    <nav class="space-y-2 text-sm">
      <a href="dashboard.php" 
         class="block py-2 px-3 rounded text-white hover:bg-[#7a6a58] <?= ($current_page == 'dashboard.php') ? 'bg-[#7a6a58]' : '' ?>">
         📊 Dashboard
      </a>

      <a href="absensi.php" 
         class="block py-2 px-3 rounded text-white hover:bg-[#7a6a58] <?= (in_array($current_page, ['riwayat-absensi.php', 'absensi-hari-ini.php', 'absensi-detail.php'])) ? 'bg-[#7a6a58]' : '' ?>">
         📝 Riwayat Absensi
      </a>
      
      <a href="data-anggota.php" 
         class="block py-2 px-3 rounded text-white hover:bg-[#7a6a58] <?= ($current_page == 'data-anggota.php') ? 'bg-[#7a6a58]' : '' ?>">
         👥 Data Anggota
      </a>
      
      <a href="master-referensi.php" 
         class="block py-2 px-3 rounded text-white hover:bg-[#7a6a58] <?= ($current_page == 'master-referensi.php') ? 'bg-[#7a6a58]' : '' ?>">
         ⚙️ Master Referensi
      </a>
    </nav>
  </div>

  <div class="relative text-sm text-white mt-10">
    <button onclick="toggleAccount()" class="w-full text-left py-2 px-3 bg-[#7a6a58] rounded hover:bg-[#655745] flex items-center justify-between">
      <span>👤 <?= htmlspecialchars($_SESSION['admin']) ?></span>
      <svg id="arrowIcon" class="w-4 h-4 ml-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div id="accountMenu" class="hidden absolute bottom-full mb-2 bg-white shadow-lg rounded text-[#4b3b2f] w-full z-10">
      <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50 rounded">🚪 Logout</a>
    </div>
  </div>
</div>

<script>
// Fungsi untuk toggle menu akun
function toggleAccount() {
  const menu = document.getElementById('accountMenu');
  const icon = document.getElementById('arrowIcon');
  menu.classList.toggle('hidden');
  icon.classList.toggle('rotate-180');
}

// Menutup menu jika diklik di luar area sidebar
document.addEventListener('click', function(event) {
  const sidebar = document.getElementById('sidebar');
  const menu = document.getElementById('accountMenu');
  const isClickInside = sidebar.contains(event.target);

  if (!isClickInside && !menu.classList.contains('hidden')) {
    menu.classList.add('hidden');
    document.getElementById('arrowIcon').classList.remove('rotate-180');
  }
});
</script>