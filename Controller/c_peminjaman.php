
<?php
session_start();

require_once __DIR__ . "/../Model/m_peminjaman.php";

if (
    !isset($_SESSION["data"]) ||
    ($_SESSION["data"]["role"] ?? "") !== "admin"
) {
    header("Location: ../View/v_login.php");
    exit;
}

class c_peminjaman
{
    private $model;

    public function __construct()
    {
        $this->model = new m_peminjaman();
    }

    public function index()
    {
        $data_peminjaman = $this->model->tampil_data();
        include __DIR__ . "/../View/v_peminjaman.php";
    }

    public function tambah()
    {
        include __DIR__ . "/../View/v_tambah_peminjaman.php";
    }

    public function simpan()
    {
        try {
            $id_user = (int) ($_POST["id_user"] ?? 0);
            $tanggal_pengajuan = $_POST["tanggal_pengajuan"] ?? "";
            $tanggal_pinjam = $_POST["tanggal_pinjam"] ?? "";
            $tanggal_kembali = $_POST["tanggal_kembali_rencana"] ?? "";
            $keperluan = trim($_POST["keperluan"] ?? "");
            $status = $_POST["status"] ?? "";

            $status_valid = [
                "menunggu",
                "disetujui",
                "ditolak",
                "selesai"
            ];

            if (
                $id_user < 1 ||
                $tanggal_pengajuan === "" ||
                $tanggal_pinjam === "" ||
                $tanggal_kembali === "" ||
                $keperluan === "" ||
                !in_array($status, $status_valid, true)
            ) {
                throw new Exception(
                    "Semua kolom wajib diisi dengan benar."
                );
            }

            if ($tanggal_kembali < $tanggal_pinjam) {
                throw new Exception(
                    "Tanggal kembali tidak boleh sebelum tanggal peminjaman."
                );
            }

            $this->model->tambah(
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status
            );

            header("Location: ../View/v_peminjaman.php");
            exit;

        } catch (Throwable $e) {
            error_log("Tambah peminjaman: " . $e->getMessage());

            http_response_code(500);
            echo "<!DOCTYPE html><html lang='id'><meta charset='UTF-8'>";
            echo "<title>Gagal menyimpan</title>";
            echo "<body style='font-family:Arial;padding:30px'>";
            echo "<h2>Data peminjaman gagal disimpan</h2>";
            echo "<p>" .
                htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8") .
                "</p>";
            echo "<p><a href='../View/v_tambah_peminjaman.php'>Kembali ke form</a></p>";
            echo "</body></html>";
            exit;
        }
    }

    public function edit($id)
    {
        $data = $this->model->get_by_id((int) $id);
        $daftar_user = $this->model->daftar_user();

        if (!$data) {
            header("Location: ../View/v_peminjaman.php");
            exit;
        }

        include __DIR__ . "/../View/v_edit_peminjaman.php";
    }

    public function update()
    {
        try {
            $id_peminjaman = (int) ($_POST["id_peminjaman"] ?? 0);
            $id_user = (int) ($_POST["id_user"] ?? 0);
            $tanggal_pengajuan = $_POST["tanggal_pengajuan"] ?? "";
            $tanggal_pinjam = $_POST["tanggal_pinjam"] ?? "";
            $tanggal_kembali = $_POST["tanggal_kembali_rencana"] ?? "";
            $keperluan = trim($_POST["keperluan"] ?? "");
            $status = $_POST["status"] ?? "";

            $status_valid = [
                "menunggu",
                "disetujui",
                "ditolak",
                "selesai"
            ];

            if (
                $id_peminjaman < 1 ||
                $id_user < 1 ||
                $tanggal_pengajuan === "" ||
                $tanggal_pinjam === "" ||
                $tanggal_kembali === "" ||
                $keperluan === "" ||
                !in_array($status, $status_valid, true)
            ) {
                throw new Exception(
                    "Data edit belum lengkap atau tidak valid."
                );
            }

            if ($tanggal_kembali < $tanggal_pinjam) {
                throw new Exception(
                    "Tanggal kembali tidak boleh sebelum tanggal peminjaman."
                );
            }

            $this->model->update(
                $id_peminjaman,
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status
            );

            header("Location: ../View/v_peminjaman.php");
            exit;

        } catch (Throwable $e) {
            error_log("Edit peminjaman: " . $e->getMessage());

            http_response_code(500);
            echo "<h3>Data peminjaman gagal diperbarui.</h3>";
            echo "<p>" .
                htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8") .
                "</p>";
            echo '<a href="../View/v_peminjaman.php">Kembali</a>';
            exit;
        }
    }

    public function hapus($id)
    {
        try {
            $this->model->hapus((int) $id);

            header("Location: ../View/v_peminjaman.php");
            exit;

        } catch (Throwable $e) {
            error_log("Hapus peminjaman: " . $e->getMessage());

            http_response_code(500);
            echo "<h3>Data gagal dihapus.</h3>";
            echo "<p>Data mungkin masih terhubung dengan detail peminjaman.</p>";
            echo '<a href="../View/v_peminjaman.php">Kembali</a>';
            exit;
        }
    }
}

// Jalankan aksi jika controller dipanggil langsung dari form.
$controller = new c_peminjaman();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["tambah"])) {
        $controller->simpan();
    }

    if (isset($_POST["edit"])) {
        $controller->update();
    }
}

if (isset($_GET["hapus"])) {
    $controller->hapus((int) $_GET["hapus"]);
}
?>
