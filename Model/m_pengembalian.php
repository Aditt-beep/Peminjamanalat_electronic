<?php
include_once __DIR__ . "/m_koneksi.php";

class m_pengembalian {

    function tampil_data(){
        $koneksi = new koneksi();
        $sql = "SELECT * FROM pengembalian"; 
        $query = mysqli_query($koneksi->koneksi, $sql);

        $result = [];

        while ($data = mysqli_fetch_assoc($query)) {
            $result[] = $data;
        }

        return $result;
    }

    function get_by_id($id) {
        $koneksi = new koneksi();

        $sql = "SELECT * FROM pengembalian WHERE id_pengembalian='$id'";
        $query = mysqli_query($koneksi->koneksi, $sql);

        return mysqli_fetch_assoc($query);
    }

    function tambah($tanggal_kembali, $kondisi_kembali) {
        $koneksi = new koneksi();

        $sql = "INSERT INTO pengembalian
        (tanggal_kembali, kondisi_kembali)
        VALUES
        ('$tanggal_kembali', '$tkondisi_pengembalian')";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function update($id_pengembalian, $tanggal_pengembalian, $kondisi_kembali) {
        $koneksi = new koneksi();

        $sql = "UPDATE pengembalian SET
        tanggal_pengembalian='$tangggal_pengembalian',
        kondisi_kembali='$kondisi_kembali',
        WHERE id_pengembalian='$id_pengembalian'";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function hapus($id) {
    $koneksi = new koneksi();

    $sql = "DELETE FROM pengembalian WHERE id_pengembalian='$id'";

    return mysqli_query($koneksi->koneksi, $sql);
}
}