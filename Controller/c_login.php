
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Model/m_koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $koneksi = new koneksi();

    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    // Validasi input
    if ($user === '' || $pass === '') {
        echo "<script>
                alert('Isi username dan password');
                window.location='../View/v_login.php';
              </script>";
        exit;
    }

    // Amankan username
    $user = mysqli_real_escape_string(
        $koneksi->koneksi,
        $user
    );

    // Cari akun yang aktif
    $query = mysqli_query(
        $koneksi->koneksi,
        "SELECT * FROM tb_user
         WHERE username='$user'
         AND status_aktif=1
         LIMIT 1"
    );

    if ($query && mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        // Pengecekan password sesuai sistem yang sekarang
        if ($pass == $data['password']) {

            // Simpan data pengguna ke session
            session_regenerate_id(true);
            $_SESSION['data'] = $data;

            // Normalisasi role
            $role = strtolower(trim($data['role'] ?? ''));

            // Tentukan halaman berdasarkan role
            if ($role == 'admin') {

                $tujuan = '../View/v_homeadmin.php';

            } elseif ($role == 'petugas') {

                $tujuan = '../View/v_home_petugas.php';

            } elseif ($role == 'peminjam') {

                $tujuan = '../View/v_homepeminjam.php';

            } else {

                unset($_SESSION['data']);

                echo "<script>
                        alert('Role tidak dikenali');
                        window.location='../View/v_login.php';
                      </script>";
                exit;
            }

            // Redirect ke dashboard
            header("Location: " . $tujuan);
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
