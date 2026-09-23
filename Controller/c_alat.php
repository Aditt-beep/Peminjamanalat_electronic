<?php

include_once __DIR__ . "/../Model/m_alat.php";

$model = new m_alat();

if (isset($_POST['tambah'])) {

    $nama_alat = $_POST['nama_alat'];
    $merk      = $_POST['merk'];
    $jumlah    = $_POST['jumlah'];
    $kondisi   = $_POST['kondisi'];
    $status    = $_POST['status'];

    $model->tambah(
        $nama_alat,
        $merk,
        $jumlah,
        $kondisi,
        $status
    );

    header("Location: ../View/v_alat.php");
    exit;
}


if (isset($_POST['update'])) {

    $id_alat   = $_POST['id_alat'];
    $nama_alat = $_POST['nama_alat'];
    $merk      = $_POST['merk'];
    $jumlah    = $_POST['jumlah'];
    $kondisi   = $_POST['kondisi'];
    $status    = $_POST['status'];

    $model->update(
        $id_alat,
        $nama_alat,
        $merk,
        $jumlah,
        $kondisi,
        $status
    );

    header("Location: ../View/v_alat.php");
    exit;
}



if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $model->hapus($id);

    header("Location: ../View/v_alat.php");
    exit;
}