<?php

session_start();

if (
    !isset($_SESSION['data']) ||
    $_SESSION['data']['role'] !== 'admin'
) {
    header("Location: v_login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>pinjam.in — Tambah Peminjaman</title>

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
        }

        .nav-item:hover{
            background:rgba(255,255,255,0.08);
        }

        .nav-item.active{
            background:var(--teal);
            color:#fff;
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

        /* MAIN */

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
            padding:25px;
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
        .form-group select{
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

        .form-group input:focus,
        .form-group select:focus{
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

            .form-row{
                grid-template-columns:1fr;
            }

        }

    </style>

</head>

<body>

<div class="layout">

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
            Dashboard
        </a>


        <div class="nav-group-label">
            Kelola Data
        </div>


        <a class="nav-item" href="v_user.php">
            Data User
        </a>


        <a class="nav-item" href="v_alat.php">
            Data Alat
        </a>


        <a class="nav-item" href="v_kategori.php">
            Kategori
        </a>


        <a class="nav-item active" href="v_peminjaman.php">
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
                    Tambah Peminjaman
                </h1>

                <p>
                    Tambahkan data peminjaman alat.
                </p>

            </div>

        </div>


        <div class="panel">

            <h2 class="panel-title">
                Form Tambah Peminjaman
            </h2>


            <form
                action="../Controller/c_peminjaman.php"
                method="POST"
            >

                <div class="form-row">

                    <div class="form-group">

                        <label for="tanggal_pengajuan">
                            Tanggal Pengajuan
                        </label>

                        <input
                            type="date"
                            id="tanggal_pengajuan"
                            name="tanggal_pengajuan"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="tanggal_peminjaman">
                            Tanggal Peminjaman
                        </label>

                        <input
                            type="date"
                            id="tanggal_peminjaman"
                            name="tanggal_peminjaman"
                            required
                        >

                    </div>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="tanggal_kembali_rencana">
                            Rencana Tanggal Kembali
                        </label>

                        <input
                            type="date"
                            id="tanggal_kembali_rencana"
                            name="tanggal_kembali_rencana"
                            required
                        >

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

                            <option value="menunggu">
                                Menunggu
                            </option>

                            <option value="disetujui">
                                Disetujui
                            </option>

                            <option value="ditolak">
                                Ditolak
                            </option>

                            <option value="selesai">
                                Selesai
                            </option>

                        </select>

                    </div>

                </div>


                <div class="form-action">

                    <button
                        type="submit"
                        name="tambah"
                        class="btn-save"
                    >
                        Simpan Peminjaman
                    </button>


                    <a
                        href="v_peminjaman.php"
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