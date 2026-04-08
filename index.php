<?php
// File: index.php
// Memanggil file-file OOP yang sudah dipisah
require_once 'Mahasiswa.php';
require_once 'Dosen.php';
require_once 'MataKuliah.php';

session_start();

// Inisialisasi Database Dummy (Session)
if (!isset($_SESSION['mahasiswa'])) $_SESSION['mahasiswa'] = [];
if (!isset($_SESSION['matakuliah'])) $_SESSION['matakuliah'] = [];

// Routing Tab Aktif
$tab = $_GET['tab'] ?? 'mahasiswa';

// ==========================================
// PROSES DELETE DATA (GET REQUEST)
// ==========================================
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'delete_mhs' && isset($_GET['nim'])) {
        unset($_SESSION['mahasiswa'][$_GET['nim']]);
        header("Location: index.php?tab=mahasiswa");
        exit;
    }
    
    if ($action === 'delete_mk' && isset($_GET['kode'])) {
        unset($_SESSION['matakuliah'][$_GET['kode']]);
        header("Location: index.php?tab=matakuliah");
        exit;
    }

    if ($action === 'reset_nilai' && isset($_GET['nim'])) {
        if (isset($_SESSION['mahasiswa'][$_GET['nim']])) {
            $_SESSION['mahasiswa'][$_GET['nim']]->resetNilai();
        }
        header("Location: index.php?tab=laporan");
        exit;
    }
}

// ==========================================
// PROSES TAMBAH DATA (POST SUBMISSION)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_mhs'])) {
        $mhs = new Mahasiswa($_POST['nim'], $_POST['nama'], $_POST['email'], $_POST['jurusan']);
        $_SESSION['mahasiswa'][$_POST['nim']] = $mhs;
        header("Location: index.php?tab=mahasiswa");
        exit;
    } 
    elseif (isset($_POST['add_mk'])) {
        $mk = new MataKuliah($_POST['kode'], $_POST['nama_mk'], $_POST['sks']);
        $_SESSION['matakuliah'][$_POST['kode']] = $mk;
        header("Location: index.php?tab=matakuliah");
        exit;
    } 
    elseif (isset($_POST['add_nilai'])) {
        $nim = $_POST['nim'];
        $kode_mk = $_POST['kode_mk'];
        $nilai = (int)$_POST['nilai'];
        
        if (isset($_SESSION['mahasiswa'][$nim]) && isset($_SESSION['matakuliah'][$kode_mk])) {
            $mhs = $_SESSION['mahasiswa'][$nim];
            $mk = $_SESSION['matakuliah'][$kode_mk];
            $mhs->tambahNilai($mk, $nilai);
            $_SESSION['mahasiswa'][$nim] = $mhs; 
        }
        header("Location: index.php?tab=nilai");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="style.css">

<header class="mb-12 text-center">
    <div class="bg-animate"></div> <div class="inline-block mb-3 px-4 py-1 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-sm font-semibold tracking-widest uppercase">
        Politeknik Negeri Jember
    </div>
    <h1 class="text-6xl font-extrabold glow-text-polije mb-3 tracking-tight">
        SIAKAD <span class="text-white">V2.0</span>
    </h1>
    <p class="text-slate-400 font-light text-lg">Sistem Informasi Akademik • Bisnis Digital</p>
</header>

<script src="script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD POLIJE - Bisnis Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.85) 50%, rgba(0, 0, 0, 0.95) 100%),
                url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.1);
        }

        .glass-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            border-color: rgba(250, 204, 21, 0.3);
            box-shadow: 0 10px 30px -10px rgba(250, 204, 21, 0.2);
        }

        .modern-input {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .modern-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
            background: rgba(0, 0, 0, 0.6);
        }

        .glow-text-polije {
            background: linear-gradient(to right, #60a5fa, #facc15);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(96, 165, 250, 0.3);
        }
    </style>
</head>
<body class="text-slate-200 antialiased py-10">

    <div class="max-w-6xl mx-auto px-6 relative z-10">
        

        <nav class="flex flex-wrap justify-center gap-4 mb-10">
            <a href="?tab=mahasiswa" class="px-7 py-3.5 rounded-2xl font-semibold text-sm tracking-wide transition-all duration-300 <?= $tab == 'mahasiswa' ? 'bg-blue-600 text-white shadow-[0_0_25px_rgba(37,99,235,0.6)] scale-105' : 'glass-panel text-slate-300 hover:bg-white/10 hover:text-white' ?>">👨‍🎓 Manajemen Mahasiswa</a>
            <a href="?tab=matakuliah" class="px-7 py-3.5 rounded-2xl font-semibold text-sm tracking-wide transition-all duration-300 <?= $tab == 'matakuliah' ? 'bg-amber-500 text-slate-900 shadow-[0_0_25px_rgba(245,158,11,0.6)] scale-105' : 'glass-panel text-slate-300 hover:bg-white/10 hover:text-white' ?>">📚 Mata Kuliah</a>
            <a href="?tab=nilai" class="px-7 py-3.5 rounded-2xl font-semibold text-sm tracking-wide transition-all duration-300 <?= $tab == 'nilai' ? 'bg-indigo-500 text-white shadow-[0_0_25px_rgba(99,102,241,0.6)] scale-105' : 'glass-panel text-slate-300 hover:bg-white/10 hover:text-white' ?>">📝 Input Nilai</a>
            <a href="?tab=laporan" class="px-7 py-3.5 rounded-2xl font-semibold text-sm tracking-wide transition-all duration-300 <?= $tab == 'laporan' ? 'bg-emerald-500 text-white shadow-[0_0_25px_rgba(16,185,129,0.6)] scale-105' : 'glass-panel text-slate-300 hover:bg-white/10 hover:text-white' ?>">🖨️ Cetak KHS & IPK</a>
        </nav>

        <main class="glass-panel rounded-[2rem] p-10">
            
            <?php if ($tab === 'mahasiswa'): ?>
                <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-4">
                    <h2 class="text-3xl font-bold text-blue-400">Database Mahasiswa</h2>
                    <span class="text-sm bg-blue-500/20 text-blue-300 py-1 px-3 rounded-lg border border-blue-500/30">Total: <?= count($_SESSION['mahasiswa']) ?> Data</span>
                </div>
                
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    <input type="text" name="nim" placeholder="NIM (Misal: E322...)" required class="modern-input rounded-xl p-4">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required class="modern-input rounded-xl p-4">
                    <input type="email" name="email" placeholder="Email Mahasiswa" required class="modern-input rounded-xl p-4">
                    <input type="text" name="jurusan" placeholder="Program Studi" value="Bisnis Digital" required class="modern-input rounded-xl p-4">
                    <button type="submit" name="add_mhs" class="col-span-1 md:col-span-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-bold py-4 rounded-xl shadow-[0_0_20px_rgba(37,99,235,0.3)] transition-all">
                        + Daftarkan Mahasiswa Baru
                    </button>
                </form>

                <h3 class="text-xl font-semibold mb-6 text-slate-300">Daftar Mahasiswa Terdaftar:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($_SESSION['mahasiswa'] as $mhs): ?>
                        <div class="glass-card p-5 rounded-2xl flex flex-col justify-between relative group">
                            <a href="?action=delete_mhs&nim=<?= $mhs->getId() ?>" onclick="return confirm('Yakin ingin menghapus data <?= $mhs->getNama() ?>?')" class="absolute top-4 right-4 text-slate-500 hover:text-red-500 transition-colors" title="Hapus Mahasiswa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </a>
                            
                            <div class="pr-6">
                                <span class="text-xs font-bold text-amber-400 tracking-wider mb-1 block"><?= $mhs->getId() ?></span>
                                <h4 class="text-lg font-bold text-white mb-2"><?= $mhs->getNama() ?></h4>
                            </div>
                            <div class="mt-4 flex justify-between items-center border-t border-white/10 pt-4">
                                <span class="text-sm text-slate-400"><?= $mhs->getRole() ?></span>
                                <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($_SESSION['mahasiswa'])) echo "<p class='text-slate-500 col-span-full italic'>Belum ada data mahasiswa terdaftar di sistem.</p>"; ?>
                </div>

            <?php elseif ($tab === 'matakuliah'): ?>
                <h2 class="text-3xl font-bold mb-8 text-amber-400 border-b border-white/10 pb-4">Kurikulum Mata Kuliah</h2>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <input type="text" name="kode" placeholder="Kode MK (Contoh: BD101)" required class="modern-input rounded-xl p-4 focus:border-amber-500">
                    <input type="text" name="nama_mk" placeholder="Nama Mata Kuliah" required class="modern-input rounded-xl p-4 focus:border-amber-500">
                    <input type="number" name="sks" placeholder="Jumlah SKS" required min="1" max="6" class="modern-input rounded-xl p-4 focus:border-amber-500">
                    <button type="submit" name="add_mk" class="col-span-1 md:col-span-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-900 font-bold py-4 rounded-xl shadow-[0_0_20px_rgba(245,158,11,0.3)] transition-all">
                        + Tambah Mata Kuliah
                    </button>
                </form>

                <h3 class="text-xl font-semibold mb-6 text-slate-300">Daftar Mata Kuliah Tersedia:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($_SESSION['matakuliah'] as $mk): ?>
                        <div class="glass-card p-5 rounded-2xl flex items-center justify-between relative group">
                            <div class="pr-10">
                                <h4 class="font-bold text-xl text-amber-300"><?= $mk->getKode() ?></h4>
                                <p class="text-slate-300 mt-1"><?= $mk->getNamaMK() ?></p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="bg-black/50 px-4 py-2 rounded-lg border border-white/10">
                                    <span class="text-lg font-bold text-white"><?= $mk->getSks() ?></span>
                                    <span class="text-xs text-slate-400 ml-1">SKS</span>
                                </div>
                                <a href="?action=delete_mk&kode=<?= $mk->getKode() ?>" onclick="return confirm('Hapus mata kuliah ini?')" class="text-slate-500 hover:text-red-500 transition-colors" title="Hapus MK">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($_SESSION['matakuliah'])) echo "<p class='text-slate-500 col-span-2 italic'>Database mata kuliah masih kosong.</p>"; ?>
                </div>

            <?php elseif ($tab === 'nilai'): ?>
                <h2 class="text-3xl font-bold mb-8 text-indigo-400 border-b border-white/10 pb-4">Sistem Input Nilai</h2>
                
                <?php if (empty($_SESSION['mahasiswa']) || empty($_SESSION['matakuliah'])): ?>
                    <div class="bg-red-500/10 border border-red-500/50 p-6 rounded-2xl flex items-center gap-4">
                        <div class="bg-red-500/20 p-3 rounded-full text-red-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-red-300 font-bold text-lg">Akses Ditolak</h4>
                            <p class="text-red-200/70 text-sm">Tambahkan minimal 1 Mahasiswa dan 1 Mata Kuliah terlebih dahulu di menu sebelumnya.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="POST" class="space-y-6 max-w-2xl mx-auto glass-card p-8 rounded-3xl">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Mahasiswa</label>
                            <select name="nim" required class="w-full modern-input rounded-xl p-4 appearance-none focus:border-indigo-500">
                                <?php foreach ($_SESSION['mahasiswa'] as $mhs): ?>
                                    <option value="<?= $mhs->getId() ?>" class="bg-slate-900"><?= $mhs->getId() ?> - <?= $mhs->getNama() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Mata Kuliah</label>
                            <select name="kode_mk" required class="w-full modern-input rounded-xl p-4 appearance-none focus:border-indigo-500">
                                <?php foreach ($_SESSION['matakuliah'] as $mk): ?>
                                    <option value="<?= $mk->getKode() ?>" class="bg-slate-900"><?= $mk->getKode() ?> - <?= $mk->getNamaMK() ?> (<?= $mk->getSks() ?> SKS)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nilai Akhir (0-100)</label>
                            <input type="number" name="nilai" required min="0" max="100" placeholder="Contoh: 85" class="w-full modern-input rounded-xl p-4 focus:border-indigo-500 text-2xl font-bold text-center">
                        </div>
                        <button type="submit" name="add_nilai" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-4 rounded-xl shadow-[0_0_20px_rgba(99,102,241,0.4)] transition-all mt-4">
                            Simpan Nilai
                        </button>
                    </form>
                <?php endif; ?>

            <?php elseif ($tab === 'laporan'): ?>
                <h2 class="text-3xl font-bold mb-8 text-emerald-400 border-b border-white/10 pb-4">Generate KHS & Transkrip</h2>
                
                <?php 
                if (empty($_SESSION['mahasiswa'])) {
                    echo "<div class='text-center py-20'><p class='text-slate-500 text-lg'>Tidak ada data akademik untuk di-generate.</p></div>";
                } else {
                    echo "<div class='grid grid-cols-1 lg:grid-cols-2 gap-8'>";
                    foreach ($_SESSION['mahasiswa'] as $mhs) {
                        echo $mhs->cetakLaporan(); 
                    }
                    echo "</div>";
                }
                ?>

            <?php endif; ?>

        </main>
        
        <footer class="mt-12 text-center text-sm text-slate-500">
            <p>&copy; 2026 SIAKAD V2.0 - Politeknik Negeri Jember. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>