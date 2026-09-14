<?php

session_start();

include_once __DIR__ . '/../Model/m_koneksi.php';
include_once __DIR__ . '/../Model/m_user.php';

if (
    !isset($_SESSION['data']) ||
    $_SESSION['data']['role'] !== 'admin'
) {
    header("Location: v_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: v_user.php");
    exit;
}

$koneksi = new koneksi();

$model = new m_user(
    $koneksi->koneksi
);

$id = (int)$_GET['id'];

$user = $model->getUserById($id);

if (!$user) {
    header("Location: v_user.php");
    exit;
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>pinjam.in — Edit User</title>

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

}

</style>

</head>

<body>

<div class="layout">

<aside class="sidebar">

```
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

<a class="nav-item"
   href="v_homeadmin.php">

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

<a class="nav-item active"
   href="v_user.php">

    <svg viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2">

        <circle cx="12" cy="8" r="4"/>
        <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>

    </svg>

    Data User

</a>

<a class="nav-item"
   href="v_alat.php">

    <svg viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2">

        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>

    </svg>

    Data Alat

</a>

<a class="nav-item"
   href="v_kategori.php">

    <svg viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2">

        <path d="M20.59 13.41 11 3.83 3.83 11l9.58 9.59a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.83-2.83Z"/>
        <circle cx="7" cy="7" r="1"/>

    </svg>

    Kategori

</a>

<a class="nav-item"
   href="v_peminjaman.php">

    <svg viewBox="0 0 24 24"
         fill="none"
         stroke="currentColor"
         stroke-width="2">

        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2-2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
        <rect x="9" y="3" width="6" height="4" rx="1"/>

    </svg>

    Data Peminjaman

</a>

<a class="nav-item"
   href="v_pengembalian.php">

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

<a class="nav-item"
   href="v_log.php">

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

    <a class="logout-btn"
       href="v_logout.php">

        <svg viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">

            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1-2-2h4"/>
            <path d="M16 17l5-5-5-5"/>
            <path d="M21 12H9"/>

        </svg>

        Keluar

    </a>

</div>
```

</aside>

<main class="main">

```
<div class="topbar">

    <div>

        <h1>
            Edit User
        </h1>

        <p>
            Ubah data akun admin, petugas, atau peminjam.
        </p>

    </div>

</div>

<div class="panel">

    <h2 class="panel-title">
        Form Edit User
    </h2>

    <form
        action="../Controller/c_user.php"
        method="POST"
    >

        <input
            type="hidden"
            name="id_user"
            value="<?= $user['id_user'] ?>"
        >

        <div class="form-group">

            <label for="nama">
                Nama
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                value="<?= htmlspecialchars($user['nama']) ?>"
                placeholder="Masukkan nama"
                required
            >

        </div>

        <div class="form-group">

            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars($user['username']) ?>"
                placeholder="Masukkan username"
                required
            >

        </div>

        <div class="form-group">

            <label for="role">
                Role
            </label>

            <select
                id="role"
                name="role"
                required
            >

                <option value="">
                    -- Pilih Role --
                </option>

                <option
                    value="admin"
                    <?= $user['role'] == 'admin' ? 'selected' : '' ?>
                >
                    Admin
                </option>

                <option
                    value="petugas"
                    <?= $user['role'] == 'petugas' ? 'selected' : '' ?>
                >
                    Petugas
                </option>

                <option
                    value="peminjam"
                    <?= $user['role'] == 'peminjam' ? 'selected' : '' ?>
                >
                    Peminjam
                </option>

            </select>

        </div>

        <div class="form-group">

            <label for="no_telp">
                No. Telepon
            </label>

            <input
                type="text"
                id="no_telp"
                name="no_telp"
                value="<?= htmlspecialchars($user['no_telp'] ?? '') ?>"
                placeholder="Masukkan nomor telepon"
            >

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
                href="v_user.php"
                class="btn-cancel"
            >
                Kembali
            </a>

        </div>

    </form>

</div>
```

</main>

</div>

</body>

</html>
