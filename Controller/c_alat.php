
<?php
session_start();

require_once __DIR__ . '/../Model/m_alat.php';

if (
    !isset($_SESSION['data']) ||
    ($_SESSION['data']['role'] ?? '') !== 'admin'
) {
    header("Location: /Pinjaminn/View/v_login.php");
    exit;
}

$model = new m_alat();

function kembaliDenganPesan($pesan, $halaman)
{
    echo "<script>
        alert(" . json_encode($pesan) . ");
        window.location.href = " . json_encode($halaman) . ";
    </script>";
    exit;
}

$halamanAlat = '/Pinjaminn/View/v_alat.php';
$halamanTambah = '/Pinjaminn/View/v_tambah_alat.php';

/* =========================
   TAMBAH ALAT
========================= */
if (isset($_POST['tambah'])) {
    $kode_alat   = trim($_POST['kode_alat'] ?? '');
    $nama_alat   = trim($_POST['nama_alat'] ?? '');
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $merk        = trim($_POST['merk'] ?? '');
    $jumlah      = (int)($_POST['jumlah'] ?? 0);
    $kondisi     = $_POST['kondisi'] ?? '';
    $status      = $_POST['status'] ?? '';
    $deskripsi   = trim($_POST['deskripsi'] ?? '');

    $kondisiValid = ['baik', 'rusak_ringan', 'rusak_berat'];
    $statusValid = ['tersedia', 'dipinjam', 'tidak_aktif'];

    if (
        $kode_alat === '' ||
        $nama_alat === '' ||
        $id_kategori <= 0 ||
        $jumlah < 1 ||
        !in_array($kondisi, $kondisiValid, true) ||
        !in_array($status, $statusValid, true)
    ) {
        kembaliDenganPesan(
            'Data tidak lengkap atau tidak valid.',
            $halamanTambah
        );
    }

    try {
        $berhasil = $model->tambah(
            $kode_alat,
            $nama_alat,
            $id_kategori,
            $merk,
            $jumlah,
            $kondisi,
            $status,
            $deskripsi
        );

        kembaliDenganPesan(
            $berhasil
                ? 'Alat berhasil ditambahkan!'
                : 'Gagal menambahkan alat.',
            $berhasil ? $halamanAlat : $halamanTambah
        );

    } catch (mysqli_sql_exception $e) {
        error_log('Tambah alat: ' . $e->getMessage());

        kembaliDenganPesan(
            'Gagal menambahkan alat. Periksa kode alat dan kategori.',
            $halamanTambah
        );
    }
}

/* =========================
   EDIT ALAT
========================= */
if (isset($_POST['edit'])) {
    $id_alat     = (int)($_POST['id_alat'] ?? 0);
    $kode_alat   = trim($_POST['kode_alat'] ?? '');
    $nama_alat   = trim($_POST['nama_alat'] ?? '');
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $merk        = trim($_POST['merk'] ?? '');
    $jumlah      = (int)($_POST['jumlah'] ?? 0);
    $kondisi     = $_POST['kondisi'] ?? '';
    $status      = $_POST['status'] ?? '';
    $deskripsi   = trim($_POST['deskripsi'] ?? '');

    $kondisiValid = ['baik', 'rusak_ringan', 'rusak_berat'];
    $statusValid = ['tersedia', 'dipinjam', 'tidak_aktif'];

    if (
        $id_alat <= 0 ||
        $kode_alat === '' ||
        $nama_alat === '' ||
        $id_kategori <= 0 ||
        $jumlah < 1 ||
        !in_array($kondisi, $kondisiValid, true) ||
        !in_array($status, $statusValid, true)
    ) {
        kembaliDenganPesan(
            'Data edit tidak lengkap atau tidak valid.',
            $halamanAlat
        );
    }

    try {
        $berhasil = $model->update(
            $id_alat,
            $kode_alat,
            $nama_alat,
            $id_kategori,
            $merk,
            $jumlah,
            $kondisi,
            $status,
            $deskripsi
        );

        kembaliDenganPesan(
            $berhasil
                ? 'Data alat berhasil diperbarui!'
                : 'Data alat gagal diperbarui.',
            $halamanAlat
        );

    } catch (mysqli_sql_exception $e) {
        error_log('Edit alat: ' . $e->getMessage());

        kembaliDenganPesan(
            'Gagal memperbarui alat. Periksa kode alat dan kategori.',
            $halamanAlat
        );
    }
}

/* =========================
   HAPUS ALAT
========================= */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    try {
        $berhasil = $id > 0 && $model->hapus($id);

        kembaliDenganPesan(
            $berhasil
                ? 'Alat berhasil dihapus!'
                : 'Alat gagal dihapus.',
            $halamanAlat
        );

    } catch (mysqli_sql_exception $e) {
        error_log('Hapus alat: ' . $e->getMessage());

        kembaliDenganPesan(
            'Alat tidak dapat dihapus karena mungkin masih digunakan dalam data peminjaman.',
            $halamanAlat
        );
    }
}

header("Location: " . $halamanAlat);
exit;
?>
