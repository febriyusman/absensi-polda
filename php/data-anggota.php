<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.html");
    exit;
}
include 'koneksi.php';

// --- AWAL LOGIKA PAGINATION ---

// 1. Tentukan jumlah data per halaman
$per_halaman = isset($_GET['per_halaman']) ? (int)$_GET['per_halaman'] : 10;
if ($per_halaman === 0) { // Jika "Semua" dipilih
    $per_halaman = 999999; // Set angka yang sangat besar
}

// 2. Tentukan halaman saat ini
$halaman_aktif = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman_aktif - 1) * $per_halaman;

// 3. Buat klausa WHERE untuk filter (sama seperti kode Anda)
$whereClauses = [];
if (isset($_GET['search_nama']) && $_GET['search_nama'] != '') {
    $whereClauses[] = "a.nama LIKE '%" . mysqli_real_escape_string($conn, $_GET['search_nama']) . "%'";
}
if (isset($_GET['search_pangkat']) && $_GET['search_pangkat'] != '') {
    $whereClauses[] = "a.pangkat_id = " . (int)$_GET['search_pangkat'];
}
if (isset($_GET['search_jabatan']) && $_GET['search_jabatan'] != '') {
    $whereClauses[] = "a.jabatan_id = " . (int)$_GET['search_jabatan'];
}
if (isset($_GET['search_subsatker']) && $_GET['search_subsatker'] != '') {
    $whereClauses[] = "a.subsatker_id = " . (int)$_GET['search_subsatker'];
}

$whereQuery = '';
if (!empty($whereClauses)) {
    $whereQuery = 'WHERE ' . implode(' AND ', $whereClauses);
}

// 4. Hitung total data untuk pagination
$query_total = "SELECT COUNT(*) as total FROM anggota a $whereQuery";
$hasil_total = mysqli_query($conn, $query_total);
$data_total = mysqli_fetch_assoc($hasil_total)['total'];
$jumlah_halaman = ceil($data_total / $per_halaman);


// 5. Query utama untuk mengambil data dengan LIMIT
$query = "SELECT a.id, a.nama, p.nama_pangkat, j.nama_jabatan, s.nama_subsatker, a.pangkat_id, a.jabatan_id, a.subsatker_id
          FROM anggota a 
          LEFT JOIN pangkat p ON a.pangkat_id = p.id 
          LEFT JOIN jabatan j ON a.jabatan_id = j.id 
          LEFT JOIN subsatker s ON a.subsatker_id = s.id 
          $whereQuery
          LIMIT $mulai, $per_halaman";

$result = mysqli_query($conn, $query);

// --- AKHIR LOGIKA PAGINATION ---
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

            // Menangani hapus anggota
            if (isset($_GET['hapus_id'])) {
                $id = $_GET['hapus_id'];
                // Hapus data absensi yang mengacu pada anggota
                $deleteAbsensi = mysqli_query($conn, "DELETE FROM absensi WHERE anggota_id = '$id'");
                if ($deleteAbsensi) {
                    // Hapus anggota setelah absensi terhapus
                    $deleteAnggota = mysqli_query($conn, "DELETE FROM anggota WHERE id = '$id'");
                    if ($deleteAnggota) {
                        echo "<script>alert('Anggota berhasil dihapus!'); window.location.href='data-anggota.php';</script>";
                    } else {
                        echo "<script>alert('Gagal menghapus anggota.'); window.location.href='data-anggota.php';</script>";
                    }
                } else {
                    echo "<script>alert('Gagal menghapus data absensi.'); window.location.href='data-anggota.php';</script>";
                }
            }
            ?>

            <form method="GET" class="mb-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                  <div>
                      <label for="search_nama" class="block mb-1 text-sm font-medium">Nama Anggota:</label>
                      <input type="text" name="search_nama" id="search_nama" class="w-full px-3 py-2 border rounded" value="<?= isset($_GET['search_nama']) ? htmlspecialchars($_GET['search_nama']) : '' ?>" />
                  </div>

                  <div>
                      <label for="search_pangkat" class="block mb-1 text-sm font-medium">Pangkat:</label>
                      <select name="search_pangkat" id="search_pangkat" class="w-full px-3 py-2 border rounded">
                          <option value="">Semua</option>
                          <?php
                          $pangkatQuery = mysqli_query($conn, "SELECT * FROM pangkat");
                          while ($pangkat = mysqli_fetch_assoc($pangkatQuery)) {
                              $selected = (isset($_GET['search_pangkat']) && $_GET['search_pangkat'] == $pangkat['id']) ? 'selected' : '';
                              echo "<option value='{$pangkat['id']}' $selected>{$pangkat['nama_pangkat']}</option>";
                          }
                          ?>
                      </select>
                  </div>

                  <div>
                      <label for="search_jabatan" class="block mb-1 text-sm font-medium">Jabatan:</label>
                      <select name="search_jabatan" id="search_jabatan" class="w-full px-3 py-2 border rounded">
                          <option value="">Semua</option>
                          <?php
                          $jabatanQuery = mysqli_query($conn, "SELECT * FROM jabatan");
                          while ($jabatan = mysqli_fetch_assoc($jabatanQuery)) {
                              $selected = (isset($_GET['search_jabatan']) && $_GET['search_jabatan'] == $jabatan['id']) ? 'selected' : '';
                              echo "<option value='{$jabatan['id']}' $selected>{$jabatan['nama_jabatan']}</option>";
                          }
                          ?>
                      </select>
                  </div>

                  <div>
                      <label for="search_subsatker" class="block mb-1 text-sm font-medium">Subsatker:</label>
                      <select name="search_subsatker" id="search_subsatker" class="w-full px-3 py-2 border rounded">
                          <option value="">Semua</option>
                          <?php
                          $subsatkerQuery = mysqli_query($conn, "SELECT * FROM subsatker");
                          while ($subsatker = mysqli_fetch_assoc($subsatkerQuery)) {
                              $selected = (isset($_GET['search_subsatker']) && $_GET['search_subsatker'] == $subsatker['id']) ? 'selected' : '';
                              echo "<option value='{$subsatker['id']}' $selected>{$subsatker['nama_subsatker']}</option>";
                          }
                          ?>
                      </select>
                  </div>
              </div>
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tampilkan</button>
            </form>

            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-bold">Data Anggota</h2>
              <button onclick="bukaModalTambah()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Tambah Anggota</button>
            </div>

            <div class="flex items-center mb-4">
                <form method="GET" class="flex items-center">
                    <label for="per_halaman" class="mr-2">Tampilkan:</label>
                    <select name="per_halaman" id="per_halaman" class="px-3 py-1 border rounded" onchange="this.form.submit()">
                        <option value="10" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '10') ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '25') ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '50') ? 'selected' : '' ?>>50</option>
                        <option value="0" <?= (isset($_GET['per_halaman']) && $_GET['per_halaman'] == '0') ? 'selected' : '' ?>>Semua</option>
                    </select>
                    <input type="hidden" name="search_nama" value="<?= isset($_GET['search_nama']) ? htmlspecialchars($_GET['search_nama']) : '' ?>">
                    <input type="hidden" name="search_pangkat" value="<?= isset($_GET['search_pangkat']) ? htmlspecialchars($_GET['search_pangkat']) : '' ?>">
                    <input type="hidden" name="search_jabatan" value="<?= isset($_GET['search_jabatan']) ? htmlspecialchars($_GET['search_jabatan']) : '' ?>">
                    <input type="hidden" name="search_subsatker" value="<?= isset($_GET['search_subsatker']) ? htmlspecialchars($_GET['search_subsatker']) : '' ?>">
                </form>
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
                if(mysqli_num_rows($result) > 0) {
                    $no = $mulai + 1;
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
                    <a href="?hapus_id=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>Tidak ada data ditemukan.</td></tr>";
                }
                ?>
            </tbody>
            </table>

            <div class="mt-4 flex justify-between items-center">
                <span class="text-sm">Menampilkan <?= mysqli_num_rows($result) ?> dari <?= $data_total ?> data</span>
                <div class="flex">
                    <?php if ($halaman_aktif > 1) : ?>
                        <a href="?halaman=<?= $halaman_aktif - 1 ?>&per_halaman=<?= $per_halaman == 999999 ? 0 : $per_halaman ?>&search_nama=<?= $_GET['search_nama'] ?? '' ?>&search_pangkat=<?= $_GET['search_pangkat'] ?? '' ?>&search_jabatan=<?= $_GET['search_jabatan'] ?? '' ?>&search_subsatker=<?= $_GET['search_subsatker'] ?? '' ?>" class="px-3 py-1 bg-white border rounded-l-md hover:bg-gray-200">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                        <a href="?halaman=<?= $i ?>&per_halaman=<?= $per_halaman == 999999 ? 0 : $per_halaman ?>&search_nama=<?= $_GET['search_nama'] ?? '' ?>&search_pangkat=<?= $_GET['search_pangkat'] ?? '' ?>&search_jabatan=<?= $_GET['search_jabatan'] ?? '' ?>&search_subsatker=<?= $_GET['search_subsatker'] ?? '' ?>" class="px-3 py-1 border-t border-b <?= $i == $halaman_aktif ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-200' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                        <a href="?halaman=<?= $halaman_aktif + 1 ?>&per_halaman=<?= $per_halaman == 999999 ? 0 : $per_halaman ?>&search_nama=<?= $_GET['search_nama'] ?? '' ?>&search_pangkat=<?= $_GET['search_pangkat'] ?? '' ?>&search_jabatan=<?= $_GET['search_jabatan'] ?? '' ?>&search_subsatker=<?= $_GET['search_subsatker'] ?? '' ?>" class="px-3 py-1 bg-white border rounded-r-md hover:bg-gray-200">Next</a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
      </div>

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
    
    // Perbaikan: Ambil id dari data, bukan nama relasi
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