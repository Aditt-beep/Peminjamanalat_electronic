<?php
include_once __DIR__ . "/m_koneksi.php";

class m_alat {

    function tampil_data(){
        $koneksi = new koneksi();
        $sql = "SELECT * FROM alat"; 
        $query = mysqli_query($koneksi->koneksi, $sql);

        $result = [];

        while ($data = mysqli_fetch_assoc($query)) {
            $result[] = $data;
        }

        return $result;
    }

    function get_by_id($id) {
        $koneksi = new koneksi();

        $sql = "SELECT * FROM alat WHERE id_alat='$id'";
        $query = mysqli_query($koneksi->koneksi, $sql);

        return mysqli_fetch_assoc($query);
    }

    function tambah($nama_alat, $merk, $jumlah, $kondisi, $status) {
        $koneksi = new koneksi();

        $sql = "INSERT INTO alat
        (nama_alat, merk, jumlah, kondisi, status)
        VALUES
        ('$nama_alat', '$merk', '$jumlah', '$kondisi', '$status')";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function update($id_alat, $nama_alat, $merk, $jumlah, $kondisi, $status) {
        $koneksi = new koneksi();

        $sql = "UPDATE alat SET
        nama_alat='$nama_alat',
        merk='$merk',
        jumlah='$jumlah',
        kondisi='$kondisi',
        status='$status'
        WHERE id_alat='$id_alat'";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function hapus($id) {
    $koneksi = new koneksi();

    $sql = "DELETE FROM alat WHERE id_alat='$id'";

    return mysqli_query($koneksi->koneksi, $sql);
}
}