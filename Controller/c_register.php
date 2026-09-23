<?php

include_once __DIR__ . '/../Model/m_koneksi.php';
include_once __DIR__ . '/../Model/m_user.php';

$koneksi = new koneksi();
$model = new m_user($koneksi->koneksi);

if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nama = trim($_POST['nama']);

    if ($username == "" || $password == "" || $nama == "") {
        echo "<script>
                alert('Semua data harus diisi');
                window.location='../View/v_register.php;
              </script>";
        exit;
    }

    $hasil = $model->registerUser($nama, $username, $password);

    if ($hasil) {
        echo "<script>
                alert('Register berhasil');
                window.location='../View/v_login.php';
              </script>";
    } else {
        echo "<script>
                alert('Register gagal');
                window.location='../View/v_register.php;
              </script>";
    }
}