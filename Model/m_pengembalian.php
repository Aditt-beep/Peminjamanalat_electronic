
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

    // Mengambil seluruh data pengembalian
    public function tampil_data()
    {
        $sql = "SELECT
                    pg.*,
                    dp.id_peminjaman,
                    dp.id_alat,
                    dp.jumlah,
                    a.nama_alat,
                    p.tanggal_kembali_rencana,
                    p.tanggal_pengajuan
                FROM pengembalian pg
                JOIN detail_peminjaman dp
                    ON pg.id_detail = dp.id_detail
                JOIN alat a
                    ON dp.id_alat = a.id_alat
                JOIN peminjaman p
                    ON dp.id_peminjaman = p.id_peminjaman
                ORDER BY pg.id_pengembalian DESC";

        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Mengambil satu data pengembalian berdasarkan ID
    public function get_by_id($id)
    {
        $sql = "SELECT *
                FROM pengembalian
                WHERE id_pengembalian = ?";

        $stmt = mysqli_prepare($this->db, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data ?: false;
    }

    // Mengambil detail peminjaman yang belum dikembalikan
    public function daftar_detail_belum_kembali()
    {
        $sql = "SELECT
                    dp.id_detail,
                    dp.id_peminjaman,
                    dp.id_alat,
                    dp.jumlah,
                    a.nama_alat,
                    p.tanggal_kembali_rencana,
                    p.tanggal_pengajuan,
                    pp.id_pengajuan_kembali,
                    pp.tanggal_pengajuan AS tanggal_pengajuan_kembali
                FROM detail_peminjaman dp
                JOIN peminjaman p
                    ON dp.id_peminjaman = p.id_peminjaman
                JOIN alat a
                    ON dp.id_alat = a.id_alat
                JOIN pengajuan_pengembalian pp
                    ON pp.id_detail = dp.id_detail
                WHERE p.status = 'disetujui'
                  AND pp.status = 'diproses'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM pengembalian pg
                      WHERE pg.id_detail = dp.id_detail
                  )
                ORDER BY dp.id_detail DESC";

        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Mengambil tanggal jatuh tempo satu detail peminjaman
    public function get_tanggal_jatuh_tempo($id_detail)
    {
        $sql = "SELECT p.tanggal_kembali_rencana
                FROM detail_peminjaman dp
                JOIN peminjaman p
                    ON dp.id_peminjaman = p.id_peminjaman
                WHERE dp.id_detail = ?
                  AND p.status = 'disetujui'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM pengembalian pg
                      WHERE pg.id_detail = dp.id_detail
                  )
                LIMIT 1";

        $stmt = mysqli_prepare($this->db, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id_detail);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data["tanggal_kembali_rencana"] ?? false;
    }

    // Menghitung denda Rp2.000 per hari keterlambatan
    public function hitung_denda($tanggal_rencana, $tanggal_kembali)
    {
        $jatuhTempo = DateTime::createFromFormat(
            "!Y-m-d",
            $tanggal_rencana
        );

        $tanggalKembali = DateTime::createFromFormat(
            "!Y-m-d",
            $tanggal_kembali
        );

        if (
            !$jatuhTempo ||
            !$tanggalKembali ||
            $jatuhTempo->format("Y-m-d") !== $tanggal_rencana ||
            $tanggalKembali->format("Y-m-d") !== $tanggal_kembali
        ) {
            return false;
        }

        if ($tanggalKembali <= $jatuhTempo) {
            return [
                "hari_terlambat" => 0,
                "total_denda" => 0
            ];
        }

        $hari = (int) $jatuhTempo->diff($tanggalKembali)->days;

        return [
            "hari_terlambat" => $hari,
            "total_denda" => $hari * $this->tarif_denda
        ];
    }

    // Menyimpan pengembalian, memperbarui status pengajuan,
    // dan mengembalikan stok dalam satu transaksi database.
    public function tambah(
        $id_detail,
        $tanggal_kembali,
        $kondisi_kembali,
        $keterangan,
        $status_bayar
    ) {
        mysqli_begin_transaction($this->db);

        try {
            // Ambil dan kunci detail peminjaman
            $sqlDetail = "SELECT
                              dp.id_detail,
                              dp.id_peminjaman,
                              dp.id_alat,
                              dp.jumlah,
                              p.tanggal_kembali_rencana
                          FROM detail_peminjaman dp
                          JOIN peminjaman p
                              ON dp.id_peminjaman = p.id_peminjaman
                          WHERE dp.id_detail = ?
                            AND p.status = 'disetujui'
                          FOR UPDATE";

            $stmt = mysqli_prepare($this->db, $sqlDetail);

            if (!$stmt) {
                throw new Exception("Gagal menyiapkan data peminjaman.");
            }

            mysqli_stmt_bind_param($stmt, "i", $id_detail);
            mysqli_stmt_execute($stmt);

            $detail = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmt)
            );

            mysqli_stmt_close($stmt);

            if (!$detail) {
                throw new Exception(
                    "Peminjaman tidak ditemukan atau sudah selesai."
                );
            }

            // Pastikan detail belum pernah dikembalikan
            $sqlCek = "SELECT id_pengembalian
                       FROM pengembalian
                       WHERE id_detail = ?
                       LIMIT 1";

            $stmtCek = mysqli_prepare($this->db, $sqlCek);

            if (!$stmtCek) {
                throw new Exception("Gagal memeriksa data pengembalian.");
            }

            mysqli_stmt_bind_param($stmtCek, "i", $id_detail);
            mysqli_stmt_execute($stmtCek);

            $sudahKembali = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmtCek)
            );

            mysqli_stmt_close($stmtCek);

            if ($sudahKembali) {
                throw new Exception(
                    "Alat ini sudah tercatat sebagai pengembalian."
                );
            }

            // Pastikan ada pengajuan yang sedang diproses
            $sqlPengajuan = "SELECT id_pengajuan_kembali
                             FROM pengajuan_pengembalian
                             WHERE id_detail = ?
                               AND status = 'diproses'
                             ORDER BY id_pengajuan_kembali DESC
                             LIMIT 1
                             FOR UPDATE";

            $stmtPengajuan = mysqli_prepare(
                $this->db,
                $sqlPengajuan
            );

            if (!$stmtPengajuan) {
                throw new Exception(
                    "Gagal memeriksa pengajuan pengembalian."
                );
            }

            mysqli_stmt_bind_param(
                $stmtPengajuan,
                "i",
                $id_detail
            );

            mysqli_stmt_execute($stmtPengajuan);

            $pengajuan = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmtPengajuan)
            );

            mysqli_stmt_close($stmtPengajuan);

            if (!$pengajuan) {
                throw new Exception(
                    "Pengajuan harus diproses terlebih dahulu oleh Petugas."
                );
            }

            // Hitung denda dari tanggal jatuh tempo database
            $hasilDenda = $this->hitung_denda(
                $detail["tanggal_kembali_rencana"],
                $tanggal_kembali
            );

            if ($hasilDenda === false) {
                throw new Exception("Tanggal pengembalian tidak valid.");
            }

            $denda = $hasilDenda["total_denda"];

            if ($denda <= 0) {
                $status_bayar = "lunas";
            }

            if (!in_array(
                $status_bayar,
                ["belum_bayar", "lunas"],
                true
            )) {
                throw new Exception("Status pembayaran tidak valid.");
            }

            // Simpan data pengembalian
            $sqlInsert = "INSERT INTO pengembalian
                            (
                                id_detail,
                                tanggal_kembali,
                                kondisi_kembali,
                                denda,
                                keterangan,
                                status_bayar
                            )
                          VALUES (?, ?, ?, ?, ?, ?)";

            $stmtInsert = mysqli_prepare($this->db, $sqlInsert);

            if (!$stmtInsert) {
                throw new Exception(
                    "Gagal menyiapkan penyimpanan pengembalian."
                );
            }

            mysqli_stmt_bind_param(
                $stmtInsert,
                "issdss",
                $id_detail,
                $tanggal_kembali,
                $kondisi_kembali,
                $denda,
                $keterangan,
                $status_bayar
            );

            if (!mysqli_stmt_execute($stmtInsert)) {
                throw new Exception("Gagal menyimpan pengembalian.");
            }

            mysqli_stmt_close($stmtInsert);

            // Tandai pengajuan sebagai diterima
            $id_pengajuan = (int) $pengajuan["id_pengajuan_kembali"];

            $sqlUpdatePengajuan = "UPDATE pengajuan_pengembalian
                                   SET status = 'diterima'
                                   WHERE id_pengajuan_kembali = ?
                                     AND status = 'diproses'";

            $stmtUpdatePengajuan = mysqli_prepare(
                $this->db,
                $sqlUpdatePengajuan
            );

            if (!$stmtUpdatePengajuan) {
                throw new Exception(
                    "Gagal menyiapkan pembaruan status pengajuan."
                );
            }

            mysqli_stmt_bind_param(
                $stmtUpdatePengajuan,
                "i",
                $id_pengajuan
            );

            if (
                !mysqli_stmt_execute($stmtUpdatePengajuan) ||
                mysqli_stmt_affected_rows($stmtUpdatePengajuan) !== 1
            ) {
                throw new Exception(
                    "Status pengajuan pengembalian gagal diperbarui."
                );
            }

            mysqli_stmt_close($stmtUpdatePengajuan);

            // Kembalikan jumlah stok yang dipinjam
            $jumlah = (int) $detail["jumlah"];
            $id_alat = (int) $detail["id_alat"];

            $sqlStok = "UPDATE alat
                        SET jumlah = jumlah + ?,
                            status = CASE
                                WHEN status = 'tidak_aktif'
                                THEN status
                                ELSE 'tersedia'
                            END
                        WHERE id_alat = ?";

            $stmtStok = mysqli_prepare($this->db, $sqlStok);

            if (!$stmtStok) {
                throw new Exception("Gagal menyiapkan pembaruan stok.");
            }

            mysqli_stmt_bind_param(
                $stmtStok,
                "ii",
                $jumlah,
                $id_alat
            );

            if (
                !mysqli_stmt_execute($stmtStok) ||
                mysqli_stmt_affected_rows($stmtStok) !== 1
            ) {
                throw new Exception("Stok alat gagal diperbarui.");
            }

            mysqli_stmt_close($stmtStok);

            // Periksa apakah semua detail sudah dikembalikan
            $sqlCekSemua = "SELECT
                                COUNT(*) AS total_detail,
                                SUM(
                                    CASE WHEN EXISTS (
                                        SELECT 1
                                        FROM pengembalian pg
                                        WHERE pg.id_detail = dp.id_detail
                                    )
                                    THEN 1 ELSE 0 END
                                ) AS total_kembali
                            FROM detail_peminjaman dp
                            WHERE dp.id_peminjaman = ?";

            $stmtSemua = mysqli_prepare(
                $this->db,
                $sqlCekSemua
            );

            if (!$stmtSemua) {
                throw new Exception(
                    "Gagal memeriksa detail peminjaman."
                );
            }

            $id_peminjaman = (int) $detail["id_peminjaman"];

            mysqli_stmt_bind_param(
                $stmtSemua,
                "i",
                $id_peminjaman
            );

            mysqli_stmt_execute($stmtSemua);

            $dataSemua = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmtSemua)
            );

            mysqli_stmt_close($stmtSemua);

            // Ubah status peminjaman menjadi selesai jika semuanya kembali
            if (
                $dataSemua &&
                (int) $dataSemua["total_detail"] > 0 &&
                (int) $dataSemua["total_detail"] ===
                (int) $dataSemua["total_kembali"]
            ) {
                $sqlSelesai = "UPDATE peminjaman
                               SET status = 'selesai'
                               WHERE id_peminjaman = ?
                                 AND status = 'disetujui'";

                $stmtSelesai = mysqli_prepare(
                    $this->db,
                    $sqlSelesai
                );

                if (!$stmtSelesai) {
                    throw new Exception(
                        "Gagal menyiapkan status peminjaman."
                    );
                }

                mysqli_stmt_bind_param(
                    $stmtSelesai,
                    "i",
                    $id_peminjaman
                );

                if (!mysqli_stmt_execute($stmtSelesai)) {
                    throw new Exception(
                        "Gagal memperbarui status peminjaman."
                    );
                }

                mysqli_stmt_close($stmtSelesai);
            }

            mysqli_commit($this->db);
            return true;

        } catch (Throwable $e) {
            mysqli_rollback($this->db);

            error_log(
                "Gagal mencatat pengembalian: " . $e->getMessage()
            );

            return false;
        }
    }

    // Memperbarui status pembayaran denda
    public function update_status_bayar($id_pengembalian, $status_bayar)
    {
        if (!in_array(
            $status_bayar,
            ["belum_bayar", "lunas"],
            true
        )) {
            return false;
        }

        $sql = "UPDATE pengembalian
                SET status_bayar = ?
                WHERE id_pengembalian = ?";

        $stmt = mysqli_prepare($this->db, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $status_bayar,
            $id_pengembalian
        );

        $berhasil = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $berhasil;
    }

    // Menghapus data pengembalian
    public function hapus($id_pengembalian)
    {
        $sql = "DELETE FROM pengembalian
                WHERE id_pengembalian = ?";

        $stmt = mysqli_prepare($this->db, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id_pengembalian
        );

        $berhasil = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $berhasil;
    }
}

?>
