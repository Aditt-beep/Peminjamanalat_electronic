<?php

include_once __DIR__ . "/../Model/m_peminjaman.php";

class c_peminjaman
{
    private $model;

    function __construct()
    {
        $this->model = new m_peminjaman();
    }

    function index()
    {
        $data_peminjaman = $this->model->tampil_data();

        include __DIR__ . "/../View/v_peminjaman.php";
    }

    function tambah()
    {
        include __DIR__ . "/../View/v_tambah_peminjaman.php";
    }

    function simpan()
    {
        $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
        $tanggal_peminjaman = $_POST['tanggal_peminjaman'];
        $tanggal_kembali_rencana = $_POST['tanggal_kembali_rencana'];
        $status = $_POST['status'];

        $this->model->tambah(
            $tanggal_pengajuan,
            $tanggal_peminjaman,
            $tanggal_kembali_rencana,
            $status
        );

        header("Location: ../View/v_peminjaman.php");
        exit;
    }

    function edit($id)
    {
        $data = $this->model->get_by_id($id);

        include __DIR__ . "/../View/v_edit_peminjaman.php";
    }

    function update()
    {
        $id_peminjaman = $_POST['id_peminjaman'];
        $tanggal_pengajuan = $_POST['tanggal_pengajuan'];
        $tanggal_peminjaman = $_POST['tanggal_peminjaman'];
        $tanggal_kembali_rencana = $_POST['tanggal_kembali_rencana'];
        $status = $_POST['status'];

        $this->model->update(
            $id_peminjaman,
            $tanggal_pengajuan,
            $tanggal_peminjaman,
            $tanggal_kembali_rencana,
            $status
        );

        header("Location: ../View/v_peminjaman.php");
        exit;
    }

    function hapus($id)
    {
        $this->model->hapus($id);

        header("Location: ../View/v_peminjaman.php");
        exit;
    }
}