<?php

include_once __DIR__ . '/../Model/m_alat.php';

class c_alat
{
    private $model;

    public function __construct()
    {
        $this->model = new m_alat();
    }

    public function index()
    {
        $data = $this->model->tampilData();
        include __DIR__ . '/../View/v_alat.php';
    }

    public function tambah()
    {
        include __DIR__ . '/../View/v_tambah_alat.php';
    }

    public function simpan()
    {
        $kode_alat = trim($_POST['kode_alat']);
        $nama_alat = trim($_POST['nama_alat']);
        $id_kategori = $_POST['id_kategori'];
        $merk = trim($_POST['merk']);
        $jumlah = $_POST['jumlah'];
        $kondisi = $_POST['kondisi'];
        $status = $_POST['status'];
        $deskripsi = trim($_POST['deskripsi']);

        $this->model->tambahAlat(
            $kode_alat,
            $nama_alat,
            $id_kategori,
            $merk,
            $jumlah,
            $kondisi,
            $status,
            $deskripsi
        );

        header("Location: ../index.php?route=alat");
        exit;
    }

    public function edit()
    {
        $id_alat = $_GET['id'];

        $result = $this->model->ambilAlat($id_alat);
        $alat = $result->fetch_assoc();

        include __DIR__ . '/../View/v_edit_alat.php';
    }

    public function update()
    {
        $id_alat = $_POST['id_alat'];
        $kode_alat = trim($_POST['kode_alat']);
        $nama_alat = trim($_POST['nama_alat']);
        $id_kategori = $_POST['id_kategori'];
        $merk = trim($_POST['merk']);
        $jumlah = $_POST['jumlah'];
        $kondisi = $_POST['kondisi'];
        $status = $_POST['status'];
        $deskripsi = trim($_POST['deskripsi']);

        $this->model->editAlat(
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

        header("Location: ../index.php?route=alat");
        exit;
    }

    public function hapus()
    {
        $id_alat = $_GET['id'];
        $this->model->hapusAlat($id_alat);
        header("Location: ../index.php?route=alat");
        exit;
    }
}