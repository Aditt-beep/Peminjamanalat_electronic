<?php
/**
 * Model/m_log.php
 * Tabel: log_aktivitas (id_log, id_user, aktivitas, waktu)
 *
 * Pemakaian di mana saja:
 *     require_once __DIR__ . "/../Model/m_log.php";
 *     m_log::catat("login");
 */
class m_log
{
    private $db;

    // SESUAIKAN: nama tabel user di database lo (untuk menampilkan nama pengguna)
    private $tabelUser = "tb_user";

    public function __construct()
    {
        // SESUAIKAN: pakai koneksi yang sama seperti di m_peminjaman.php
        $this->db = new mysqli("localhost", "root", "", "pinjamin_electronic");
        $this->db->set_charset("utf8mb4");
    }

    /**
     * Catat satu aktivitas milik user yang sedang login.
     * Tidak melempar error ke pemanggil, jadi kegagalan log
     * tidak menggagalkan proses utama.
     */
    public static function catat($aktivitas)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_user = $_SESSION["data"]["id_user"] ?? null;
        if ($id_user === null) {
            return; // belum login, tidak ada id_user untuk dicatat
        }

        try {
            $m = new self();
            $stmt = $m->db->prepare(
                "INSERT INTO log_aktivitas (id_user, aktivitas, waktu)
                 VALUES (?, ?, NOW())"
            );
            $id_user = (int)$id_user;
            $stmt->bind_param("is", $id_user, $aktivitas);
            $stmt->execute();
        } catch (Throwable $e) {
            error_log("Gagal mencatat log: " . $e->getMessage());
        }
    }

    /** Ambil log (beserta nama user) dengan filter dan paginasi. */
    public function daftar($jenis = "", $limit = 20, $offset = 0)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;

        $sql = "SELECT l.id_log, l.id_user, l.aktivitas, l.waktu,
                       u.nama, u.username, u.role
                FROM log_aktivitas l
                LEFT JOIN {$this->tabelUser} u ON u.id_user = l.id_user";

        if ($jenis !== "") {
            $stmt = $this->db->prepare(
                $sql . " WHERE l.aktivitas = ?
                         ORDER BY l.id_log DESC LIMIT ? OFFSET ?"
            );
            $stmt->bind_param("sii", $jenis, $limit, $offset);
        } else {
            $stmt = $this->db->prepare(
                $sql . " ORDER BY l.id_log DESC LIMIT ? OFFSET ?"
            );
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function hitung($jenis = "")
    {
        if ($jenis !== "") {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS n FROM log_aktivitas WHERE aktivitas = ?"
            );
            $stmt->bind_param("s", $jenis);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS n FROM log_aktivitas");
        }

        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()["n"];
    }

    /** Daftar jenis aktivitas yang pernah tercatat (untuk dropdown filter). */
    public function daftar_aksi()
    {
        $res = $this->db->query(
            "SELECT DISTINCT aktivitas FROM log_aktivitas ORDER BY aktivitas"
        );
        return array_column($res->fetch_all(MYSQLI_ASSOC), "aktivitas");
    }
}