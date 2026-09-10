<?php

session_start();

include_once __DIR__ . '/../Model/m_koneksi.php';
include_once __DIR__ . '/../Model/m_user.php';


if (
    !isset($_SESSION['data']) ||
    $_SESSION['data']['role'] !== 'admin'
) {
    header("Location: ../View/v_login.php");
    exit;
}


$koneksi = new koneksi();

$model = new m_user(
    $koneksi->koneksi
);



if (isset($_POST['tambah'])) {

    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'];
    $no_telp  = trim($_POST['no_telp']);


    if (
        $nama == '' ||
        $username == '' ||
        $password == '' ||
        $role == ''
    ) {

        echo "<script>
                alert('Data wajib diisi!');
                window.location='../View/v_user.php';
              </script>";

        exit;
    }


    $hasil = $model->tambahUser(
        $nama,
        $username,
        $password,
        $role,
        $no_telp
    );


    if ($hasil) {

        echo "<script>
                alert('User berhasil ditambahkan!');
                window.location='../View/v_user.php';
              </script>";

    } else {

        echo "<script>
                alert('Gagal menambahkan user!');
                window.location='../View/v_user.php';
              </script>";
    }

    exit;
}

if (isset($_POST['edit'])) {

    $id       = $_POST['id_user'];
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $no_telp  = trim($_POST['no_telp']);


    if (
        $id == '' ||
        $nama == '' ||
        $username == '' ||
        $role == ''
    ) {

        echo "<script>
                alert('Data wajib diisi!');
                window.location='../View/v_user.php';
              </script>";

        exit;
    }

    $hasil = $model->editUser(
        $id,
        $nama,
        $username,
        $role,
        $no_telp
    );

    if ($hasil) {

        echo "<script>
                alert('User berhasil diubah!');
                window.location='../View/v_user.php';
              </script>";

    } else {

        echo "<script>
                alert('Gagal mengubah user!');
                window.location='../View/v_user.php';
              </script>";
    }

    exit;
}

if (isset($_POST['edit_password'])) {

    $id       = $_POST['id_user'];
    $password = trim($_POST['password']);

    if ($id == '' || $password == '') {

        echo "<script>
                alert('Password wajib diisi!');
                window.location='../View/v_user.php';
              </script>";

        exit;
    }

    $hasil = $model->editPassword(
        $id,
        $password
    );

    if ($hasil) {

        echo "<script>
                alert('Password berhasil diubah!');
                window.location='../View/v_user.php';
              </script>";

    } else {

        echo "<script>
                alert('Gagal mengubah password!');
                window.location='../View/v_user.php';
              </script>";
    }

    exit;
}

if (isset($_GET['nonaktifkan'])) {

    $id = $_GET['nonaktifkan'];

    $hasil = $model->nonaktifkanUser($id);

    if ($hasil) {

        echo "<script>
                alert('User berhasil dinonaktifkan!');
                window.location='../View/v_user.php';
              </script>";

    } else {

        echo "<script>
                alert('Gagal menonaktifkan user!');
                window.location='../View/v_user.php';
              </script>";
    }

    exit;
}

if (isset($_GET['aktifkan'])) {

    $id = $_GET['aktifkan'];

    $hasil = $model->aktifkanUser($id);

    if ($hasil) {

        echo "<script>
                alert('User berhasil diaktifkan!');
                window.location='../View/v_user.php';
              </script>";

    } else {

        echo "<script>
                alert('Gagal mengaktifkan user!');
                window.location='../View/v_user.php';
              </script>";
    }

    exit;
}

?>