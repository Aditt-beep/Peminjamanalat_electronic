
<?php
include_once __DIR__ . "/m_koneksi.php";

class m_alat
{
    public function tampil_data()
    {
        $koneksi = new koneksi();

        $sql = "SELECT alat.*, kategori.nama_kategori
                FROM alat
                LEFT JOIN kategori
                    ON alat.id_kategori = kategori.id_kategori
                ORDER BY alat.id_alat DESC";

        $query = mysqli_query($koneksi->koneksi, $sql);

        $result = [];

        while ($data = mysqli_fetch_assoc($query)) {
            $result[] = $data;
        }

        return $result;
    }

    public function get_by_id($id)
    {
        $koneksi = new koneksi();

        $sql = "SELECT * FROM alat WHERE id_alat = ?";
        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $query = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($query);
    }

    public function tambah(
        $kode_alat,
        $nama_alat,
        $id_kategori,
        $merk,
        $jumlah,
        $kondisi,
        $status,
        $deskripsi = ""
    ) {
        $koneksi = new koneksi();

        $sql = "INSERT INTO alat
                    (kode_alat, nama_alat, id_kategori, merk,
                     jumlah, kondisi, status, deskripsi)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssisisss",
            $kode_alat,
            $nama_alat,
            $id_kategori,
            $merk,
            $jumlah,
            $kondisi,
            $status,
            $deskripsi
        );

        return mysqli_stmt_execute($stmt);
    }

    public function update(
        $id_alat,
        $kode_alat,
        $nama_alat,
        $id_kategori,
        $merk,
        $jumlah,
        $kondisi,
        $status,
        $deskripsi = ""
    ) {
        $koneksi = new koneksi();

        $sql = "UPDATE alat SET
                    kode_alat = ?,
                    nama_alat = ?,
                    id_kategori = ?,
                    merk = ?,
                    jumlah = ?,
                    kondisi = ?,
                    status = ?,
                    deskripsi = ?
                WHERE id_alat = ?";

        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssisisssi",
            $kode_alat,
            $nama_alat,
            $id_kategori,
            $merk,
            $jumlah,
            $kondisi,
            $status,
            $deskripsi,
            $id_alat
        );

        return mysqli_stmt_execute($stmt);
    }

    public function hapus($id)
    {
        $koneksi = new koneksi();

        $sql = "DELETE FROM alat WHERE id_alat = ?";
        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }
}
?>
