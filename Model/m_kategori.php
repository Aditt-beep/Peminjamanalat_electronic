
<?php
require_once __DIR__ . "/m_koneksi.php";

class m_kategori
{
    public function tampil_data()
    {
        $koneksi = new koneksi();

        $sql = "SELECT * FROM kategori ORDER BY id_kategori DESC";
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

        $sql = "SELECT * FROM kategori WHERE id_kategori = ?";
        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $query = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($query);
    }

    public function tambah($nama_kategori)
    {
        $koneksi = new koneksi();

        $sql = "INSERT INTO kategori (nama_kategori) VALUES (?)";
        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param($stmt, "s", $nama_kategori);

        return mysqli_stmt_execute($stmt);
    }

    public function update($id_kategori, $nama_kategori)
    {
        $koneksi = new koneksi();

        $sql = "UPDATE kategori
                SET nama_kategori = ?
                WHERE id_kategori = ?";

        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $nama_kategori,
            $id_kategori
        );

        return mysqli_stmt_execute($stmt);
    }

    public function hapus($id)
    {
        $koneksi = new koneksi();

        $sql = "DELETE FROM kategori WHERE id_kategori = ?";
        $stmt = mysqli_prepare($koneksi->koneksi, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }
}
?>
