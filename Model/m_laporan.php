
<?php
require_once __DIR__ . "/m_koneksi.php";

class m_laporan {
    private $db;

    public function __construct() {
        $koneksi = new koneksi();
        $this->db = $koneksi->koneksi;
    }

    public function laporan_peminjaman() {
        $sql = "SELECT
                    p.id_peminjaman,
                    u.nama AS nama_peminjam,
                    a.kode_alat,
                    a.nama_alat,
                    dp.jumlah,
                    p.tanggal_pengajuan,
                    p.tanggal_pinjam,
                    p.tanggal_kembali_rencana,
                    p.keperluan,
                    p.status
                FROM peminjaman p
                JOIN tb_user u ON p.id_user = u.id_user
                JOIN detail_peminjaman dp
                    ON p.id_peminjaman = dp.id_peminjaman
                JOIN alat a ON dp.id_alat = a.id_alat
                ORDER BY p.tanggal_pinjam DESC, p.id_peminjaman DESC";

        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            die("Gagal mengambil laporan peminjaman: " .
                mysqli_error($this->db));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function laporan_pengembalian() {
        $sql = "SELECT
                    pg.id_pengembalian,
                    u.nama AS nama_peminjam,
                    a.kode_alat,
                    a.nama_alat,
                    dp.jumlah,
                    p.tanggal_pinjam,
                    pg.tanggal_kembali,
                    pg.kondisi_kembali,
                    pg.denda,
                    pg.status_bayar,
                    pg.keterangan
                FROM pengembalian pg
                JOIN detail_peminjaman dp
                    ON pg.id_detail = dp.id_detail
                JOIN peminjaman p
                    ON dp.id_peminjaman = p.id_peminjaman
                JOIN tb_user u ON p.id_user = u.id_user
                JOIN alat a ON dp.id_alat = a.id_alat
                ORDER BY pg.tanggal_kembali DESC,
                         pg.id_pengembalian DESC";

        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            die("Gagal mengambil laporan pengembalian: " .
                mysqli_error($this->db));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>
