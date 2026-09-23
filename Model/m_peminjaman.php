<?php
include_once __DIR__ . "/m_koneksi.php";

class m_peminjaman {

    function tampil_data(){
        $koneksi = new koneksi();
        $sql = "SELECT * FROM peminjaman"; 
        $query = mysqli_query($koneksi->koneksi, $sql);

        $result = [];

        while ($data = mysqli_fetch_assoc($query)) {
            $result[] = $data;
        }

        return $result;
    }

    function get_by_id($id) {
        $koneksi = new koneksi();

        $sql = "SELECT * FROM peminjaman WHERE id_peminjaman='$id'";
        $query = mysqli_query($koneksi->koneksi, $sql);

        return mysqli_fetch_assoc($query);
    }

    function tambah($tanggal_pengajuan, $tanggal_peminjaman, $tanggal_kembali_rencana, $status) {
        $koneksi = new koneksi();

        $sql = "INSERT INTO peminjaman
        (tanggal_pengajuan, tanggal_peminjaman, tanggal_kembali_rencana, status)
        VALUES
        ('$tanggal_pengajuan', '$tanggal_peminjaman', '$tanggal_kembali_renacana', '$status')";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function update($id_peminjaman, $tanggal_pengajuan, $tanggal_peminjaman, $tanggal_kembali_rencana, $status) {
        $koneksi = new koneksi();

        $sql = "UPDATE peminjaman SET
        tanggal_pengajuan='$tangggal_pengajuan',
        tanggal_peminjaman='$tanggal_peminjaman',
        tanggal_kembali_rencana='$tanggal_kembali_rencana',
        status='$status'
        WHERE id_peminjaman='$id_peminjaman'";

        return mysqli_query($koneksi->koneksi, $sql);
    }

    function hapus($id) {
    $koneksi = new koneksi();

    $sql = "DELETE FROM peminjaman WHERE id_peminjaman='$id'";

    return mysqli_query($koneksi->koneksi, $sql);
}
}