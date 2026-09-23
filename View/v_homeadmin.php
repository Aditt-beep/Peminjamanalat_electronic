<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>pinjam.in — Dashboard Admin</title>

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
        --warn:#b5601f;
    }

    *{
        box-sizing:border-box;
    }

    body{
        margin:0;
        font-family:'Segoe UI', system-ui, -apple-system, sans-serif;
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

    .nav-item svg{
        width:16px;
        height:16px;
        flex-shrink:0;
        opacity:.85;
    }

    .nav-item:hover{
        background:rgba(255,255,255,0.08);
    }

    .nav-item.active{
        background:var(--teal);
        color:#fff;
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

    .logout-btn svg{
        width:16px;
        height:16px;
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
        color:var(--ink);
    }

    .topbar p{
        margin:0;
        font-size:13px;
        color:var(--muted);
    }

    .profile{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .avatar{
        width:38px;
        height:38px;
        border-radius:50%;
        background:var(--teal-deep);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:700;
        font-size:14px;
    }

    .profile-text{
        line-height:1.2;
    }

    .profile-text .pname{
        font-size:13px;
        font-weight:700;
        color:var(--ink);
    }

    .profile-text .prole{
        font-size:11px;
        color:var(--muted);
        text-transform:capitalize;
    }

    .stats{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:16px;
        margin-bottom:32px;
    }

    .stat-card{
        background:var(--cream);
        border:1px solid var(--card-border);
        border-radius:14px;
        padding:20px;
    }

    .stat-card .icon{
        width:34px;
        height:34px;
        border-radius:9px;
        display:flex;
        align-items:center;
        justify-content:center;
        margin-bottom:14px;
    }

    .stat-card .icon svg{
        width:17px;
        height:17px;
        color:#fff;
    }

    .stat-card .value{
        font-size:26px;
        font-weight:800;
        color:var(--ink);
        line-height:1;
        margin-bottom:4px;
    }

    .stat-card .label{
        font-size:12px;
        color:var(--muted);
        font-weight:600;
    }

    .icon.teal{
        background:var(--teal);
    }

    .icon.deep{
        background:var(--teal-deep);
    }

    .icon.warn{
        background:var(--warn);
    }

    .icon.dark{
        background:#3d4b48;
    }

    .section-title{
        font-size:15px;
        font-weight:800;
        color:var(--ink);
        margin:0 0 14px;
    }

    .menu-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:14px;
        margin-bottom:32px;
    }

    .menu-card{
        background:var(--cream);
        border:1px solid var(--card-border);
        border-radius:14px;
        padding:18px;
        display:flex;
        align-items:flex-start;
        gap:12px;
        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            transform .1s ease;
    }

    .menu-card:hover{
        border-color:var(--teal);
        box-shadow:0 10px 24px rgba(15,45,42,0.08);
        transform:translateY(-1px);
    }

    .menu-card .icon{
        width:36px;
        height:36px;
        border-radius:10px;
        background:var(--cream-deep);
        border:1px solid var(--card-border);
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
    }

    .menu-card .icon svg{
        width:17px;
        height:17px;
        color:var(--teal-deep);
    }

    .menu-card .title{
        font-size:13.5px;
        font-weight:700;
        color:var(--ink);
        margin-bottom:3px;
    }

    .menu-card .desc{
        font-size:11.5px;
        color:var(--muted);
        line-height:1.4;
    }

    .panel{
        background:var(--cream);
        border:1px solid var(--card-border);
        border-radius:14px;
        padding:22px;
    }

    .panel-title{
        font-size:14px;
        font-weight:800;
        margin:0 0 8px;
    }

    .panel-text{
        font-size:12.5px;
        color:var(--muted);
        margin:0;
        line-height:1.6;
    }

    @media (max-width:1000px){

        .stats{
            grid-template-columns:repeat(2,1fr);
        }

        .menu-grid{
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media (max-width:700px){

        .layout{
            flex-direction:column;
        }

        .sidebar{
            width:100%;
            flex-direction:row;
            overflow-x:auto;
            align-items:center;
        }

        .brand{
            margin-bottom:0;
            margin-right:14px;
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

        .stats{
            grid-template-columns:1fr 1fr;
        }

        .menu-grid{
            grid-template-columns:1fr;
        }

        .main{
            padding:20px;
        }

        .topbar{
            align-items:flex-start;
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

        <a class="nav-item active" href="v_homeadmin.php">

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

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <circle cx="12" cy="8" r="4"/>
                <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>

            </svg>

            Data User

        </a>

        <a class="nav-item" href="v_alat.php">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>

            </svg>

            Data Alat

        </a>

        <a class="nav-item" href="v_kategori.php">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M20.59 13.41 11 3.83 3.83 11l9.58 9.59a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.83Z"/>

                <circle cx="7" cy="7" r="1"/>

            </svg>

            Kategori Alat

        </a>

        <a class="nav-item" href="v_peminjaman.php">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>

                <rect
                    x="9"
                    y="3"
                    width="6"
                    height="4"
                    rx="1"
                />

            </svg>

            Data Peminjaman

        </a>

        <a class="nav-item" href="v_pengembalian.php">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M3 7v6h6"/>
                <path d="M3 13a9 9 0 1 0 3-6.7L3 9"/>

            </svg>

            Data Pengembalian

        </a>

        <div class="nav-group-label">
            Lainnya
        </div>


        <a class="nav-item" href="v_log.php">

            <svg viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M12 8v4l3 3"/>
                <circle cx="12" cy="12" r="9"/>

            </svg>

            Log Aktivitas

        </a>

        <div class="sidebar-footer">

            <a class="logout-btn" href="../Controller/c_logout.php">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2">

                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M16 17l5-5-5-5"/>
                    <path d="M21 12H9"/>

                </svg>

                Keluar

            </a>

        </div>

    </aside>

    <main class="main">


        <div class="topbar">

            <div>

                <h1>
                    Selamat datang, Admin
                </h1>

                <p>
                    Ringkasan aktivitas peminjaman alat hari ini.
                </p>

            </div>


            <div class="profile">

                <div class="profile-text"
                     style="text-align:right;">

                    <div class="pname">
                        Admin
                    </div>

                    <div class="prole">
                        admin
                    </div>

                </div>


                <div class="avatar">
                    A
                </div>

            </div>

        </div>

        <div class="stats">



            <div class="stat-card">

                <div class="icon teal">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>

                    </svg>

                </div>

                <div class="value">
                    0
                </div>

                <div class="label">
                    Total Alat
                </div>

            </div>



            <div class="stat-card">

                <div class="icon warn">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>

                        <rect
                            x="9"
                            y="3"
                            width="6"
                            height="4"
                            rx="1"
                        />

                    </svg>

                </div>

                <div class="value">
                    0
                </div>

                <div class="label">
                    Sedang Dipinjam
                </div>

            </div>

            <div class="stat-card">

                <div class="icon deep">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <circle cx="12" cy="8" r="4"/>

                        <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>

                    </svg>

                </div>

                <div class="value">
                    0
                </div>

                <div class="label">
                    Peminjam Terdaftar
                </div>

            </div>

            <div class="stat-card">

                <div class="icon dark">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <circle cx="12" cy="12" r="9"/>

                        <path d="M12 7v5l3 3"/>

                    </svg>

                </div>

                <div class="value">
                    0
                </div>

                <div class="label">
                    Menunggu Persetujuan
                </div>

            </div>

        </div>

        <div class="section-title">
            Kelola Data
        </div>


        <div class="menu-grid">

            <a class="menu-card" href="v_user.php">

                <div class="icon">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <circle cx="12" cy="8" r="4"/>

                        <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>

                    </svg>

                </div>

                <div>

                    <div class="title">
                        Data User
                    </div>

                    <div class="desc">
                        Tambah, ubah, dan nonaktifkan akun admin, petugas, dan peminjam.
                    </div>

                </div>

            </a>

            <a class="menu-card" href="v_alat.php">

                <div class="icon">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>

                    </svg>

                </div>

                <div>

                    <div class="title">
                        Data Alat
                    </div>

                    <div class="desc">
                        Kelola daftar alat, kondisi, jumlah, dan status ketersediaan.
                    </div>

                </div>

            </a>

            <a class="menu-card" href="v_kategori.php">

                <div class="icon">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M20.59 13.41 11 3.83 3.83 11l9.58 9.59a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.83Z"/>

                        <circle cx="7" cy="7" r="1"/>

                    </svg>

                </div>

                <div>

                    <div class="title">
                        Kategori Alat
                    </div>

                    <div class="desc">
                        Atur pengelompokan kategori supaya alat mudah ditemukan.
                    </div>

                </div>

            </a>

            <a class="menu-card" href="v_peminjaman.php">

                <div class="icon">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>

                        <rect
                            x="9"
                            y="3"
                            width="6"
                            height="4"
                            rx="1"
                        />

                    </svg>

                </div>

                <div>

                    <div class="title">
                        Data Peminjaman
                    </div>

                    <div class="desc">
                        Lihat dan kelola seluruh transaksi peminjaman alat.
                    </div>

                </div>

            </a>

            <a class="menu-card" href="v_pengembalian.php">

                <div class="icon">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M3 7v6h6"/>

                        <path d="M3 13a9 9 0 1 0 3-6.7L3 9"/>

                    </svg>

                </div>

                <div>

                    <div class="title">
                        Data Pengembalian
                    </div>

                    <div class="desc">
                        Pantau pengembalian alat beserta denda keterlambatan.
                    </div>

                </div>

            </a>

            <a class="menu-card" href="v_log.php">

                <div class="icon">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M12 8v4l3 3"/>

                        <circle cx="12" cy="12" r="9"/>

                    </svg>

                </div>

                <div>

                    <div class="title">
                        Log Aktivitas
                    </div>

                    <div class="desc">
                        Riwayat aktivitas semua pengguna di dalam sistem.
                    </div>

                </div>

            </a>

        </div>

        <div class="section-title">
            Informasi Sistem
        </div>

        <div class="panel">

            <h3 class="panel-title">
                Dashboard Admin
            </h3>

            <p class="panel-text">
                Gunakan menu di sebelah kiri untuk mengelola
                pengguna, kategori alat, data alat, peminjaman,
                pengembalian, dan log aktivitas.
            </p>

        </div>

    </main>

</div>

</body>
</html>