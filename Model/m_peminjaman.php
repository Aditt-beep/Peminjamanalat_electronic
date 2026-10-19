
<?php
require_once __DIR__ . "/m_koneksi.php";

class m_peminjaman
{
    private function koneksi()
    {
        $db = new koneksi();
        return $db->koneksi;
    }

    // =====================================================
    // CRUD ADMIN: TAMPIL DATA PEMINJAMAN
    // =====================================================
    public function tampil_data()
    {
        $conn = $this->koneksi();

        $sql = "SELECT p.*, u.nama, u.username
                FROM peminjaman p
                LEFT JOIN tb_user u ON p.id_user = u.id_user
                ORDER BY p.id_peminjaman DESC";

        $query = mysqli_query($conn, $sql);
        $hasil = [];

        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $hasil[] = $row;
            }
        }

        return $hasil;
    }

    // =====================================================
    // CRUD ADMIN: DAFTAR USER UNTUK DROPDOWN
    // =====================================================
    public function daftar_user()
    {
        $conn = $this->koneksi();

        $sql = "SELECT id_user, nama, username
                FROM tb_user
                WHERE status_aktif = 1
                ORDER BY nama ASC";

        $query = mysqli_query($conn, $sql);
        $hasil = [];

        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $hasil[] = $row;
            }
        }

        return $hasil;
    }

    // =====================================================
    // CRUD ADMIN: AMBIL SATU DATA
    // =====================================================
    public function get_by_id($id)
    {
        $conn = $this->koneksi();

        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM peminjaman WHERE id_peminjaman = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $query = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($query);

        mysqli_stmt_close($stmt);

        return $data;
    }

    // =====================================================
    // CRUD ADMIN: TAMBAH DATA
    // =====================================================
    public function tambah(
        $id_user,
        $tanggal_pengajuan,
        $tanggal_pinjam,
        $tanggal_kembali_rencana,
        $keperluan,
        $status
    ) {
        $conn = $this->koneksi();

        $sql = "INSERT INTO peminjaman
                (
                    id_user,
                    tanggal_pengajuan,
                    tanggal_pinjam,
                    tanggal_kembali_rencana,
                    keperluan,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "isssss",
            $id_user,
            $tanggal_pengajuan,
            $tanggal_pinjam,
            $tanggal_kembali_rencana,
            $keperluan,
            $status
        );

        if (!mysqli_stmt_execute($stmt)) {
            $pesan = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            throw new Exception($pesan);
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    // =====================================================
    // CRUD ADMIN: EDIT DATA
    // =====================================================
    public function update(
        $id_peminjaman,
        $id_user,
        $tanggal_pengajuan,
        $tanggal_pinjam,
        $tanggal_kembali_rencana,
        $keperluan,
        $status
    ) {
        $conn = $this->koneksi();

        $sql = "UPDATE peminjaman SET
                    id_user = ?,
                    tanggal_pengajuan = ?,
                    tanggal_pinjam = ?,
                    tanggal_kembali_rencana = ?,
                    keperluan = ?,
                    status = ?
                WHERE id_peminjaman = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "isssssi",
            $id_user,
            $tanggal_pengajuan,
            $tanggal_pinjam,
            $tanggal_kembali_rencana,
            $keperluan,
            $status,
            $id_peminjaman
        );

        if (!mysqli_stmt_execute($stmt)) {
            $pesan = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            throw new Exception($pesan);
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    // =====================================================
    // CRUD ADMIN: HAPUS DATA
    // =====================================================
    public function hapus($id)
    {
        $conn = $this->koneksi();

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM peminjaman WHERE id_peminjaman = ?"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $id);

        if (!mysqli_stmt_execute($stmt)) {
            $pesan = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            throw new Exception($pesan);
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    // =====================================================
    // PENGAJUAN PEMINJAM: DAFTAR PENGAJUAN UNTUK PETUGAS
    // =====================================================
    public function daftar_pengajuan()
    {
        $conn = $this->koneksi();

        $sql = "SELECT p.*, u.nama, u.username
                FROM peminjaman p
                JOIN tb_user u ON p.id_user = u.id_user
                ORDER BY p.id_peminjaman DESC";

        $query = mysqli_query($conn, $sql);
        $hasil = [];

        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $hasil[] = $row;
            }
        }

        return $hasil;
    }

    // =====================================================
    // PENGAJUAN PEMINJAM: PENGAJUAN MILIK USER
    // =====================================================
    public function pengajuan_saya($id_user)
    {
        $conn = $this->koneksi();

        $stmt = mysqli_prepare(
            $conn,
            "SELECT p.*, a.nama_alat, d.jumlah
             FROM peminjaman p
             LEFT JOIN detail_peminjaman d
                ON p.id_peminjaman = d.id_peminjaman
             LEFT JOIN alat a ON d.id_alat = a.id_alat
             WHERE p.id_user = ?
             ORDER BY p.id_peminjaman DESC"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);

        $query = mysqli_stmt_get_result($stmt);
        $hasil = [];

        while ($row = mysqli_fetch_assoc($query)) {
            $hasil[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $hasil;
    }

    // =====================================================
    // PENGAJUAN PEMINJAM: AJUKAN PEMINJAMAN ALAT
    // =====================================================
    public function ajukan(
        $id_user,
        $id_alat,
        $jumlah,
        $tanggal_pinjam,
        $tanggal_kembali,
        $keperluan
    ) {
        $conn = $this->koneksi();

        if ($jumlah < 1) {
            return [
                "ok" => false,
                "pesan" => "Jumlah alat minimal 1."
            ];
        }

        if ($tanggal_pinjam < date("Y-m-d")) {
            return [
                "ok" => false,
                "pesan" => "Tanggal pinjam tidak boleh sudah lewat."
            ];
        }

        if ($tanggal_kembali < $tanggal_pinjam) {
            return [
                "ok" => false,
                "pesan" => "Tanggal kembali harus sama atau setelah tanggal pinjam."
            ];
        }

        mysqli_begin_transaction($conn);

        try {
            $stmt = mysqli_prepare(
                $conn,
                "SELECT id_alat, nama_alat, jumlah, kondisi, status
                 FROM alat
                 WHERE id_alat = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param($stmt, "i", $id_alat);
            mysqli_stmt_execute($stmt);

            $alat = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmt)
            );

            mysqli_stmt_close($stmt);

            if (
                !$alat ||
                $alat["status"] !== "tersedia" ||
                $alat["kondisi"] === "rusak_berat" ||
                (int) $alat["jumlah"] < $jumlah
            ) {
                throw new Exception(
                    "Alat tidak tersedia atau jumlah stok tidak mencukupi."
                );
            }

            $tanggal_pengajuan = date("Y-m-d");
            $status = "menunggu";
            $disetujui_oleh = null;
            $keterangan = "";

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO peminjaman
                 (
                    id_user,
                    tanggal_pengajuan,
                    tanggal_pinjam,
                    tanggal_kembali_rencana,
                    keperluan,
                    status,
                    disetujui_oleh,
                    keterangan
                 )
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "isssssis",
                $id_user,
                $tanggal_pengajuan,
                $tanggal_pinjam,
                $tanggal_kembali,
                $keperluan,
                $status,
                $disetujui_oleh,
                $keterangan
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Pengajuan gagal disimpan.");
            }

            $id_peminjaman = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            $kondisi_keluar = $alat["kondisi"];

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO detail_peminjaman
                 (id_peminjaman, id_alat, jumlah, kondisi_keluar)
                 VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "iiis",
                $id_peminjaman,
                $id_alat,
                $jumlah,
                $kondisi_keluar
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Detail alat gagal disimpan.");
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($conn);

            return [
                "ok" => true,
                "pesan" => "Pengajuan berhasil dikirim dan menunggu persetujuan petugas."
            ];

        } catch (Throwable $e) {
            mysqli_rollback($conn);

            return [
                "ok" => false,
                "pesan" => $e->getMessage()
            ];
        }
    }

    // =====================================================
    // PETUGAS: SETUJUI ATAU TOLAK PENGAJUAN
    // =====================================================
    public function proses_pengajuan(
        $id_peminjaman,
        $id_petugas,
        $aksi,
        $keterangan
    ) {
        $conn = $this->koneksi();

        if (!in_array($aksi, ["setujui", "tolak"], true)) {
            return [
                "ok" => false,
                "pesan" => "Aksi tidak valid."
            ];
        }

        mysqli_begin_transaction($conn);

        try {
            $stmt = mysqli_prepare(
                $conn,
                "SELECT p.status, d.id_alat, d.jumlah,
                        a.jumlah AS stok,
                        a.status AS status_alat,
                        a.kondisi AS kondisi_alat
                 FROM peminjaman p
                 JOIN detail_peminjaman d
                    ON p.id_peminjaman = d.id_peminjaman
                 JOIN alat a ON d.id_alat = a.id_alat
                 WHERE p.id_peminjaman = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $id_peminjaman
            );

            mysqli_stmt_execute($stmt);

            $data = mysqli_fetch_assoc(
                mysqli_stmt_get_result($stmt)
            );

            mysqli_stmt_close($stmt);

            if (!$data || $data["status"] !== "menunggu") {
                throw new Exception(
                    "Pengajuan tidak ditemukan atau sudah diproses."
                );
            }

            if ($aksi === "setujui") {
                if (
                    $data["status_alat"] !== "tersedia" ||
                    $data["kondisi_alat"] === "rusak_berat" ||
                    (int) $data["stok"] < (int) $data["jumlah"]
                ) {
                    throw new Exception(
                        "Stok alat saat ini tidak mencukupi untuk menyetujui pengajuan."
                    );
                }

                $status_baru = "disetujui";

                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE alat
                     SET jumlah = jumlah - ?
                     WHERE id_alat = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ii",
                    $data["jumlah"],
                    $data["id_alat"]
                );

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception(
                        "Stok alat gagal diperbarui."
                    );
                }

                mysqli_stmt_close($stmt);

            } else {
                $status_baru = "ditolak";
            }

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE peminjaman
                 SET status = ?,
                     disetujui_oleh = ?,
                     keterangan = ?
                 WHERE id_peminjaman = ?
                   AND status = 'menunggu'"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sisi",
                $status_baru,
                $id_petugas,
                $keterangan,
                $id_peminjaman
            );

            if (
                !mysqli_stmt_execute($stmt) ||
                mysqli_stmt_affected_rows($stmt) !== 1
            ) {
                throw new Exception(
                    "Status pengajuan gagal diperbarui."
                );
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($conn);

            return [
                "ok" => true,
                "pesan" => $aksi === "setujui"
                    ? "Pengajuan berhasil disetujui."
                    : "Pengajuan berhasil ditolak."
            ];

        } catch (Throwable $e) {
            mysqli_rollback($conn);

            return [
                "ok" => false,
                "pesan" => $e->getMessage()
            ];
        }
    }
}
?>
