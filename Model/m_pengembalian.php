
<?php
require_once __DIR__ . "/m_koneksi.php";

class m_pengembalian
{
    private $db;
    private $tarif_denda = 2000;

    public function __construct()
    {
        $koneksi = new koneksi();
        $this->db = $koneksi->koneksi;
    }

    public function tampil_data()
    {
        $sql = "SELECT pg.*,
                       dp.id_peminjaman,
                       dp.id_alat,
                       dp.jumlah,
                       a.nama_alat,
                       p.tanggal_kembali_rencana,
                       p.tanggal_pengajuan
                FROM pengembalian pg
                JOIN detail_peminjaman dp ON pg.id_detail = dp.id_detail
                JOIN alat a ON dp.id_alat = a.id_alat
                JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                ORDER BY pg.id_pengembalian DESC";

        $result = mysqli_query($this->db, $sql);

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    // Mengambil pengembalian sekaligus data peminjaman terkait.
    public function get_by_id($id)
    {
        $sql = "SELECT pg.*,
                       dp.id_peminjaman,
                       dp.id_alat,
                       dp.jumlah,
                       a.nama_alat,
                       p.tanggal_kembali_rencana
                FROM pengembalian pg
                JOIN detail_peminjaman dp ON pg.id_detail = dp.id_detail
                JOIN alat a ON dp.id_alat = a.id_alat
                JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                WHERE pg.id_pengembalian = ?
                LIMIT 1";

        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        return $data ?: false;
    }

    public function daftar_detail_belum_kembali()
    {
        $sql = "SELECT dp.id_detail, dp.id_peminjaman, dp.id_alat,
                       dp.jumlah, a.nama_alat,
                       p.tanggal_kembali_rencana, p.tanggal_pengajuan,
                       pp.id_pengajuan_kembali,
                       pp.tanggal_pengajuan AS tanggal_pengajuan_kembali
                FROM detail_peminjaman dp
                JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                JOIN alat a ON dp.id_alat = a.id_alat
                JOIN pengajuan_pengembalian pp ON pp.id_detail = dp.id_detail
                WHERE p.status = 'disetujui'
                  AND pp.status = 'diproses'
                  AND NOT EXISTS (
                      SELECT 1 FROM pengembalian pg
                      WHERE pg.id_detail = dp.id_detail
                  )
                ORDER BY dp.id_detail DESC";

        $result = mysqli_query($this->db, $sql);

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    public function get_tanggal_jatuh_tempo($id_detail)
    {
        $sql = "SELECT p.tanggal_kembali_rencana
                FROM detail_peminjaman dp
                JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                WHERE dp.id_detail = ?
                  AND p.status = 'disetujui'
                  AND NOT EXISTS (
                      SELECT 1 FROM pengembalian pg
                      WHERE pg.id_detail = dp.id_detail
                  )
                LIMIT 1";

        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "i", $id_detail);
        mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        return $data["tanggal_kembali_rencana"] ?? false;
    }

    public function hitung_denda($tanggal_rencana, $tanggal_kembali)
    {
        $jatuhTempo = DateTime::createFromFormat("!Y-m-d", $tanggal_rencana);
        $tanggalKembali = DateTime::createFromFormat("!Y-m-d", $tanggal_kembali);

        if (
            !$jatuhTempo || !$tanggalKembali ||
            $jatuhTempo->format("Y-m-d") !== $tanggal_rencana ||
            $tanggalKembali->format("Y-m-d") !== $tanggal_kembali
        ) {
            return false;
        }

        $hari = $tanggalKembali > $jatuhTempo
            ? (int) $jatuhTempo->diff($tanggalKembali)->days
            : 0;

        return [
            "hari_terlambat" => $hari,
            "total_denda" => $hari * $this->tarif_denda
        ];
    }

    // Proses pengembalian asli Petugas tetap dipertahankan.
    public function tambah(
        $id_detail,
        $tanggal_kembali,
        $kondisi_kembali,
        $keterangan,
        $status_bayar
    ) {
        mysqli_begin_transaction($this->db);

        try {
            $sql = "SELECT dp.id_detail, dp.id_peminjaman,
                           dp.id_alat, dp.jumlah, p.tanggal_kembali_rencana
                    FROM detail_peminjaman dp
                    JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                    WHERE dp.id_detail = ? AND p.status = 'disetujui'
                    FOR UPDATE";

            $stmt = mysqli_prepare($this->db, $sql);
            if (!$stmt) throw new Exception("Gagal menyiapkan data peminjaman.");

            mysqli_stmt_bind_param($stmt, "i", $id_detail);
            mysqli_stmt_execute($stmt);
            $detail = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$detail) throw new Exception("Peminjaman tidak ditemukan atau sudah selesai.");

            $stmt = mysqli_prepare(
                $this->db,
                "SELECT id_pengembalian FROM pengembalian WHERE id_detail = ? LIMIT 1"
            );
            if (!$stmt) throw new Exception("Gagal memeriksa pengembalian.");

            mysqli_stmt_bind_param($stmt, "i", $id_detail);
            mysqli_stmt_execute($stmt);
            $sudahKembali = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($sudahKembali) throw new Exception("Alat sudah tercatat dikembalikan.");

            $stmt = mysqli_prepare(
                $this->db,
                "SELECT id_pengajuan_kembali
                 FROM pengajuan_pengembalian
                 WHERE id_detail = ? AND status = 'diproses'
                 ORDER BY id_pengajuan_kembali DESC LIMIT 1 FOR UPDATE"
            );
            if (!$stmt) throw new Exception("Gagal memeriksa pengajuan.");

            mysqli_stmt_bind_param($stmt, "i", $id_detail);
            mysqli_stmt_execute($stmt);
            $pengajuan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$pengajuan) throw new Exception("Pengajuan harus diproses terlebih dahulu.");

            $hasilDenda = $this->hitung_denda(
                $detail["tanggal_kembali_rencana"],
                $tanggal_kembali
            );

            if ($hasilDenda === false) throw new Exception("Tanggal pengembalian tidak valid.");

            $denda = $hasilDenda["total_denda"];

            if ($denda <= 0) {
                $status_bayar = "lunas";
            }

            if (!in_array($status_bayar, ["belum_bayar", "lunas"], true)) {
                throw new Exception("Status pembayaran tidak valid.");
            }

            $stmt = mysqli_prepare(
                $this->db,
                "INSERT INTO pengembalian
                 (id_detail, tanggal_kembali, kondisi_kembali, denda, keterangan, status_bayar)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) throw new Exception("Gagal menyiapkan penyimpanan.");

            mysqli_stmt_bind_param(
                $stmt, "issdss",
                $id_detail, $tanggal_kembali, $kondisi_kembali,
                $denda, $keterangan, $status_bayar
            );

            if (!mysqli_stmt_execute($stmt)) throw new Exception("Gagal menyimpan pengembalian.");
            mysqli_stmt_close($stmt);

            $id_pengajuan = (int) $pengajuan["id_pengajuan_kembali"];
            $stmt = mysqli_prepare(
                $this->db,
                "UPDATE pengajuan_pengembalian
                 SET status = 'diterima'
                 WHERE id_pengajuan_kembali = ? AND status = 'diproses'"
            );
            if (!$stmt) throw new Exception("Gagal menyiapkan status pengajuan.");

            mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
            if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
                throw new Exception("Status pengajuan gagal diperbarui.");
            }
            mysqli_stmt_close($stmt);

            $jumlah = (int) $detail["jumlah"];
            $id_alat = (int) $detail["id_alat"];

            $stmt = mysqli_prepare(
                $this->db,
                "UPDATE alat
                 SET jumlah = jumlah + ?,
                     status = CASE
                         WHEN status = 'tidak_aktif' THEN status
                         ELSE 'tersedia'
                     END
                 WHERE id_alat = ?"
            );
            if (!$stmt) throw new Exception("Gagal menyiapkan pembaruan stok.");

            mysqli_stmt_bind_param($stmt, "ii", $jumlah, $id_alat);
            if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
                throw new Exception("Stok alat gagal diperbarui.");
            }
            mysqli_stmt_close($stmt);

            $id_peminjaman = (int) $detail["id_peminjaman"];

            $stmt = mysqli_prepare(
                $this->db,
                "SELECT COUNT(*) AS total_detail,
                        SUM(CASE WHEN EXISTS (
                            SELECT 1 FROM pengembalian pg
                            WHERE pg.id_detail = dp.id_detail
                        ) THEN 1 ELSE 0 END) AS total_kembali
                 FROM detail_peminjaman dp
                 WHERE dp.id_peminjaman = ?"
            );
            if (!$stmt) throw new Exception("Gagal memeriksa detail peminjaman.");

            mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
            mysqli_stmt_execute($stmt);
            $semua = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (
                $semua &&
                (int) $semua["total_detail"] > 0 &&
                (int) $semua["total_detail"] === (int) $semua["total_kembali"]
            ) {
                $stmt = mysqli_prepare(
                    $this->db,
                    "UPDATE peminjaman SET status = 'selesai'
                     WHERE id_peminjaman = ? AND status = 'disetujui'"
                );
                if (!$stmt) throw new Exception("Gagal menyiapkan status selesai.");

                mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
                if (!mysqli_stmt_execute($stmt)) throw new Exception("Status peminjaman gagal diperbarui.");
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($this->db);
            return true;
        } catch (Throwable $e) {
            mysqli_rollback($this->db);
            error_log("Gagal mencatat pengembalian: " . $e->getMessage());
            return false;
        }
    }

    public function update_status_bayar($id_pengembalian, $status_bayar)
    {
        if (!in_array($status_bayar, ["belum_bayar", "lunas"], true)) return false;

        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE pengembalian SET status_bayar = ? WHERE id_pengembalian = ?"
        );
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "si", $status_bayar, $id_pengembalian);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $berhasil;
    }

    // Update khusus Admin; denda dihitung ulang dari jatuh tempo database.
    public function update_admin(
        $id_pengembalian,
        $tanggal_kembali,
        $kondisi_kembali,
        $keterangan,
        $status_bayar
    ) {
        if (!in_array($kondisi_kembali, ["Baik", "Rusak", "Hilang"], true)) return false;
        if (!in_array($status_bayar, ["belum_bayar", "lunas"], true)) return false;

        $data = $this->get_by_id($id_pengembalian);
        if (!$data) return false;

        $hasil = $this->hitung_denda(
            $data["tanggal_kembali_rencana"],
            $tanggal_kembali
        );
        if ($hasil === false) return false;

        $denda = $hasil["total_denda"];
        if ($denda <= 0) $status_bayar = "lunas";

        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE pengembalian
             SET tanggal_kembali = ?, kondisi_kembali = ?,
                 denda = ?, keterangan = ?, status_bayar = ?
             WHERE id_pengembalian = ?"
        );
        if (!$stmt) return false;

        mysqli_stmt_bind_param(
            $stmt, "ssdssi",
            $tanggal_kembali, $kondisi_kembali, $denda,
            $keterangan, $status_bayar, $id_pengembalian
        );

        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $berhasil;
    }

    // Hapus dengan membatalkan efek pengembalian pada stok dan status.
    public function hapus($id_pengembalian)
    {
        mysqli_begin_transaction($this->db);

        try {
            $stmt = mysqli_prepare(
                $this->db,
                "SELECT pg.id_detail, dp.id_peminjaman, dp.id_alat, dp.jumlah
                 FROM pengembalian pg
                 JOIN detail_peminjaman dp ON pg.id_detail = dp.id_detail
                 WHERE pg.id_pengembalian = ?
                 FOR UPDATE"
            );
            if (!$stmt) throw new Exception("Gagal mengambil data pengembalian.");

            mysqli_stmt_bind_param($stmt, "i", $id_pengembalian);
            mysqli_stmt_execute($stmt);
            $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$data) throw new Exception("Data pengembalian tidak ditemukan.");

            $id_detail = (int) $data["id_detail"];
            $id_alat = (int) $data["id_alat"];
            $jumlah = (int) $data["jumlah"];
            $id_peminjaman = (int) $data["id_peminjaman"];

            // Kurangi kembali stok yang sebelumnya ditambahkan saat pengembalian.
            $stmt = mysqli_prepare(
                $this->db,
                "UPDATE alat SET jumlah = jumlah - ?
                 WHERE id_alat = ? AND jumlah >= ?"
            );
            if (!$stmt) throw new Exception("Gagal menyiapkan pengurangan stok.");

            mysqli_stmt_bind_param($stmt, "iii", $jumlah, $id_alat, $jumlah);
            if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
                throw new Exception("Stok tidak cukup untuk membatalkan pengembalian.");
            }
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $this->db,
                "UPDATE pengajuan_pengembalian
                 SET status = 'diproses'
                 WHERE id_detail = ? AND status = 'diterima'
                 ORDER BY id_pengajuan_kembali DESC LIMIT 1"
            );
            if (!$stmt) throw new Exception("Gagal mengembalikan status pengajuan.");

            mysqli_stmt_bind_param($stmt, "i", $id_detail);
            if (!mysqli_stmt_execute($stmt)) throw new Exception("Status pengajuan gagal diperbarui.");
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $this->db,
                "DELETE FROM pengembalian WHERE id_pengembalian = ?"
            );
            if (!$stmt) throw new Exception("Gagal menyiapkan penghapusan.");

            mysqli_stmt_bind_param($stmt, "i", $id_pengembalian);
            if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
                throw new Exception("Data pengembalian gagal dihapus.");
            }
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $this->db,
                "UPDATE peminjaman SET status = 'disetujui'
                 WHERE id_peminjaman = ? AND status = 'selesai'"
            );
            if (!$stmt) throw new Exception("Gagal menyiapkan status peminjaman.");

            mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
            if (!mysqli_stmt_execute($stmt)) throw new Exception("Status peminjaman gagal diperbarui.");
            mysqli_stmt_close($stmt);

            mysqli_commit($this->db);
            return true;
        } catch (Throwable $e) {
            mysqli_rollback($this->db);
            error_log("Gagal menghapus pengembalian: " . $e->getMessage());
            return false;
        }
    }
}
?>
