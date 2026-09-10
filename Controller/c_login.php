<?php

session_start();

include_once __DIR__ . '/../Model/m_koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $koneksi = new koneksi();

    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if ($user == "" || $pass == "") {
        echo "<script>
                alert('Isi username dan password');
                window.location='../View/v_login.php';
              </script>";
        exit;
    }

    $user = mysqli_real_escape_string($koneksi->koneksi, $user);

    $query = mysqli_query(
        $koneksi->koneksi,
        "SELECT * FROM tb_user
         WHERE username='$user'
         AND status_aktif=1
         LIMIT 1"
    );

    if ($query && mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        if ($pass == $data['password']) {

            $_SESSION['data'] = $data;

            if ($data['role'] == 'admin') {

                header("Location: ../View/v_homeadmin.php");

            } elseif ($data['role'] == 'petugas') {

                header("Location: ../View/v_homepetugas.php");

            } elseif ($data['role'] == 'peminjam') {

                header("Location: ../View/v_homepeminjam.php");

            } else {

                echo "<script>
                        alert('Role tidak dikenali');
                        window.location='../View/v_login.php';
                      </script>";
            }

            exit;

        } else {

            echo "<script>
                    alert('Password salah');
                    window.location='../View/v_login.php';
                  </script>";
            exit;
        }

    } else {

        echo "<script>
                alert('Username tidak ditemukan atau akun nonaktif');
                window.location='../View/v_login.php';
              </script>";
        exit;
    }
}
?>