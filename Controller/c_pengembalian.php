<?php

include_once __DIR__ . "/../Model/m_pengembalian.php";

class c_pengembalian
{
    private $model;

    function __construct()
    {
        $this->model = new m_pengembalian();
    }

    function index()
    {
        $data_pengembalian = $this->model->tampil_data();

        include __DIR__ . "/../View/v_pengembalian.php";
    }

    function tambah()
    {
        include __DIR__ . "/../View/v_tambah_pengembalian.php";
    }

    function simpan()
    {
        $id_peminjaman = $_POST['id_peminjaman'];
        $tanggal_pengembalian = $_POST['tanggal_pengembalian'];
        $kondisi = $_POST['kondisi'];
        $denda = $_POST['denda'];

        $this->model->tambah(
            $id_peminjaman,
            $tanggal_pengembalian,
            $kondisi,
            $denda
        );

        header("Location: ../View/v_pengembalian.php");
        exit;
    }

    function edit($id)
    {
        $data = $this->model->get_by_id($id);

        include __DIR__ . "/../View/v_edit_pengembalian.php";
    }

    function update()
    {
        $id_pengembalian = $_POST['id_pengembalian'];
        $id_peminjaman = $_POST['id_peminjaman'];
        $tanggal_pengembalian = $_POST['tanggal_pengembalian'];
        $kondisi = $_POST['kondisi'];
        $denda = $_POST['denda'];

        $this->model->update(
            $id_pengembalian,
            $id_peminjaman,
            $tanggal_pengembalian,
            $kondisi,
            $denda
        );

        header("Location: ../View/v_pengembalian.php");
        exit;
    }

    function hapus($id)
    {
        $this->model->hapus($id);

        header("Location: ../View/v_pengembalian.php");
        exit;
    }
}