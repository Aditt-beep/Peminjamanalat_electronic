```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['data'] ?? [];
$namaUser = $user['nama'] ?? $user['username'] ?? 'Pengguna';
$role = strtolower($user['role'] ?? 'peminjam');

$rows = $data_log ?? $rows ?? [];
$rows = is_array($rows) ? $rows : [];

$totalLog = count($rows);
$daftarUser = [];

foreach ($rows as $row) {
    $username = $row['username'] ?? $row['nama'] ?? '';
    if ($username !== '') {
        $daftarUser[] = $username;
    }
}

$totalUserAktif = count(array_unique($daftarUser));

function log_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function log_icon($name) {
    $icons = [
        'grid' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',

        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>',

        'tool' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/>',

        'tag' => '<path d="M20.59 13.41 11 3.83 3.83 11l9.58 9.59a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.83Z"/><circle cx="7" cy="7" r="1"/>',

        'clipboard' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V2h6v2"/>',

        'return' => '<path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/>',

        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',

        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',

        'activity' => '<path d="M3 12h4l3-8 4 16 3-8h4"/>'
    ];

    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' .
        ($icons[$name] ?? '') .
        '</svg>';
}

$dashboardUrl = '../View/v_homeadmin.php';
$userUrl = '../Controller/c_user.php?aksi=tampil';
$alatUrl = '../Controller/c_alat.php?aksi=tampil';
$kategoriUrl = '../Controller/c_kategori.php?aksi=tampil';
$peminjamanUrl = '../Controller/c_peminjaman.php?aksi=tampil';
$pengembalianUrl = '../Controller/c_pengembalian.php?aksi=tampil';
$logUrl = '../Controller/c_log.php?aksi=tampil';
$logoutUrl = '../Controller/c_logout.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Log Aktivitas | pinjam.in</title>

<style>
:root {
    --cream: #f4f1ea;
    --cream-deep: #efebe1;
    --teal-dark: #0f6b64;
    --teal: #1a7a72;
    --teal-deep: #0a4a45;
    --ink: #1c2b29;
    --muted: #7d8a87;
    --card-border: #e4e0d6;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    background: var(--cream-deep);
    color: var(--ink);
    font-size: 14px;
}

a {
    color: inherit;
    text-decoration: none;
}

.layout {
    display: flex;
    min-height: 100vh;
}

/* =========================
   SIDEBAR
========================= */

.sidebar {
    width: 260px;
    min-width: 260px;
    min-height: 100vh;
    background: var(--teal-deep);
    color: #fff;
    display: flex;
    flex-direction: column;
    padding: 16px 13px;
    position: fixed;
    inset: 0 auto 0 0;
    overflow-y: auto;
    z-index: 100;
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 35px;
    padding: 0 7px;
}

.brand .logo {
    width: 39px;
    height: 39px;
    border-radius: 9px;
    background: var(--teal);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.brand .logo svg {
    width: 21px;
    height: 21px;
}

.brand-text {
    line-height: 1.1;
}

.brand-text .name {
    font-weight: 800;
    font-size: 19px;
    color: #fff;
}

.brand-text .tag {
    font-size: 10px;
    letter-spacing: 1px;
    color: #8fc9c2;
    font-weight: 700;
    margin-top: 2px;
}

.nav-group-label {
    font-size: 12px;
    letter-spacing: .7px;
    color: #7fa7a1;
    font-weight: 750;
    margin: 18px 11px 9px;
    text-transform: uppercase;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 12px 13px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 600;
    color: #dce9e7;
    margin-bottom: 3px;
    transition: background .15s ease;
}

.nav-item:hover {
    background: rgba(255,255,255,.08);
}

.nav-item.active {
    background: var(--teal);
    color: #fff;
}

.nav-item svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    opacity: .9;
}

.nav-item.active svg {
    opacity: 1;
}

.sidebar-footer {
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,.12);
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 12px 13px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #f3c9c9;
}

.logout-btn:hover {
    background: rgba(255,255,255,.08);
}

.logout-btn svg {
    width: 18px;
    height: 18px;
}

/* =========================
   KONTEN UTAMA
========================= */

.main {
    flex: 1;
    min-width: 0;
    margin-left: 260px;
    padding: 30px 46px;
    width: calc(100% - 260px);
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 32px;
}

.topbar h1 {
    font-size: 26px;
    font-weight: 850;
    margin: 0 0 5px;
    color: #18343a;
}

.topbar p {
    margin: 0;
    font-size: 14px;
    color: var(--muted);
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: none;
    border-radius: 9px;
    padding: 12px 17px;
    background: var(--teal-dark);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    transition: background .2s;
}

.btn-back:hover {
    background: var(--teal-deep);
}

/* =========================
   RINGKASAN
========================= */

.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.summary-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: var(--cream);
    border: 1px solid var(--card-border);
    border-radius: 13px;
}

.summary-icon {
    width: 46px;
    height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #d9efe9;
    color: var(--teal-dark);
    flex-shrink: 0;
}

.summary-icon svg {
    width: 23px;
    height: 23px;
}

.summary-label {
    color: var(--muted);
    font-size: 12px;
    font-weight: 600;
}

.summary-value {
    font-size: 25px;
    font-weight: 800;
    color: var(--ink);
    margin-top: 4px;
}

.summary-note {
    font-size: 11px;
    color: var(--muted);
    margin-top: 3px;
}

/* =========================
   PANEL DAN TABEL
========================= */

.panel {
    background: var(--cream);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    padding: 25px 25px 0;
    overflow: hidden;
}

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 16px;
}

.panel-title {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    color: #172f32;
}

.record-count {
    display: inline-block;
    padding: 6px 11px;
    border-radius: 999px;
    background: #d9efe9;
    color: var(--teal-dark);
    font-size: 11px;
    font-weight: 750;
    white-space: nowrap;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap;
}

thead th {
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--muted);
    font-weight: 750;
    padding: 10px 12px;
    border-bottom: 1px solid var(--card-border);
}

tbody td {
    padding: 16px 12px;
    font-size: 13px;
    color: #17343b;
    border-bottom: 1px solid var(--card-border);
}

tbody tr:last-child td {
    border-bottom: none;
}

tbody tr:hover {
    background: rgba(255,255,255,.4);
}

.username-text {
    font-weight: 600;
}

.time-date {
    font-weight: 600;
    color: #314b4c;
}

.time-clock {
    margin-top: 4px;
    font-size: 11px;
    color: #87938e;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.badge-activity {
    background: #d9efe9;
    color: #0f6b64;
}

.badge-admin {
    background: #d9efe9;
    color: #0f6b64;
}

.badge-petugas {
    background: #fdecd2;
    color: #8a5a10;
}

.badge-peminjam {
    background: #e4e0e6;
    color: #5b5363;
}

.badge-default {
    background: #e8e8e3;
    color: #616b68;
}

/* =========================
   DATA KOSONG
========================= */

.empty-row td {
    text-align: center;
    padding: 0;
}

.empty-state {
    text-align: center;
    padding: 45px 20px;
    white-space: normal;
}

.empty-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background: #e8e4da;
    color: #81928d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
}

.empty-icon svg {
    width: 26px;
    height: 26px;
}

.empty-state h3 {
    font-size: 15px;
    margin: 0 0 8px;
}

.empty-state p {
    font-size: 13px;
    color: var(--muted);
    margin: 0;
}

.page-footer {
    padding: 24px 0 5px;
    text-align: center;
    font-size: 12px;
    color: #87938e;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 1000px) {
    .sidebar {
        width: 230px;
        min-width: 230px;
    }

    .main {
        margin-left: 230px;
        width: calc(100% - 230px);
        padding: 27px 24px;
    }

    .topbar h1 {
        font-size: 23px;
    }

    .panel {
        padding: 20px 17px 0;
    }
}

@media (max-width: 700px) {
    .layout {
        display: block;
    }

    .sidebar {
        position: relative;
        width: 100%;
        min-width: 0;
        min-height: auto;
        height: auto;
        padding: 14px;
        overflow: visible;
    }

    .brand {
        margin-bottom: 12px;
    }

    .sidebar .nav-group-label {
        display: none;
    }

    .sidebar {
        flex-direction: column;
    }

    .sidebar > .nav-group-label {
        display: none;
    }

    .sidebar .nav-item {
        display: inline-flex;
        width: auto;
        padding: 10px;
        margin: 2px;
    }

    .sidebar {
        align-items: stretch;
    }

    .sidebar-footer {
        margin-top: 10px;
    }

    .main {
        margin-left: 0;
        width: 100%;
        padding: 23px 15px;
    }

    .topbar {
        align-items: flex-start;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 23px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .panel {
        padding: 18px 13px 0;
    }

    .panel-header {
        align-items: flex-start;
    }

    thead th,
    tbody td {
        padding: 13px 10px;
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
                    <rect x="14" y="3" width="7" height="7" rx="1.5" fill="#fff" opacity=".7"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5" fill="#fff" opacity=".7"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5" fill="#fff"/>
                </svg>
            </div>

            <div class="brand-text">
                <div class="name">pinjam.in</div>
                <div class="tag">ADMIN PANEL</div>
            </div>
        </div>

        <div class="nav-group-label">Menu Utama</div>

        <a class="nav-item" href="<?= log_h($dashboardUrl) ?>">
            <?= log_icon('grid') ?>
            <span>Dashboard</span>
        </a>

        <div class="nav-group-label">Kelola Data</div>

        <a class="nav-item" href="v_user.php<?= log_h($userUrl) ?>">
            <?= log_icon('user') ?>
            <span>Data User</span>
        </a>

        <a class="nav-item" href="v_alat.php<?= log_h($alatUrl) ?>">
            <?= log_icon('tool') ?>
            <span>Data Alat</span>
        </a>

        <a class="nav-item" href="v_kategori.php<?= log_h($kategoriUrl) ?>">
            <?= log_icon('tag') ?>
            <span>Kategori</span>
        </a>

        <a class="nav-item" href="v_peminjaman.php<?= log_h($peminjamanUrl) ?>">
            <?= log_icon('clipboard') ?>
            <span>Data Peminjaman</span>
        </a>

        <a class="nav-item" href="v_pengembalian.php<?= log_h($pengembalianUrl) ?>">
            <?= log_icon('return') ?>
            <span>Data Pengembalian</span>
        </a>

        <div class="nav-group-label">Lainnya</div>

        <a class="nav-item active" href="<?= log_h($logUrl) ?>">
            <?= log_icon('clock') ?>
            <span>Log Aktivitas</span>
        </a>

        <div class="sidebar-footer">
            <a class="logout-btn" href="<?= log_h($logoutUrl) ?>">
                <?= log_icon('logout') ?>
                <span>Keluar</span>
            </a>
        </div>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main">

        <div class="topbar">
            <div>
                <h1>Log Aktivitas</h1>
                <p>Riwayat aktivitas pengguna sistem.</p>
            </div>

            <a href="<?= log_h($dashboardUrl) ?>" class="btn-back">
                &larr; Dashboard
            </a>
        </div>

        <!-- RINGKASAN -->
        <div class="summary-grid">

            <div class="summary-card">
                <div class="summary-icon">
                    <?= log_icon('activity') ?>
                </div>

                <div>
                    <div class="summary-label">Total Log</div>
                    <div class="summary-value"><?= $totalLog ?></div>
                    <div class="summary-note">Total aktivitas yang tercatat</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <?= log_icon('user') ?>
                </div>

                <div>
                    <div class="summary-label">User Tercatat</div>
                    <div class="summary-value"><?= $totalUserAktif ?></div>
                    <div class="summary-note">Jumlah pengguna unik</div>
                </div>
            </div>

        </div>

        <!-- TABEL LOG -->
        <div class="panel">

            <div class="panel-header">
                <h2 class="panel-title">Riwayat Aktivitas</h2>
                <span class="record-count"><?= $totalLog ?> data</span>
            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th style="width:70px">No</th>
                            <th>Username</th>
                            <th>Waktu Aktivitas</th>
                            <th>Aktivitas</th>
                            <th>Role</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($rows)): ?>

                        <?php $no = 1; ?>

                        <?php foreach ($rows as $row): ?>

                            <?php
                            $username = $row['username']
                                ?? $row['nama']
                                ?? 'Tidak diketahui';

                            $waktu = $row['waktu_aktivitas']
                                ?? $row['waktu']
                                ?? '';

                            $aktivitas = $row['aktivitas']
                                ?? 'Login';

                            $roleRow = strtolower(
                                $row['role'] ?? '-'
                            );

                            $timestamp = $waktu !== ''
                                ? strtotime((string)$waktu)
                                : false;

                            $tanggal = $timestamp
                                ? date('d-m-Y', $timestamp)
                                : ($waktu !== '' ? $waktu : '-');

                            $jam = $timestamp
                                ? date('H:i:s', $timestamp)
                                : '';

                            if (in_array($roleRow, ['admin', 'administrator'])) {
                                $roleClass = 'badge-admin';
                            } elseif ($roleRow === 'petugas') {
                                $roleClass = 'badge-petugas';
                            } elseif ($roleRow === 'peminjam') {
                                $roleClass = 'badge-peminjam';
                            } else {
                                $roleClass = 'badge-default';
                            }
                            ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <span class="username-text">
                                        <?= log_h($username) ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="time-date">
                                        <?= log_h($tanggal) ?>
                                    </div>

                                    <?php if ($jam !== ''): ?>
                                        <div class="time-clock">
                                            <?= log_h($jam) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge badge-activity">
                                        <?= log_h($aktivitas) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge <?= $roleClass ?>">
                                        <?= log_h(ucfirst($roleRow)) ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr class="empty-row">
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <?= log_icon('clock') ?>
                                    </div>

                                    <h3>Belum Ada Aktivitas</h3>

                                    <p>
                                        Belum ada data log aktivitas yang tersedia.
                                    </p>
                                </div>
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div>

        <footer class="page-footer">
            pinjam.in &copy; <?= date('Y') ?> — Sistem Peminjaman Elektronik
        </footer>

    </main>

</div>

</body>
</html>
```