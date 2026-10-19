
<?php
session_start();

require_once __DIR__ . '/../Model/m_kategori.php';

if (
    !isset($_SESSION['data']) ||
    ($_SESSION['data']['role'] ?? '') !== 'admin'
) {
    header("Location: ../View/v_login.php");
    exit;
}

$model = new m_kategori();

/* =========================
   TAMBAH KATEGORI
========================= */
if (isset($_POST['tambah'])) {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if ($nama_kategori === '') {
        echo "<script>
            alert('Nama kategori wajib diisi.');
            window.location.href = '../View/v_tambah_kategori.php';
        </script>";
        exit;
    }

    try {
        $berhasil = $model->tambah($nama_kategori);

        if ($berhasil) {
            echo "<script>
                alert('Kategori berhasil ditambahkan!');
                window.location.href = '../View/v_kategori.php';
            </script>";
            exit;
        }

        echo "<script>
            alert('Kategori gagal ditambahkan.');
            window.location.href = '../View/v_tambah_kategori.php';
        </script>";
        exit;

    } catch (mysqli_sql_exception $e) {
        error_log('Tambah kategori: ' . $e->getMessage());

        echo "<script>
            alert('Gagal menambahkan kategori. Periksa kembali data kategori.');
            window.location.href = '../View/v_tambah_kategori.php';
        </script>";
        exit;
    }
}

/* =========================
   EDIT KATEGORI
========================= */
if (isset($_POST['edit'])) {
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if ($id_kategori <= 0 || $nama_kategori === '') {
        echo "<script>
            alert('Data kategori tidak valid.');
            window.location.href = '../View/v_kategori.php';
        </script>";
        exit;
    }

    try {
        $berhasil = $model->update($id_kategori, $nama_kategori);

        echo "<script>
            alert(" . json_encode(
                $berhasil
                    ? 'Kategori berhasil diperbarui!'
                    : 'Kategori gagal diperbarui.'
            ) . ");
            window.location.href = '../View/v_kategori.php';
        </script>";
        exit;

    } catch (mysqli_sql_exception $e) {
        error_log('Edit kategori: ' . $e->getMessage());

        echo "<script>
            alert('Gagal memperbarui kategori.');
            window.location.href = '../View/v_kategori.php';
        </script>";
        exit;
    }
}

/* =========================
   HAPUS KATEGORI
========================= */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    try {
        $berhasil = $id > 0 && $model->hapus($id);

        echo "<script>
            alert(" . json_encode(
                $berhasil
                    ? 'Kategori berhasil dihapus!'
                    : 'Kategori gagal dihapus.'
            ) . ");
            window.location.href = '../View/v_kategori.php';
        </script>";
        exit;

    } catch (mysqli_sql_exception $e) {
        error_log('Hapus kategori: ' . $e->getMessage());

        echo "<script>
            alert('Kategori tidak bisa dihapus karena mungkin masih digunakan oleh data alat.');
            window.location.href = '../View/v_kategori.php';
        </script>";
        exit;
    }
}

// Jika controller dibuka langsung tanpa aksi
header("Location: ../View/v_kategori.php");
exit;
?>
