<?php
// File: index.php
require_once 'Mahasiswa.php';
require_once 'Dosen.php';
require_once 'MataKuliah.php';

session_start();

// ==========================================
// LOGIKA LOGIN & LOGOUT
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_display'] = htmlspecialchars($_POST['username']);
        header("Location: index.php");
        exit;
    }
}

// Cek Status Login
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

// ==========================================
// INISIALISASI DATABASE DUMMY (SESSION)
// ==========================================
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

// Statistik
$totalMhs = count($_SESSION['mahasiswa']);
$totalMK = count($_SESSION['matakuliah']);
$totalNilai = array_sum(array_map(fn($m) => count(array_filter((array)$m, fn($v) => is_array($v))), $_SESSION['mahasiswa']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD PRO - Bisnis Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="text-slate-200 antialiased overflow-x-hidden">

    <div class="bg-animate"></div>
    <div class="cursor-glow"></div>

    <?php if (!$isLoggedIn): ?>
        <div class="min-h-screen flex items-center justify-center px-4 relative z-20">
            <div class="glass-panel max-w-md w-full p-10 rounded-[2.5rem] border border-white/10 shadow-2xl">
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl mx-auto flex items-center justify-center text-4xl mb-6 shadow-lg shadow-blue-500/20">🚀</div>
                    <h1 class="text-4xl font-extrabold glow-text-polije tracking-tighter mb-2">SIAKAD PRO</h1>
                    <span class="badge-pill border" style="background:rgba(59,130,246,0.1);border-color:rgba(59,130,246,0.25);color:#93c5fd">
                        Bisnis Digital
                    </span>
                </div>

                <form method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 uppercase ml-1 tracking-wider">Username</label>
                        <input type="text" name="username" required class="modern-input w-full rounded-2xl p-4 text-sm" placeholder="Masukkan username">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-400 uppercase ml-1 tracking-wider">Password</label>
                        <input type="password" name="password" required class="modern-input w-full rounded-2xl p-4 text-sm" placeholder="••••••••">
                    </div>
                    <button type="submit" name="login" class="w-full py-4 mt-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-all shadow-lg shadow-blue-600/30 hover:scale-[1.02] active:scale-95 flex justify-center items-center gap-2">
                        <span>Akses Dashboard</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>

    <?php else: ?>
        <div class="max-w-6xl mx-auto px-4 py-10 relative z-10">
            
            <header class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6 bg-white/5 p-6 rounded-[2rem] border border-white/5 backdrop-blur-md">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-3xl shadow-lg shadow-blue-500/20">🎓</div>
                    <div>
                        <h1 class="text-3xl font-extrabold glow-text-polije leading-none mb-1">SIAKAD</h1>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-bold">Bisnis Digital Polije</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-xs text-slate-400 leading-none mb-1">Status: <span class="text-emerald-400">Online</span></p>
                        <p class="text-sm font-bold text-white"><?= $_SESSION['user_display'] ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold border border-blue-500/30">
                        <?= strtoupper(substr($_SESSION['user_display'], 0, 1)) ?>
                    </div>
                    <div class="h-8 w-px bg-white/10 mx-2"></div>
                    <a href="?action=logout" class="flex items-center gap-2 p-2 px-4 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 rounded-xl text-red-400 transition-all group" title="Logout">
                        <span class="text-sm font-semibold hidden sm:block">Keluar</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </a>
                </div>
            </header>

            <div class="grid grid-cols-3 gap-4 mb-8 no-print">
                <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                    <div class="p-3 bg-blue-500/20 text-blue-400 rounded-xl text-xl">👨‍🎓</div>
                    <div><div class="text-2xl font-bold text-white"><?= $totalMhs ?></div><div class="text-xs text-slate-500">Mahasiswa</div></div>
                </div>
                <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                    <div class="p-3 bg-amber-500/20 text-amber-400 rounded-xl text-xl">📚</div>
                    <div><div class="text-2xl font-bold text-white"><?= $totalMK ?></div><div class="text-xs text-slate-500">Mata Kuliah</div></div>
                </div>
                <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                    <div class="p-3 bg-indigo-500/20 text-indigo-400 rounded-xl text-xl">📝</div>
                    <div><div class="text-2xl font-bold text-white"><?= $totalNilai ?></div><div class="text-xs text-slate-500">Total Nilai</div></div>
                </div>
            </div>

            <nav class="flex flex-wrap gap-3 mb-8 no-print">
                <?php
                $tabs = [
                    ['id' => 'mahasiswa', 'label' => 'Mahasiswa', 'icon' => '👨‍🎓', 'active' => 'bg-blue-600 text-white shadow-lg shadow-blue-500/30'],
                    ['id' => 'matakuliah', 'label' => 'Mata Kuliah', 'icon' => '📚', 'active' => 'bg-amber-600 text-white shadow-lg shadow-amber-500/30'],
                    ['id' => 'nilai', 'label' => 'Input Nilai', 'icon' => '📝', 'active' => 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30'],
                    ['id' => 'laporan', 'label' => 'Cetak Laporan', 'icon' => '🖨️', 'active' => 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'],
                ];
                foreach ($tabs as $t):
                    $isActive = $tab === $t['id'];
                    $style = $isActive ? $t['active'] : 'bg-white/5 text-slate-300 hover:bg-white/10 border border-white/5';
                ?>
                    <a href="?tab=<?= $t['id'] ?>" class="px-5 py-3 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center gap-2 <?= $style ?>">
                        <span><?= $t['icon'] ?></span><span><?= $t['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <main class="glass-panel rounded-[2.5rem] p-6 md:p-10 border border-white/10 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl -z-10"></div>

                <?php if ($tab === 'mahasiswa'): ?>
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-white mb-2">Manajemen <span class="text-blue-400">Mahasiswa</span></h2>
                        <p class="text-sm text-slate-400">Kelola data mahasiswa program studi Bisnis Digital.</p>
                    </div>

                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10 bg-black/20 p-6 rounded-3xl border border-white/5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">NIM</label>
                            <input type="text" name="nim" placeholder="Contoh: E32201001" required class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Nama Lengkap</label>
                            <input type="text" name="nama" placeholder="Nama mahasiswa" required class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Email</label>
                            <input type="email" name="email" placeholder="email@student.polije.ac.id" required class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Program Studi</label>
                            <input type="text" name="jurusan" placeholder="Bisnis Digital" value="Bisnis Digital" required class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <button type="submit" name="add_mhs" class="col-span-1 md:col-span-2 font-bold py-4 mt-2 rounded-xl text-white transition-all duration-200 bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-500/25">
                            + Daftarkan Mahasiswa Baru
                        </button>
                    </form>

                    <h3 class="text-lg font-semibold mb-5 text-slate-300">Daftar Terdaftar</h3>
                    <?php if (empty($_SESSION['mahasiswa'])): ?>
                        <div class="text-center py-16 text-slate-500 glass-card rounded-3xl border border-dashed border-white/10">
                            <div class="text-5xl mb-3 opacity-50">👨‍🎓</div><p class="text-sm">Belum ada data mahasiswa.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($_SESSION['mahasiswa'] as $mhs): ?>
                                <div class="glass-card p-6 rounded-3xl flex flex-col justify-between relative group overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <a href="?action=delete_mhs&nim=<?= $mhs->getId() ?>" onclick="return confirm('Hapus data <?= htmlspecialchars($mhs->getNama()) ?>?')" class="absolute top-4 right-4 p-2 bg-red-500/10 rounded-lg text-red-400 opacity-0 group-hover:opacity-100 transition-all hover:bg-red-500 hover:text-white" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                    <div class="pr-8 relative z-10">
                                        <span class="text-xs font-bold text-blue-400 tracking-wider mb-1 block"><?= htmlspecialchars($mhs->getId()) ?></span>
                                        <h4 class="text-lg font-bold text-white mb-1 leading-tight"><?= htmlspecialchars($mhs->getNama()) ?></h4>
                                        <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($mhs->email ?? '') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'matakuliah'): ?>
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-white mb-2">Kurikulum <span class="text-amber-400">Mata Kuliah</span></h2>
                        <p class="text-sm text-slate-400">Kelola daftar mata kuliah yang tersedia.</p>
                    </div>

                    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 bg-black/20 p-6 rounded-3xl border border-white/5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Kode MK</label>
                            <input type="text" name="kode" placeholder="Contoh: BD101" required class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" placeholder="Nama lengkap mata kuliah" required class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase ml-1">SKS</label>
                            <input type="number" name="sks" placeholder="1–6" required min="1" max="6" class="modern-input rounded-xl p-4 text-sm">
                        </div>
                        <button type="submit" name="add_mk" class="col-span-1 md:col-span-3 font-bold py-4 mt-2 rounded-xl text-white transition-all duration-200 bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-500/25">
                            + Tambah Mata Kuliah
                        </button>
                    </form>

                    <h3 class="text-lg font-semibold mb-5 text-slate-300">Daftar Tersedia</h3>
                    <?php if (empty($_SESSION['matakuliah'])): ?>
                        <div class="text-center py-16 text-slate-500 glass-card rounded-3xl border border-dashed border-white/10">
                            <div class="text-5xl mb-3 opacity-50">📚</div><p class="text-sm">Belum ada mata kuliah yang ditambahkan.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($_SESSION['matakuliah'] as $mk): ?>
                                <div class="glass-card p-5 rounded-2xl flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col items-center justify-center shrink-0">
                                            <span class="text-lg font-bold text-amber-400 leading-none"><?= $mk->getSks() ?></span>
                                            <span class="text-[10px] text-amber-400/70 uppercase">SKS</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-400 tracking-wider block mb-0.5"><?= htmlspecialchars($mk->getKode()) ?></span>
                                            <h4 class="text-base font-semibold text-white"><?= htmlspecialchars($mk->getNamaMK()) ?></h4>
                                        </div>
                                    </div>
                                    <a href="?action=delete_mk&kode=<?= $mk->getKode() ?>" onclick="return confirm('Hapus mata kuliah ini?')" class="p-2 rounded-lg text-slate-500 hover:bg-red-500/20 hover:text-red-400 transition-colors ml-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'nilai'): ?>
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-white mb-2">Input <span class="text-indigo-400">Nilai</span></h2>
                        <p class="text-sm text-slate-400">Masukkan nilai akhir mahasiswa per mata kuliah.</p>
                    </div>

                    <?php if (empty($_SESSION['mahasiswa']) || empty($_SESSION['matakuliah'])): ?>
                        <div class="flex items-center gap-4 p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-200">
                            <div class="text-3xl">⚠️</div>
                            <div>
                                <h4 class="font-bold text-red-400 mb-1">Data Belum Lengkap</h4>
                                <p class="text-sm opacity-80">Pastikan ada minimal 1 mahasiswa dan 1 mata kuliah terdaftar.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-black/20 p-6 rounded-3xl border border-white/5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Mahasiswa</label>
                                <select name="nim" required class="modern-input rounded-xl p-4 text-sm appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23cbd5e1%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[length:12px_12px] bg-[position:right_1rem_center] pr-10 cursor-pointer">
                                    <?php foreach ($_SESSION['mahasiswa'] as $mhs): ?>
                                        <option value="<?= $mhs->getId() ?>"><?= $mhs->getId() ?> - <?= htmlspecialchars($mhs->getNama()) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Mata Kuliah</label>
                                <select name="kode_mk" required class="modern-input rounded-xl p-4 text-sm appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23cbd5e1%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[length:12px_12px] bg-[position:right_1rem_center] pr-10 cursor-pointer">
                                    <?php foreach ($_SESSION['matakuliah'] as $mk): ?>
                                        <option value="<?= $mk->getKode() ?>"><?= htmlspecialchars($mk->getNamaMK()) ?> (<?= $mk->getSks() ?> SKS)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-slate-400 uppercase ml-1">Nilai Angka (0-100)</label>
                                <input type="number" name="nilai" required min="0" max="100" placeholder="0 - 100" class="modern-input rounded-xl p-4 text-sm font-bold text-indigo-300">
                            </div>
                            <button type="submit" name="add_nilai" class="col-span-1 md:col-span-3 font-bold py-4 mt-2 rounded-xl text-white transition-all duration-200 bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-500/25">
                                + Simpan Nilai
                            </button>
                        </form>
                    <?php endif; ?>

                <?php elseif ($tab === 'laporan'): ?>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2">Cetak <span class="text-emerald-400">KHS & IPK</span></h2>
                            <p class="text-sm text-slate-400">Pratinjau dan cetak Kartu Hasil Studi.</p>
                        </div>
                        <button onclick="window.print()" class="no-print font-bold px-6 py-3 rounded-xl text-white transition-all hover:opacity-90 bg-emerald-600 shadow-lg shadow-emerald-500/30 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Laporan
                        </button>
                    </div>

                    <?php if (empty($_SESSION['mahasiswa'])): ?>
                        <div class="text-center py-16 text-slate-500 glass-card rounded-3xl border border-dashed border-white/10">
                            <div class="text-5xl mb-3 opacity-50">🖨️</div><p class="text-sm">Belum ada data mahasiswa untuk dicetak.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <?php foreach ($_SESSION['mahasiswa'] as $mhs): ?>
                                <div class="relative group">
                                    <?= $mhs->cetakLaporan() ?>
                                    <div class="mt-3 flex justify-end no-print">
                                        <a href="?action=reset_nilai&nim=<?= $mhs->getId() ?>" onclick="return confirm('Reset SEMUA nilai untuk <?= htmlspecialchars($mhs->getNama()) ?>?')" class="text-xs font-semibold text-red-400/70 hover:text-red-400 hover:bg-red-400/10 px-3 py-1.5 rounded-md transition-colors">
                                            ⚠️ Reset Nilai Mahasiswa Ini
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>

            <footer class="mt-12 text-center text-xs text-slate-600 no-print">
                SIAKAD PRO &copy; <?= date('Y') ?> &bull; Sistem Informasi Akademik Terpadu
            </footer>
        </div>
    <?php endif; ?>

    <script src="script.js"></script>
    <script>
        // Efek kursor bercahaya
        document.addEventListener('mousemove', (e) => {
            const glow = document.querySelector('.cursor-glow');
            if (glow) {
                glow.style.left = e.clientX + 'px';
                glow.style.top = e.clientY + 'px';
            }
        });
    </script>
</body>
</html>