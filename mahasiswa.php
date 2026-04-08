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

    // Fitur: Hitung IPK
    public function resetNilai() {
        $this->daftarNilai = [];
    }
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
        $html = "<div class='bg-white/10 backdrop-blur-lg border border-white/20 p-6 rounded-2xl shadow-xl text-white'>";
        $html .= "<h3 class='text-2xl font-bold mb-4 text-purple-300'>Kartu Hasil Studi (KHS)</h3>";
        $html .= "<p><strong>NIM:</strong> {$this->id}</p>";
        $html .= "<p><strong>Nama:</strong> {$this->nama}</p>";
        $html .= "<p class='mb-4'><strong>Jurusan:</strong> {$this->jurusan}</p>";
        
        $html .= "<table class='w-full text-left border-collapse'>";
        $html .= "<thead><tr class='border-b border-white/20'><th class='p-2'>Mata Kuliah</th><th class='p-2'>SKS</th><th class='p-2'>Nilai</th></tr></thead>";
        $html .= "<tbody>";
        foreach ($this->daftarNilai as $data) {
            $mk = $data['mk'];
            $nilai = $data['nilai'];
            $huruf = $nilai >= 85 ? 'A' : ($nilai >= 70 ? 'B' : ($nilai >= 55 ? 'C' : ($nilai >= 40 ? 'D' : 'E')));
            $html .= "<tr class='border-b border-white/10'><td class='p-2'>{$mk->getNamaMK()}</td><td class='p-2'>{$mk->getSks()}</td><td class='p-2'>{$huruf} ({$nilai})</td></tr>";
        }
        $html .= "</tbody></table>";
        $html .= "<div class='mt-4 text-xl font-bold text-green-300'>IPK: " . $this->hitungIPK() . "</div>";
        $html .= "</div>";

        return $html;
    }
}
?>