
<?php

require_once __DIR__ . "/m_koneksi.php";

class m_pengajuan_pengembalian
{
    private $db;

    public function __construct()
    {
        $koneksi = new koneksi();
        $this->db = $koneksi->koneksi;
    }

    // Mengambil alat yang masih dipinjam oleh peminjam
    public function detail_peminjam($id_user)
    {
        $sql = "SELECT
                    dp.id_detail,
                    dp.jumlah,
                    a.nama_alat,
                    a.kode_alat,
                    p.id_peminjaman,
                    p.tanggal_pinjam,
                    p.tanggal_kembali_rencana
                FROM detail_peminjaman dp
                JOIN peminjaman p
                    ON dp.id_peminjaman = p.id_peminjaman
                JOIN alat a
                    ON dp.id_alat = a.id_alat
                WHERE p.id_user = ?
                    AND p.status = 'disetujui'
                    AND NOT EXISTS (
                        SELECT 1
                        FROM pengembalian pg
                        WHERE pg.id_detail = dp.id_detail
                    )
                    AND NOT EXISTS (
                        SELECT 1
                        FROM pengajuan_pengembalian pp
                        WHERE pp.id_detail = dp.id_detail
                        AND pp.status IN ('menunggu', 'diproses')
                    )
                ORDER BY p.tanggal_pinjam DESC";

        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);

        $hasil = mysqli_stmt_get_result($stmt);
        $data = [];

        while ($row = mysqli_fetch_assoc($hasil)) {
            $data[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $data;
    }

    // Menyimpan pengajuan dari peminjam
    public function ajukan($id_user, $id_detail, $keterangan = "")
    {
        mysqli_begin_transaction($this->db);

        try {
            $sql = "SELECT dp.id_detail
                    FROM detail_peminjaman dp
                    JOIN peminjaman p
                        ON dp.id_peminjaman = p.id_peminjaman
                    WHERE dp.id_detail = ?
                        AND p.id_user = ?
                        AND p.status = 'disetujui'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM pengembalian pg
                            WHERE pg.id_detail = dp.id_detail
                        )
                    FOR UPDATE";

            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $id_detail,
                $id_user
            );
            mysqli_stmt_execute($stmt);

            $hasil = mysqli_stmt_get_result($stmt);
            $detail = mysqli_fetch_assoc($hasil);

            mysqli_stmt_close($stmt);

            if (!$detail) {
                throw new Exception(
                    "Alat tidak ditemukan atau sudah dikembalikan."
                );
            }

            $sql = "SELECT id_pengajuan_kembali
                    FROM pengajuan_pengembalian
                    WHERE id_detail = ?
                    AND status IN ('menunggu', 'diproses')
                    LIMIT 1";

            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_detail);
            mysqli_stmt_execute($stmt);

            $hasil = mysqli_stmt_get_result($stmt);
            $pengajuan = mysqli_fetch_assoc($hasil);

            mysqli_stmt_close($stmt);

            if ($pengajuan) {
                throw new Exception(
                    "Pengajuan pengembalian alat ini masih diproses."
                );
            }

            $sql = "INSERT INTO pengajuan_pengembalian
                        (id_detail, status, keterangan)
                    VALUES (?, 'menunggu', ?)";

            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "is",
                $id_detail,
                $keterangan
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(
                    "Gagal menyimpan pengajuan pengembalian."
                );
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($this->db);

            return [
                "status" => true,
                "pesan" => "Pengajuan pengembalian berhasil dikirim."
            ];

        } catch (Throwable $e) {
            mysqli_rollback($this->db);

            return [
                "status" => false,
                "pesan" => $e->getMessage()
            ];
        }
    }

    // Mengambil daftar pengajuan untuk petugas
    public function daftar_petugas()
    {
        $sql = "SELECT
                    pp.id_pengajuan_kembali,
                    pp.id_detail,
                    pp.tanggal_pengajuan,
                    pp.status AS status_pengajuan,
                    pp.keterangan AS keterangan_pengajuan,
                    dp.jumlah,
                    a.nama_alat,
                    a.kode_alat,
                    u.nama,
                    u.username,
                    p.id_peminjaman,
                    p.tanggal_kembali_rencana
                FROM pengajuan_pengembalian pp
                JOIN detail_peminjaman dp
                    ON pp.id_detail = dp.id_detail
                JOIN peminjaman p
                    ON dp.id_peminjaman = p.id_peminjaman
                JOIN alat a
                    ON dp.id_alat = a.id_alat
                JOIN tb_user u
                    ON p.id_user = u.id_user
                ORDER BY pp.tanggal_pengajuan DESC";

        $hasil = mysqli_query($this->db, $sql);
        $data = [];

        if ($hasil) {
            while ($row = mysqli_fetch_assoc($hasil)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    // Konfirmasi pengembalian oleh petugas
    public function konfirmasi($id_pengajuan, $kondisi_kembali, $keterangan = "")
    {
        mysqli_begin_transaction($this->db);

        try {
            $sql = "SELECT
                        pp.id_pengajuan_kembali,
                        pp.id_detail,
                        pp.status,
                        dp.id_alat,
                        dp.jumlah,
                        p.id_peminjaman,
                        p.tanggal_kembali_rencana
                    FROM pengajuan_pengembalian pp
                    JOIN detail_peminjaman dp
                        ON pp.id_detail = dp.id_detail
                    JOIN peminjaman p
                        ON dp.id_peminjaman = p.id_peminjaman
                    WHERE pp.id_pengajuan_kembali = ?
                    FOR UPDATE";

            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
            mysqli_stmt_execute($stmt);

            $hasil = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($hasil);

            mysqli_stmt_close($stmt);

            if (!$data) {
                throw new Exception("Pengajuan tidak ditemukan.");
            }

            if ($data["status"] !== "menunggu") {
                throw new Exception(
                    "Pengajuan ini sudah pernah diproses."
                );
            }

            // Pastikan transaksi belum pernah dikembalikan
            $sql = "SELECT id_pengembalian
                    FROM pengembalian
                    WHERE id_detail = ?
                    LIMIT 1";

            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $data["id_detail"]
            );
            mysqli_stmt_execute($stmt);

            $hasil = mysqli_stmt_get_result($stmt);
            $sudah_kembali = mysqli_fetch_assoc($hasil);

            mysqli_stmt_close($stmt);

            if ($sudah_kembali) {
                throw new Exception(
                    "Alat ini sudah tercatat dikembalikan."
                );
            }

            // Hitung denda berdasarkan tanggal hari ini
            $tanggal_kembali = date("Y-m-d");

            $rencana = new DateTime(
                $data["tanggal_kembali_rencana"]
            );

            $aktual = new DateTime($tanggal_kembali);

            $selisih = (int) $rencana
                ->diff($aktual)
                ->format("%r%a");

            $denda = max(0, $selisih) * 2000;

            $status_bayar = $denda > 0
                ? "belum_bayar"
                : "lunas";

            // Simpan transaksi pengembalian
            $sql = "INSERT INTO pengembalian (
                        id_detail,
                        tanggal_kembali,
                        kondisi_kembali,
                        denda,
                        keterangan,
                        status_bayar
                    ) VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($this->db, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "issdss",
                $data["id_detail"],
                $tanggal_kembali,
                $kondisi_kembali,
                $denda,
                $keterangan,
                $status_bayar
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(
                    "Gagal menyimpan transaksi pengembalian."
                );
            }

            mysqli_stmt_close($stmt);

            // Tandai pengajuan sudah diterima
            $sql = "UPDATE pengajuan_pengembalian
                    SET status = 'diterima'
                    WHERE id_pengajuan_kembali = ?";

            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Kembalikan stok satu kali
            $sql = "UPDATE alat
                    SET jumlah = jumlah + ?
                    WHERE id_alat = ?";

            $stmt = mysqli_prepare($this->db, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $data["jumlah"],
                $data["id_alat"]
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal memperbarui stok alat.");
            }

            mysqli_stmt_close($stmt);

            // Cek apakah seluruh detail peminjaman sudah kembali
            $sql = "SELECT COUNT(*) AS belum_kembali
                    FROM detail_peminjaman dp
                    WHERE dp.id_peminjaman = ?
                    AND NOT EXISTS (
                        SELECT 1
                        FROM pengembalian pg
                        WHERE pg.id_detail = dp.id_detail
                    )";

            $stmt = mysqli_prepare($this->db, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $data["id_peminjaman"]
            );

            mysqli_stmt_execute($stmt);

            $hasil = mysqli_stmt_get_result($stmt);
            $jumlah_belum_kembali = mysqli_fetch_assoc($hasil);

            mysqli_stmt_close($stmt);

            if ((int) $jumlah_belum_kembali["belum_kembali"] === 0) {
                $sql = "UPDATE peminjaman
                        SET status = 'selesai'
                        WHERE id_peminjaman = ?";

                $stmt = mysqli_prepare($this->db, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "i",
                    $data["id_peminjaman"]
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            mysqli_commit($this->db);

            return [
                "status" => true,
                "pesan" => "Pengembalian berhasil dikonfirmasi. Denda: Rp"
                    . number_format($denda, 0, ",", ".")
            ];

        } catch (Throwable $e) {
            mysqli_rollback($this->db);

            return [
                "status" => false,
                "pesan" => $e->getMessage()
            ];
        }
    }
}
?>
