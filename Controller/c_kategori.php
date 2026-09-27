```php
<?php
include_once __DIR__ . "/../Model/m_kategori.php";

class c_kategori {

    function __construct() {
        $this->model = new m_kategori();
    }

    function index() {
        $data = $this->model->tampil_data();

        include_once __DIR__ . "/../View/v_kategori.php";
    }

    function tambah() {
        include_once __DIR__ . "/../View/v_tambah_kategori.php";
    }

    function proses_tambah() {
        $nama_kategori = $_POST['nama_kategori'];

        $this->model->tambah($nama_kategori);

        header("Location: index.php?page=kategori");
        exit;
    }

    function edit($id) {
        $data = $this->model->get_by_id($id);

        include_once __DIR__ . "/../View/v_edit_kategori.php";
    }

    function proses_edit() {
        $id_kategori = $_POST['id_kategori'];
        $nama_kategori = $_POST['nama_kategori'];

        $this->model->update($id_kategori, $nama_kategori);

        header("Location: index.php?page=kategori");
        exit;
    }

    function hapus($id) {
        $this->model->hapus($id);

        header("Location: index.php?page=kategori");
        exit;
    }
}
