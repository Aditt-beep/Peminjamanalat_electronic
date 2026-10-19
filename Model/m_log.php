
<?php
require_once __DIR__ . "/m_koneksi.php";

class m_log
{
    public function simpan_log($id_user, $aktivitas)
    {
        $db = new koneksi();
        $conn = $db->koneksi;

        $sql = "INSERT INTO log_aktivitas (id_user, aktivitas, waktu)
                VALUES (?, ?, NOW())";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "is", $id_user, $aktivitas);
        $hasil = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $hasil;
    }

    public function tampil_log()
    {
        $db = new koneksi();
        $conn = $db->koneksi;

        $sql = "SELECT
                    l.id_log,
                    l.id_user,
                    l.aktivitas,
                    l.waktu,
                    u.nama,
                    u.username,
                    u.role
                FROM log_aktivitas l
                LEFT JOIN tb_user u ON l.id_user = u.id_user
                ORDER BY l.waktu DESC, l.id_log DESC";

        return mysqli_query($conn, $sql);
    }
}
?>
