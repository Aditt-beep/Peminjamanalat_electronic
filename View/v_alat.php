<?php
include_once __DIR__ . '/../Model/m_koneksi.php';
include_once __DIR__ . '/../Model/m_alat.php';

$koneksi = new koneksi();

$model = new m_alat(
    $koneksi->koneksi
);

$dataAlat = $model->tampil_data();

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>pinjam.in — Data Alat</title>

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

.nav-item svg{
    width:16px;
    height:16px;
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

.btn-add{
    display:flex;
    align-items:center;
    gap:7px;
    border:none;
    border-radius:8px;
    padding:10px 15px;
    background:var(--teal-dark);
    color:#fff;
    font-size:13px;
    font-weight:700;
}

.btn-add:hover{
    background:var(--teal-deep);
}

.panel{
    background:var(--cream);
    border:1px solid var(--card-border);
    border-radius:14px;
    padding:22px;
}

.panel-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:18px;
}

.panel-title{
    margin:0;
    font-size:15px;
    font-weight:800;
}

.search-box{
    display:flex;
    align-items:center;
    border:1px solid var(--card-border);
    background:#fff;
    border-radius:8px;
    padding:9px 12px;
    width:250px;
}

.search-box input{
    border:none;
    outline:none;
    width:100%;
    font-size:13px;
}

.table-wrapper{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead th{
    text-align:left;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:var(--muted);
    font-weight:700;
    padding:0 10px 10px;
    border-bottom:1px solid var(--card-border);
    white-space:nowrap;
}

tbody td{
    padding:13px 10px;
    font-size:13px;
    border-bottom:1px solid var(--card-border);
}

tbody tr:last-child td{
    border-bottom:none;
}

.empty-row td{
    text-align:center;
    color:var(--muted);
    padding:30px 10px;
}

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
}

.badge-baik{
    background:#d9efe9;
    color:#0f6b64;
}

.badge-ringan{
    background:#fdecd2;
    color:#8a5a10;
}

.badge-berat{
    background:#fbdede;
    color:#a53939;
}

.badge-tersedia{
    background:#d9efe9;
    color:#0f6b64;
}

.badge-dipinjam{
    background:#fdecd2;
    color:#8a5a10;
}

.badge-nonaktif{
    background:#e4e0e6;
    color:#5b5363;
}

.action{
    display:flex;
    gap:6px;
}

.btn-edit,
.btn-delete{
    border:none;
    border-radius:7px;
    padding:7px 10px;
    font-size:11px;
    font-weight:700;
    cursor:pointer;
}

.btn-edit{
    background:#e4efed;
    color:var(--teal-deep);
}

.btn-delete{
    background:#fbe3e3;
    color:#a53939;
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

                <rect x="3" y="3" width="7" height="7" rx="1.5" fill="#fff"/>

                <rect x="14" y="3" width="7" height="7" rx="1.5" fill="#fff" opacity="0.7"/>

                <rect x="3" y="14" width="7" height="7" rx="1.5" fill="#fff" opacity="0.7"/>

                <rect x="14" y="14" width="7" height="7" rx="1.5" fill="#fff"/>

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

        Dashboard

    </a>


    <div class="nav-group-label">
        Kelola Data
    </div>


    <a class="nav-item"
       href="v_user.php">

        Data User

    </a>


    <a class="nav-item active"
       href="v_alat.php">

        Data Alat

    </a>


    <a class="nav-item"
       href="v_kategori.php">

        Kategori

    </a>


    <a class="nav-item"
       href="v_peminjaman.php">

        Data Peminjaman

    </a>


    <a class="nav-item"
       href="v_pengembalian.php">

        Data Pengembalian

    </a>


    <div class="nav-group-label">
        Lainnya
    </div>


    <a class="nav-item"
       href="v_log.php">

        Log Aktivitas

    </a>


    <div class="sidebar-footer">

        <a class="logout-btn"
           href="v_logout.php">

            Keluar

        </a>

    </div>

</aside>


<!-- MAIN -->

<main class="main">

    <div class="topbar">

        <div>

            <h1>
                Data Alat
            </h1>

            <p>
                Kelola data alat yang tersedia untuk dipinjam.
            </p>

        </div>


        <a
            href="v_tambah_alat.php"
            class="btn-add">

            + Tambah Alat

        </a>

    </div>


    <div class="panel">

        <div class="panel-header">

            <h2 class="panel-title">
                Daftar Alat
            </h2>


            <div class="search-box">

                <input
                    type="text"
                    id="search"
                    placeholder="Cari alat..."
                >

            </div>

        </div>


        <div class="table-wrapper">

            <table id="tabelAlat">

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Alat</th>
                        <th>Merk</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                /*
                 * $dataAlat berasal dari:
                 * $model->tampil_data()
                 *
                 * Karena tampil_data() mengembalikan ARRAY,
                 * maka gunakan foreach, bukan mysqli_num_rows()
                 * dan mysqli_fetch_assoc().
                 */

                if (!empty($dataAlat)):

                    foreach ($dataAlat as $alat):

                ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $alat['kode_alat'] ?? '-'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $alat['nama_alat'] ?? '-'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $alat['merk'] ?? '-'
                            ) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $alat['jumlah'] ?? '-'
                            ) ?>
                        </td>


                        <td>

                            <?php if (
                                ($alat['kondisi'] ?? '') == 'baik'
                            ): ?>

                                <span class="badge badge-baik">
                                    Baik
                                </span>

                            <?php elseif (
                                ($alat['kondisi'] ?? '') == 'rusak_ringan'
                            ): ?>

                                <span class="badge badge-ringan">
                                    Rusak Ringan
                                </span>

                            <?php else: ?>

                                <span class="badge badge-berat">
                                    Rusak Berat
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                ($alat['status'] ?? '') == 'tersedia'
                            ): ?>

                                <span class="badge badge-tersedia">
                                    Tersedia
                                </span>

                            <?php elseif (
                                ($alat['status'] ?? '') == 'dipinjam'
                            ): ?>

                                <span class="badge badge-dipinjam">
                                    Dipinjam
                                </span>

                            <?php else: ?>

                                <span class="badge badge-nonaktif">
                                    Tidak Aktif
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $alat['deskripsi'] ?? '-'
                            ) ?>

                        </td>


                        <td>

                            <div class="action">

                                <a
                                    href="v_edit_alat.php?id=<?= $alat['id_alat'] ?>"
                                    class="btn-edit">

                                    Edit

                                </a>


                                <a
                                    href="../Controller/c_alat.php?aksi=hapus&id=<?= $alat['id_alat'] ?>"
                                    class="btn-delete"
                                    onclick="return confirm('Yakin ingin menghapus alat ini?')">

                                    Hapus

                                </a>

                            </div>

                        </td>

                    </tr>

                <?php

                    endforeach;

                else:

                ?>

                    <tr class="empty-row">

                        <td colspan="9">

                            Belum ada data alat.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

</div>


<script>

document.getElementById('search').addEventListener('keyup', function(){

    let keyword = this.value.toLowerCase();

    let rows = document.querySelectorAll(
        '#tabelAlat tbody tr'
    );

    rows.forEach(function(row){

        let text = row.innerText.toLowerCase();

        if(text.includes(keyword)){

            row.style.display = '';

        }else{

            row.style.display = 'none';

        }

    });

});

</script>

</body>

</html>