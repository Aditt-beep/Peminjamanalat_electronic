<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION["data"]["id_user"]) ||
    strtolower(trim($_SESSION["data"]["role"] ?? "")) !== "petugas"
) {
    header("Location: v_login.php");
    exit;
}

require_once __DIR__ . "/../Model/m_peminjaman.php";

$user = $_SESSION["data"];
$namaUser = $user["nama"] ?? $user["username"] ?? "Petugas";

$model = new m_peminjaman();
$data_pengajuan = $model->daftar_pengajuan();

if (!is_array($data_pengajuan)) {
    $data_pengajuan = [];
}

$total = count($data_pengajuan);
$menunggu = 0;
$disetujui = 0;
$ditolak = 0;
$selesai = 0;

foreach ($data_pengajuan as $p) {
    switch ($p["status"] ?? "") {
        case "menunggu":
            $menunggu++;
            break;
        case "disetujui":
            $disetujui++;
            break;
        case "ditolak":
            $ditolak++;
            break;
        case "selesai":
            $selesai++;
            break;
    }
}

$pct = function ($jumlah) use ($total) {
    return $total > 0 ? round($jumlah / $total * 100, 2) : 0;
};

$pesan = $_SESSION["pesan_pengajuan"] ?? "";
unset($_SESSION["pesan_pengajuan"]);

function e_petugas($nilai) {
    return htmlspecialchars(
        (string)($nilai ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$linkPengajuan = "../Controller/c_pengajuan.php?aksi=petugas";
$linkPengembalian = "../Controller/c_pengajuan_pengembalian.php?aksi=petugas";
$linkLaporan = "../Controller/c_laporan.php";
$linkLogout = "../Controller/c_logout.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pinjam.in - Dashboard Petugas</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --cream: #f4f1ea;
    --white: #ffffff;
    --teal: #0f6b64;
    --teal-dark: #0a514c;
    --text: #253b37;
    --muted: #73807b;
    --border: #e3e7e2;
    --teal-soft: #e9f2ef;
    --amber: #d99a1c;
    --amber-soft: #fff0c2;
    --green: #2f8f57;
    --green-soft: #d9f1df;
    --red: #c9534f;
    --red-soft: #f8dddd;
    --blue: #4f65b3;
    --blue-soft: #e3e8f8;
    --radius: 16px;
    --shadow: 0 1px 2px rgba(37,59,55,.05),
              0 8px 24px -12px rgba(37,59,55,.12);
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Plus Jakarta Sans", "Segoe UI", Arial, sans-serif;
    background: var(--cream);
    color: var(--text);
    line-height: 1.5;
}

a {
    color: inherit;
}

button, a {
    -webkit-tap-highlight-color: transparent;
}

:focus-visible {
    outline: 3px solid var(--teal);
    outline-offset: 3px;
}

/* HEADER */

header {
    position: sticky;
    top: 0;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 14px 5%;
    background: rgba(255,255,255,.96);
    border-bottom: 1px solid var(--border);
}

.brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: var(--teal);
    font-size: 22px;
    font-weight: 800;
    text-decoration: none;
}

.brand-mark {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    background: var(--teal);
    color: white;
    border-radius: 10px;
    font-size: 19px;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.nav-link {
    padding: 9px 13px;
    border-radius: 999px;
    color: var(--text);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.nav-link:hover {
    background: var(--teal-soft);
    color: var(--teal-dark);
}

.nav-logout {
    color: var(--red);
}

.nav-logout:hover {
    background: var(--red-soft);
    color: var(--red);
}

.user-chip {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    margin-right: 8px;
    padding: 5px 13px 5px 5px;
    background: var(--teal-soft);
    border-radius: 999px;
    color: var(--teal-dark);
    font-size: 13px;
    font-weight: 700;
}

.avatar {
    display: grid;
    place-items: center;
    flex: none;
    width: 32px;
    height: 32px;
    background: var(--teal);
    color: white;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 700;
}

/* LAYOUT */

.container {
    max-width: 1180px;
    margin: auto;
    padding: 32px 20px 20px;
}

/* WELCOME */

.welcome {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 24px;
    flex-wrap: wrap;
    padding: 36px;
    background: linear-gradient(135deg, var(--teal), var(--teal-dark));
    color: white;
    border-radius: 22px;
    box-shadow: 0 18px 40px -22px rgba(10,81,76,.8);
}

.welcome-text {
    max-width: 570px;
}

.welcome h1 {
    margin: 0 0 10px;
    font-size: clamp(26px, 4vw, 36px);
    font-weight: 800;
    letter-spacing: -.025em;
    line-height: 1.2;
}

.welcome p {
    margin: 0;
    color: #d6ece7;
    font-size: 14px;
    line-height: 1.7;
}

.welcome-action {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* BUTTON */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 16px;
    background: var(--teal);
    color: white;
    border: 1px solid transparent;
    border-radius: 10px;
    font: inherit;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: background .15s;
}

.btn:hover {
    background: var(--teal-dark);
}

.welcome .btn {
    background: white;
    color: var(--teal-dark);
}

.welcome .btn:hover {
    background: var(--teal-soft);
}

.welcome .btn-ghost {
    background: transparent;
    color: white;
    border-color: rgba(255,255,255,.5);
}

.welcome .btn-ghost:hover {
    background: rgba(255,255,255,.12);
}

.btn-outline {
    background: white;
    color: var(--teal);
    border-color: var(--border);
}

.btn-outline:hover {
    background: var(--teal-soft);
}

/* ALERT */

.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding: 14px 18px;
    background: #e3f2e8;
    color: #205b3b;
    border: 1px solid #c6e4d0;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
}

/* STATISTICS */

.stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin: 24px 0 16px;
}

.stat {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.stat-icon {
    display: grid;
    place-items: center;
    flex: none;
    width: 48px;
    height: 48px;
    border-radius: 14px;
    font-size: 23px;
    font-weight: 800;
}

.stat-total .stat-icon {
    background: var(--teal-soft);
    color: var(--teal);
}

.stat-wait .stat-icon {
    background: var(--amber-soft);
    color: #a8740c;
}

.stat-ok .stat-icon {
    background: var(--green-soft);
    color: var(--green);
}

.stat-no .stat-icon {
    background: var(--red-soft);
    color: var(--red);
}

.stat p {
    margin: 0 0 3px;
    color: var(--muted);
    font-size: 12px;
}

.stat strong {
    display: block;
    font-size: 29px;
    font-weight: 800;
    line-height: 1.2;
}

/* DISTRIBUTION */

.dist {
    padding: 18px 20px;
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.dist-title {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin: 0 0 12px;
    font-size: 14px;
    font-weight: 800;
}

.dist-title span {
    color: var(--muted);
    font-weight: 500;
}

.dist-bar {
    display: flex;
    height: 12px;
    overflow: hidden;
    background: var(--teal-soft);
    border-radius: 999px;
}

.dist-bar i {
    display: block;
    height: 100%;
}

.c-menunggu {
    background: var(--amber);
}

.c-disetujui {
    background: var(--green);
}

.c-ditolak {
    background: var(--red);
}

.c-selesai {
    background: var(--blue);
}

.dist-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 22px;
    margin-top: 14px;
    color: var(--muted);
    font-size: 12px;
}

.dist-legend b {
    color: var(--text);
}

.dot {
    display: inline-block;
    width: 9px;
    height: 9px;
    margin-right: 7px;
    border-radius: 50%;
}

/* SECTION */

.section-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin: 34px 0 16px;
}

.section-heading h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
}

/* MENU CARDS */

.menu-aksi {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.menu-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    text-decoration: none;
    transition: border-color .15s, transform .15s;
}

.menu-card:hover {
    border-color: var(--teal);
    transform: translateY(-2px);
}

.menu-icon {
    display: grid;
    place-items: center;
    flex: none;
    width: 52px;
    height: 52px;
    background: var(--teal);
    color: white;
    border-radius: 14px;
    font-size: 25px;
}

.menu-card.alt .menu-icon {
    background: var(--teal-soft);
    color: var(--teal);
}

.menu-card strong {
    display: block;
    margin-bottom: 4px;
    font-size: 15px;
}

.menu-card span {
    color: var(--muted);
    font-size: 12px;
    line-height: 1.5;
}

/* TABLE */

.panel {
    padding: 8px 20px 14px;
    overflow-x: auto;
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 15px 12px;
    border-bottom: 1px solid var(--border);
    text-align: left;
    vertical-align: middle;
}

tbody tr:last-child td {
    border-bottom: none;
}

tbody tr:hover {
    background: #f8faf8;
}

th {
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

td {
    font-size: 13px;
}

.id-cell {
    color: var(--teal);
    font-weight: 800;
}

.person {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.person .avatar {
    width: 34px;
    height: 34px;
    background: var(--teal-soft);
    color: var(--teal-dark);
}

.tgl {
    color: var(--muted);
    font-size: 12px;
    white-space: nowrap;
}

/* STATUS */

.status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 11px;
    background: var(--amber-soft);
    color: #7a5608;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    text-transform: capitalize;
    white-space: nowrap;
}

.status::before {
    content: "";
    width: 7px;
    height: 7px;
    background: var(--amber);
    border-radius: 50%;
}

.status-disetujui {
    background: var(--green-soft);
    color: #1f5f3a;
}

.status-disetujui::before {
    background: var(--green);
}

.status-ditolak {
    background: var(--red-soft);
    color: #8a2f2c;
}

.status-ditolak::before {
    background: var(--red);
}

.status-selesai {
    background: var(--blue-soft);
    color: #33437f;
}

.status-selesai::before {
    background: var(--blue);
}

.link-detail {
    display: inline-flex;
    align-items: center;
    padding: 7px 11px;
    color: var(--teal);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
}

.link-detail:hover {
    background: var(--teal-soft);
}

/* EMPTY STATE */

.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 38px 20px;
    color: var(--muted);
    text-align: center;
}

.empty-icon {
    display: grid;
    place-items: center;
    width: 60px;
    height: 60px;
    margin-bottom: 14px;
    background: var(--teal-soft);
    color: var(--teal);
    border-radius: 50%;
    font-size: 27px;
}

.empty h3 {
    margin: 0 0 5px;
    color: var(--text);
    font-size: 16px;
}

.empty p {
    margin: 0;
    font-size: 13px;
}

footer {
    padding: 30px 20px 36px;
    color: var(--muted);
    font-size: 12px;
    text-align: center;
}

/* RESPONSIVE */

@media (max-width: 900px) {
    .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 720px) {
    header {
        padding: 12px 4%;
    }

    .user-chip {
        display: none;
    }

    .menu-aksi {
        grid-template-columns: 1fr;
    }

    .container {
        padding: 22px 14px 12px;
    }

    .panel {
        padding: 8px 14px;
    }

    table, thead, tbody, tr, td {
        display: block;
        width: 100%;
    }

    thead {
        position: absolute;
        left: -9999px;
    }

    tbody tr {
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    tbody tr:last-child {
        border-bottom: none;
    }

    td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        border: none;
    }

    td::before {
        content: attr(data-label);
        flex: none;
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .person {
        justify-content: flex-end;
        text-align: right;
    }
}

@media (max-width: 520px) {
    .brand {
        font-size: 19px;
    }

    .header-right {
        gap: 2px;
    }

    .nav-link {
        padding: 7px 9px;
        font-size: 11px;
    }

    .welcome {
        padding: 25px 20px;
        border-radius: 18px;
    }

    .welcome-action {
        width: 100%;
    }

    .welcome-action .btn {
        flex: 1;
    }

    .stats {
        gap: 10px;
    }

    .stat {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 15px;
    }

    .stat strong {
        font-size: 25px;
    }

    .section-heading h2 {
        font-size: 18px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        transition: none !important;
    }
}
</style>
</head>

<body>

<header>
    <a class="brand" href="v_home_petugas.php">
        <span class="brand-mark">p</span>
        pinjam.in
    </a>

    <nav class="header-right" aria-label="Navigasi utama">
        <span class="user-chip">
            <span class="avatar">
                <?= e_petugas(strtoupper(substr($namaUser, 0, 1))) ?>
            </span>
            <?= e_petugas($namaUser) ?>
        </span>

        <a class="nav-link" href="<?= e_petugas($linkPengajuan) ?>">
            Kelola Pengajuan
        </a>

        <a class="nav-link" href="<?= e_petugas($linkPengembalian) ?>">
            Pengembalian Alat
        </a>

        <a class="nav-link" href="<?= e_petugas($linkLaporan) ?>">
            Cetak Laporan
        </a>

        <a class="nav-link nav-logout" href="<?= e_petugas($linkLogout) ?>">
            Keluar
        </a>
    </nav>
</header>

<main class="container">

    <?php if ($pesan !== ""): ?>
        <div class="alert" role="status">
            ✓ <?= e_petugas($pesan) ?>
        </div>
    <?php endif; ?>

    <section class="welcome">
        <div class="welcome-text">
            <h1>Halo, <?= e_petugas($namaUser) ?></h1>
            <p>
                Periksa pengajuan peminjaman dan pastikan setiap permintaan
                diproses sesuai ketersediaan alat.
            </p>
        </div>

        <div class="welcome-action">
            <a class="btn" href="<?= e_petugas($linkPengajuan) ?>">
                Kelola Pengajuan
            </a>

            <a class="btn btn-ghost" href="<?= e_petugas($linkPengembalian) ?>">
                Pantau Pengembalian
            </a>
        </div>
    </section>

    <section class="stats" aria-label="Ringkasan pengajuan">

        <div class="stat stat-total">
            <div class="stat-icon">▤</div>
            <div>
                <p>Total pengajuan</p>
                <strong><?= $total ?></strong>
            </div>
        </div>

        <div class="stat stat-wait">
            <div class="stat-icon">◷</div>
            <div>
                <p>Menunggu persetujuan</p>
                <strong><?= $menunggu ?></strong>
            </div>
        </div>

        <div class="stat stat-ok">
            <div class="stat-icon">✓</div>
            <div>
                <p>Disetujui</p>
                <strong><?= $disetujui ?></strong>
            </div>
        </div>

        <div class="stat stat-no">
            <div class="stat-icon">×</div>
            <div>
                <p>Ditolak</p>
                <strong><?= $ditolak ?></strong>
            </div>
        </div>

    </section>

    <?php if ($total > 0): ?>
        <section class="dist">
            <p class="dist-title">
                Komposisi status
                <span><?= $total ?> pengajuan</span>
            </p>

            <div class="dist-bar">
                <i class="c-menunggu" style="width: <?= $pct($menunggu) ?>%"></i>
                <i class="c-disetujui" style="width: <?= $pct($disetujui) ?>%"></i>
                <i class="c-ditolak" style="width: <?= $pct($ditolak) ?>%"></i>
                <i class="c-selesai" style="width: <?= $pct($selesai) ?>%"></i>
            </div>

            <div class="dist-legend">
                <span><i class="dot c-menunggu"></i>Menunggu <b><?= $menunggu ?></b></span>
                <span><i class="dot c-disetujui"></i>Disetujui <b><?= $disetujui ?></b></span>
                <span><i class="dot c-ditolak"></i>Ditolak <b><?= $ditolak ?></b></span>
                <span><i class="dot c-selesai"></i>Selesai <b><?= $selesai ?></b></span>
            </div>
        </section>
    <?php endif; ?>

    <section>
        <div class="section-heading">
            <h2>Menu Petugas</h2>
        </div>

        <div class="menu-aksi">

            <a class="menu-card" href="<?= e_petugas($linkPengajuan) ?>">
                <div class="menu-icon">▤</div>
                <div>
                    <strong>Kelola Pengajuan</strong>
                    <span>Setujui atau tolak permintaan peminjaman</span>
                </div>
            </a>

            <a class="menu-card alt" href="<?= e_petugas($linkPengembalian) ?>">
                <div class="menu-icon">↩</div>
                <div>
                    <strong>Pantau Pengembalian</strong>
                    <span>Periksa dan konfirmasi pengembalian alat</span>
                </div>
            </a>

            <a class="menu-card alt" href="<?= e_petugas($linkLaporan) ?>">
                <div class="menu-icon">▧</div>
                <div>
                    <strong>Cetak Laporan</strong>
                    <span>Lihat dan cetak laporan peminjaman atau pengembalian</span>
                </div>
            </a>

        </div>
    </section>

    <section>
        <div class="section-heading">
            <h2>Pengajuan Terbaru</h2>

            <a class="btn btn-outline" href="<?= e_petugas($linkPengajuan) ?>">
                Lihat Semua Pengajuan
            </a>
        </div>

        <div class="panel">

            <?php if (empty($data_pengajuan)): ?>

                <div class="empty">
                    <div class="empty-icon">▤</div>
                    <h3>Belum ada pengajuan</h3>
                    <p>Pengajuan peminjam akan muncul di sini.</p>
                </div>

            <?php else: ?>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach (array_slice($data_pengajuan, 0, 5) as $p): ?>

                        <?php
                        $status = $p["status"] ?? "menunggu";
                        $statusClass = "";

                        if ($status === "disetujui") {
                            $statusClass = "status-disetujui";
                        } elseif ($status === "ditolak") {
                            $statusClass = "status-ditolak";
                        } elseif ($status === "selesai") {
                            $statusClass = "status-selesai";
                        }

                        $namaPeminjam = $p["nama"]
                            ?? $p["username"]
                            ?? "-";

                        $inisial = strtoupper(substr($namaPeminjam, 0, 1));
                        ?>

                        <tr>
                            <td class="id-cell" data-label="ID">
                                #<?= (int)($p["id_peminjaman"] ?? 0) ?>
                            </td>

                            <td data-label="Peminjam">
                                <div class="person">
                                    <span class="avatar">
                                        <?= e_petugas($inisial) ?>
                                    </span>
                                    <?= e_petugas($namaPeminjam) ?>
                                </div>
                            </td>

                            <td class="tgl" data-label="Tanggal">
                                <?= e_petugas($p["tanggal_pengajuan"] ?? "-") ?>
                            </td>

                            <td data-label="Status">
                                <span class="status <?= e_petugas($statusClass) ?>">
                                    <?= e_petugas($status) ?>
                                </span>
                            </td>

                            <td data-label="Aksi">
                                <a class="link-detail" href="<?= e_petugas($linkPengajuan) ?>">
                                    Detail →
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </section>

</main>

<footer>
    Pinjam.in — Dashboard Petugas
</footer>

</body>
</html>