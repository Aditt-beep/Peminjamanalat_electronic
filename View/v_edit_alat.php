<?php

session_start();

include_once __DIR__ . '/../Model/m_koneksi.php';
include_once __DIR__ . '/../Model/m_alat.php';

if (
    !isset($_SESSION['data']) ||
    $_SESSION['data']['role'] !== 'admin'
) {
    header("Location: v_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: v_alat.php");
    exit;
}

$koneksi = new koneksi();

$model = new m_alat(
    $koneksi->koneksi
);

$id = (int)$_GET['id'];

$alat = $model->get_by_id($id);

if (!$alat) {
    header("Location: v_alat.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>pinjam.in — Edit Alat</title>

    <style>

        :root{
            --cream:#f4f1ea;
            --cream-deep:#efebe1;
            --teal-dark:#0f6b64;
            --teal:#1a7a72;
            --teal-deep:#0a4a45;
            --ink:#1c2b29;
            --muted:#7d8a87;
            --card-border:#e4e0d6;
        }

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
            background:var(--cream-deep);
            color:var(--ink);
        }

        a{
            color:inherit;
            text-decoration:none;
        }

        .layout{
            display:flex;
            min-height:100vh;
        }


        .sidebar{
            width:230px;
            background:var(--teal-deep);
            color:#fff;
            display:flex;
            flex-direction:column;
            padding:22px 16px;
            flex-shrink:0;
        }

        .brand{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:32px;
            padding:0 6px;
        }

        .brand .logo{
            width:34px;
            height:34px;
            border-radius:8px;
            background:var(--teal);
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }

        .brand .logo svg{
            width:18px;
            height:18px;
        }

        .brand-text{
            line-height:1.1;
        }

        .brand-text .name{
            font-weight:800;
            font-size:16px;
            color:#fff;
        }

        .brand-text .tag{
            font-size:9px;
            letter-spacing:1px;
            color:#8fc9c2;
            font-weight:700;
        }

        .nav-group-label{
            font-size:10.5px;
            letter-spacing:.8px;
            color:#7fa7a1;
            font-weight:700;
            margin:18px 10px 8px;
            text-transform:uppercase;
        }

        .nav-item{
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 12px;
            border-radius:8px;
            font-size:13.5px;
            font-weight:600;
            color:#dce9e7;
            margin-bottom:2px;
            transition:background .15s ease;
        }

        .nav-item:hover{
            background:rgba(255,255,255,0.08);
        }

        .nav-item.active{
            background:var(--teal);
            color:#fff;
        }

        .nav-item svg{
            width:16px;
            height:16px;
            flex-shrink:0;
            opacity:.85;
        }

        .nav-item.active svg{
            opacity:1;
        }

        .sidebar-footer{
            margin-top:auto;
            padding-top:16px;
            border-top:1px solid rgba(255,255,255,0.12);
        }

        .logout-btn{
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 12px;
            border-radius:8px;
            font-size:13.5px;
            font-weight:700;
            color:#f3c9c9;
        }

        .logout-btn:hover{
            background:rgba(255,255,255,0.08);
        }


        .main{
            flex:1;
            padding:32px 40px;
            max-width:1200px;
        }

        .topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:28px;
        }

        .topbar h1{
            font-size:23px;
            font-weight:800;
            margin:0 0 4px;
        }

        .topbar p{
            margin:0;
            font-size:13px;
            color:var(--muted);
        }

        .panel{
            background:var(--cream);
            border:1px solid var(--card-border);
            border-radius:14px;
            padding:22px;
            max-width:700px;
        }

        .panel-title{
            margin:0 0 22px;
            font-size:15px;
            font-weight:800;
        }

        .form-group{
            margin-bottom:18px;
        }

        .form-group label{
            display:block;
            font-size:12px;
            font-weight:700;
            margin-bottom:7px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea{
            width:100%;
            padding:11px 12px;
            border:1px solid var(--card-border);
            border-radius:8px;
            background:#fff;
            color:var(--ink);
            font-family:inherit;
            font-size:13px;
            outline:none;
        }

        .form-group textarea{
            resize:vertical;
            min-height:100px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus{
            border-color:var(--teal);
        }

        .form-row{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
        }

        .form-action{
            display:flex;
            gap:8px;
            margin-top:25px;
        }

        .btn-save,
        .btn-cancel{
            border:none;
            border-radius:8px;
            padding:10px 16px;
            font-size:13px;
            font-weight:700;
            cursor:pointer;
            text-decoration:none;
        }

        .btn-save{
            background:var(--teal-dark);
            color:#fff;
        }

        .btn-save:hover{
            background:var(--teal-deep);
        }

        .btn-cancel{
            background:#e4e0d6;
            color:var(--ink);
        }

        .btn-cancel:hover{
            background:#d8d3c7;
        }

        @media(max-width:700px){

            .layout{
                flex-direction:column;
            }

            .sidebar{
                width:100%;
                flex-direction:row;
                overflow-x:auto;
                align-items:center;
            }

            .nav-group-label{
                display:none;
            }

            .sidebar-footer{
                margin-top:0;
                margin-left:auto;
                border-top:none;
                padding-top:0;
            }

            .main{
                padding:20px;
            }

            .topbar{
                flex-direction:column;
                align-items:flex-start;
                gap:15px;
            }

            .panel{
                max-width:100%;
            }

            .form-row{
                grid-template-columns:1fr;
            }

        }

    </style>

</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">

            <div class="logo">

                <svg viewBox="0 0 24 24" fill="none">

                    <rect
                        x="3"
                        y="3"
                        width="7"
                        height="7"
                        rx="1.5"
                        fill="#fff"
                    />

                    <rect
                        x="14"
                        y="3"
                        width="7"
                        height="7"
                        rx="1.5"
                        fill="#fff"
                        opacity="0.7"
                    />

                    <rect
                        x="3"
                        y="14"
                        width="7"
                        height="7"
                        rx="1.5"
                        fill="#fff"
                        opacity="0.7"
                    />

                    <rect
                        x="14"
                        y="14"
                        width="7"
                        height="7"
                        rx="1.5"
                        fill="#fff"
                    />

                </svg>

            </div>

            <div class="brand-text">

                <div class="name">
                    pinjam.in
                </div>

                <div class="tag">
                    ADMIN PANEL
                </div>

            </div>

        </div>


        <div class="nav-group-label">
            Menu Utama
        </div>


        <a class="nav-item" href="v_homeadmin.php">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <rect x="3" y="3" width="7" height="9" rx="1"/>
                <rect x="14" y="3" width="7" height="5" rx="1"/>
                <rect x="14" y="12" width="7" height="9" rx="1"/>
                <rect x="3" y="16" width="7" height="5" rx="1"/>

            </svg>

            Dashboard

        </a>


        <div class="nav-group-label">
            Kelola Data
        </div>


        <a class="nav-item" href="v_user.php">

            Data User

        </a>


        <a class="nav-item active" href="v_alat.php">

            Data Alat

        </a>


        <a class="nav-item" href="v_kategori.php">

            Kategori

        </a>


        <a class="nav-item" href="v_peminjaman.php">

            Data Peminjaman

        </a>


        <a class="nav-item" href="v_pengembalian.php">

            Data Pengembalian

        </a>


        <div class="nav-group-label">
            Lainnya
        </div>


        <a class="nav-item" href="v_log.php">

            Log Aktivitas

        </a>


        <div class="sidebar-footer">

            <a class="logout-btn" href="v_logout.php">

                Keluar

            </a>

        </div>

    </aside>



    <main class="main">

        <div class="topbar">

            <div>

                <h1>
                    Edit Alat
                </h1>

                <p>
                    Ubah data alat yang tersedia untuk dipinjam.
                </p>

            </div>

        </div>


        <div class="panel">

            <h2 class="panel-title">
                Form Edit Alat
            </h2>


            <form
                action="../Controller/c_alat.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="id_alat"
                    value="<?= $alat['id_alat'] ?>"
                >


                <div class="form-row">

                    <div class="form-group">

                        <label for="kode_alat">
                            Kode Alat
                        </label>

                        <input
                            type="text"
                            id="kode_alat"
                            name="kode_alat"
                            value="<?= htmlspecialchars($alat['kode_alat']) ?>"
                            placeholder="Masukkan kode alat"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="nama_alat">
                            Nama Alat
                        </label>

                        <input
                            type="text"
                            id="nama_alat"
                            name="nama_alat"
                            value="<?= htmlspecialchars($alat['nama_alat']) ?>"
                            placeholder="Masukkan nama alat"
                            required
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="merk">
                            Merk
                        </label>

                        <input
                            type="text"
                            id="merk"
                            name="merk"
                            value="<?= htmlspecialchars($alat['merk'] ?? '') ?>"
                            placeholder="Masukkan merk alat"
                        >

                    </div>


                    <div class="form-group">

                        <label for="jumlah">
                            Jumlah
                        </label>

                        <input
                            type="number"
                            id="jumlah"
                            name="jumlah"
                            value="<?= htmlspecialchars($alat['jumlah']) ?>"
                            placeholder="Masukkan jumlah"
                            min="1"
                            required
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="kondisi">
                            Kondisi
                        </label>

                        <select
                            id="kondisi"
                            name="kondisi"
                            required
                        >

                            <option value="">
                                -- Pilih Kondisi --
                            </option>

                            <option
                                value="baik"
                                <?= ($alat['kondisi'] == 'baik') ? 'selected' : '' ?>
                            >
                                Baik
                            </option>

                            <option
                                value="rusak_ringan"
                                <?= ($alat['kondisi'] == 'rusak_ringan') ? 'selected' : '' ?>
                            >
                                Rusak Ringan
                            </option>

                            <option
                                value="rusak_berat"
                                <?= ($alat['kondisi'] == 'rusak_berat') ? 'selected' : '' ?>
                            >
                                Rusak Berat
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            required
                        >

                            <option value="">
                                -- Pilih Status --
                            </option>

                            <option
                                value="tersedia"
                                <?= ($alat['status'] == 'tersedia') ? 'selected' : '' ?>
                            >
                                Tersedia
                            </option>

                            <option
                                value="dipinjam"
                                <?= ($alat['status'] == 'dipinjam') ? 'selected' : '' ?>
                            >
                                Dipinjam
                            </option>

                            <option
                                value="nonaktif"
                                <?= ($alat['status'] == 'nonaktif') ? 'selected' : '' ?>
                            >
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>


                <div class="form-group">

                    <label for="deskripsi">
                        Deskripsi
                    </label>

                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        placeholder="Masukkan deskripsi alat"
                    ><?= htmlspecialchars($alat['deskripsi'] ?? '') ?></textarea>

                </div>


                <div class="form-action">

                    <button
                        type="submit"
                        name="edit"
                        class="btn-save"
                    >
                        Simpan Perubahan
                    </button>


                    <a
                        href="v_alat.php"
                        class="btn-cancel"
                    >
                        Kembali
                    </a>

                </div>

            </form>

        </div>

    </main>

</div>

</body>

</html>