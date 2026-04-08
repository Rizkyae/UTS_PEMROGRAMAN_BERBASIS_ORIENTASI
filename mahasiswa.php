<?php
require_once 'User.php';
require_once 'CetakLaporan.php';
require_once 'MataKuliah.php';

class Mahasiswa extends User implements CetakLaporan {
    // Encapsulation: property private
    private $jurusan;
    private $daftarNilai = []; // Array untuk menyimpan nilai (MataKuliah => Nilai Huruf)

    public function __construct($nim, $nama, $email, $jurusan) {
        parent::__construct($nim, $nama, $email); // Memanggil constructor parent
        $this->jurusan = $jurusan;
    }

    public function getRole() {
        return "Mahasiswa";
    }

    // Fitur: Input Nilai
    public function tambahNilai(MataKuliah $mk, $nilaiAngka) {
        $this->daftarNilai[] = [
            'mk' => $mk,
            'nilai' => $nilaiAngka
        ];
    }

    // Fitur: Reset Nilai
    public function resetNilai() {
        $this->daftarNilai = [];
    }

    // Fitur: Hitung IPK
    public function hitungIPK() {
        if (empty($this->daftarNilai)) return 0;

        $totalBobot = 0;
        $totalSKS = 0;

        foreach ($this->daftarNilai as $data) {
            $sks = $data['mk']->getSks();
            $nilai = $data['nilai'];

            // Konversi nilai angka ke bobot
            $bobot = 0;
            if ($nilai >= 85) $bobot = 4.0; // A
            elseif ($nilai >= 70) $bobot = 3.0; // B
            elseif ($nilai >= 55) $bobot = 2.0; // C
            elseif ($nilai >= 40) $bobot = 1.0; // D
            else $bobot = 0.0; // E

            $totalBobot += ($bobot * $sks);
            $totalSKS += $sks;
        }

        return $totalSKS > 0 ? round($totalBobot / $totalSKS, 2) : 0;
    }

    // Polymorphism: Implementasi dari CetakLaporan (Cetak KHS)
    public function cetakLaporan() {
        $ipk = $this->hitungIPK();

        if ($ipk >= 3.5) $predikat = "Dengan Pujian";
        elseif ($ipk >= 3.0) $predikat = "Sangat Memuaskan";
        elseif ($ipk >= 2.5) $predikat = "Memuaskan";
        elseif ($ipk >= 2.0) $predikat = "Cukup";
        else $predikat = "Kurang";

        $html = "<div class='glass-card p-6 rounded-3xl text-white'>";
        $html .= "<div class='border-b border-white/10 pb-4 mb-4'>";
        $html .= "<h3 class='text-xl font-bold text-purple-300'>Kartu Hasil Studi (KHS)</h3>";
        $html .= "</div>";
        $html .= "<div class='grid grid-cols-2 gap-2 text-sm mb-6 bg-black/20 p-4 rounded-xl'>";
        $html .= "<div><span class='text-slate-400'>NIM:</span> <br><strong class='text-amber-400'>{$this->id}</strong></div>";
        $html .= "<div><span class='text-slate-400'>Nama:</span> <br><strong>{$this->nama}</strong></div>";
        $html .= "<div class='col-span-2'><span class='text-slate-400'>Jurusan:</span> <strong class='text-blue-300'>{$this->jurusan}</strong></div>";
        $html .= "</div>";

        $html .= "<div class='overflow-x-auto'>";
        $html .= "<table class='w-full text-left border-collapse text-sm'>";
        $html .= "<thead><tr class='border-b border-white/20 text-slate-400'>";
        $html .= "<th class='p-3 font-medium'>Mata Kuliah</th>";
        $html .= "<th class='p-3 font-medium text-center'>SKS</th>";
        $html .= "<th class='p-3 font-medium text-center'>Nilai</th>";
        $html .= "<th class='p-3 font-medium text-center'>Huruf</th>";
        $html .= "</tr></thead><tbody>";

        if (empty($this->daftarNilai)) {
            $html .= "<tr><td colspan='4' class='p-3 text-center text-slate-500'>Belum ada nilai.</td></tr>";
        } else {
            foreach ($this->daftarNilai as $data) {
                $mk = $data['mk'];
                $nilai = $data['nilai'];
                if ($nilai >= 85) $huruf = 'A';
                elseif ($nilai >= 70) $huruf = 'B';
                elseif ($nilai >= 55) $huruf = 'C';
                elseif ($nilai >= 40) $huruf = 'D';
                else $huruf = 'E';

                $hurufColor = $huruf === 'A' ? 'text-emerald-400' : ($huruf === 'B' ? 'text-blue-400' : ($huruf === 'C' ? 'text-amber-400' : ($huruf === 'D' ? 'text-orange-400' : 'text-red-400')));

                $html .= "<tr class='border-b border-white/5 hover:bg-white/5 transition-colors'>";
                $html .= "<td class='p-3'>{$mk->getNamaMK()}</td>";
                $html .= "<td class='p-3 text-center'>{$mk->getSks()}</td>";
                $html .= "<td class='p-3 text-center font-semibold'>{$nilai}</td>";
                $html .= "<td class='p-3 text-center'><span class='font-bold {$hurufColor}'>{$huruf}</span></td>";
                $html .= "</tr>";
            }
        }

        $html .= "</tbody></table></div>";
        $html .= "<div class='mt-4 flex justify-between items-center border-t border-white/10 pt-4'>";
        $html .= "<div class='text-sm text-slate-400'>Predikat: <span class='text-white font-semibold'>{$predikat}</span></div>";
        $html .= "<div class='text-2xl font-extrabold text-green-300'>IPK: {$ipk}</div>";
        $html .= "</div></div>";

        return $html;
    }
}
?>
