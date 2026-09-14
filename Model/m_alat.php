<?php

include_once __DIR__ . '/m_koneksi.php';

class m_alat
{
    private $koneksi;

    public function __construct()
    {
        $db = new koneksi();
        $this->koneksi = $db->koneksi;
    }

    public function tampilData()
    {
        $query = "SELECT * FROM alat ORDER BY id_alat DESC";
        return $this->koneksi->query($query);
    }

    public function tambahAlat(
        $kode_alat,
        $nama_alat,
        $id_kategori,
        $merk,
        $jumlah,
        $kondisi,
        $status,
        $deskripsi
    ) {
        $query = "INSERT INTO alat
                  (kode_alat, nama_alat, id_kategori, merk, jumlah, kondisi, status, deskripsi)
                  VALUES
                  ('$kode_alat', '$nama_alat', '$id_kategori', '$merk', '$jumlah', '$kondisi', '$status', '$deskripsi')";

        return $this->koneksi->query($query);
    }

    public function ambilAlat($id_alat)
    {
        $query = "SELECT * FROM alat WHERE id_alat = '$id_alat'";
        return $this->koneksi->query($query);
    }

    public function editAlat(
        $id_alat,
        $kode_alat,
        $nama_alat,
        $id_kategori,
        $merk,
        $jumlah,
        $kondisi,
        $status,
        $deskripsi
    ) {
        $query = "UPDATE alat SET
                    kode_alat = '$kode_alat',
                    nama_alat = '$nama_alat',
                    id_kategori = '$id_kategori',
                    merk = '$merk',
                    jumlah = '$jumlah',
                    kondisi = '$kondisi',
                    status = '$status',
                    deskripsi = '$deskripsi'
                  WHERE id_alat = '$id_alat'";

        return $this->koneksi->query($query);
    }

    public function hapusAlat($id_alat)
    {
        $query = "DELETE FROM alat WHERE id_alat = '$id_alat'";
        return $this->koneksi->query($query);
    }
}