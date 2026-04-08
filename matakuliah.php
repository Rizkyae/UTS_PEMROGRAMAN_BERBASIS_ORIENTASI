<?php

class MataKuliah {
    private $kode;
    private $namaMK;
    private $sks;

    public function __construct($kode, $namaMK, $sks) {
        $this->kode = $kode;
        $this->namaMK = $namaMK;
        $this->sks = $sks;
    }

    public function getKode() { return $this->kode; }
    public function getNamaMK() { return $this->namaMK; }
    public function getSks() { return $this->sks; }
}
?>