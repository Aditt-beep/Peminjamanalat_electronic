<?php
include_once __DIR__ . "/m_koneksi.php";

class m_kategori {

    function tampil_data(){
        $koneksi = new koneksi();
        $sql = "SELECT * FROM kategori"; 
        $query = mysqli_query($koneksi->koneksi, $sql);

        $result = [];

        while ($data = mysqli_fetch_assoc($query)) {
            $result[] = $data;
        }

        return $result;
    }

    function get_by_id($id) {
        $koneksi = new koneksi();

        $sql = "SELECT * FROM kategori WHERE id_kategori='$id'";
        $query = mysqli_query($koneksi->koneksi, $sql);

        return mysqli_fetch_assoc($query);
    }

    function tambah($nama_kategori) {
        $koneksi = new koneksi();

        $sql = "INSERT INTO kategori
        (nama_kategori)
        VALUES
        ('$nama_kategori')";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function update($id_kategori, $nama_kategori) {
        $koneksi = new koneksi();

        $sql = "UPDATE kategori SET
        nama_kategori='$nama_kategori',
        WHERE id_kategori='$id_kategori'";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function hapus($id) {
    $koneksi = new koneksi();

    $sql = "DELETE FROM kategori WHERE id_kategori='$id'";

    return mysqli_query($koneksi->koneksi, $sql);
}
}