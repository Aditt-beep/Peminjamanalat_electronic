<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['data'] ?? [];
$namaUser = $user['nama'] ?? $user['username'] ?? 'Pengguna';
$role = strtolower($user['role'] ?? 'peminjam');

// Data dari Controller
$rows = isset($rows) && is_array($rows) ? $rows : [];
$total = (int) ($total ?? count($rows));
$page = max(1, (int) ($page ?? 1));
$totalPage = max(1, (int) ($totalPage ?? 1));
$daftar_aksi = is_array($daftar_aksi ?? null) ? $daftar_aksi : [];

$totalUserAktif = count(array_unique(array_filter(array_map(
    fn($row) => $row['username'] ?? $row['nama'] ?? '',
    $rows
))));

function log_h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function log_icon($name) {
    $icons = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>',
        'tool' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/>',
        'tag' => '<path d="M20.59 13.41 11 3.83 3.83 11l9.58 9.59a2 2 0 0 0 2.83 0l4.35-4.35a2 2 0 0 0 0-2.83Z"/><circle cx="7" cy="7" r="1"/>',
        'clipboard' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V2h6v2"/>',
        'return' => '<path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
        'activity' => '<path d="M3 12h4l3-8 4 16 3-8h4"/>'
    ];

    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
        . ($icons[$name] ?? '') . '</svg>';
}

$dashboardUrl = '../View/v_homeadmin.php';
$userUrl = '../Controller/c_user.php?aksi=tampil';
$alatUrl = '../Controller/c_alat.php?aksi=tampil';
$kategoriUrl = '../Controller/c_kategori.php?aksi=tampil';
$peminjamanUrl = '../Controller/c_peminjaman.php?aksi=tampil';
$pengembalianUrl = '../Controller/c_pengembalian.php?aksi=tampil';
$logUrl = '../Controller/c_log.php';
$logoutUrl = '../Controller/c_logout.php';

$jenisTerpilih = trim($_GET['jenis'] ?? '');
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

* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--cream-deep);
    color: var(--ink);
    font-size: 14px;
}

a { color: inherit; text-decoration: none; }

.layout { display: flex; min-height: 100vh; }

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
    z-index: 10;
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 35px;
    padding: 0 7px;
}

.logo {
    width: 39px;
    height: 39px;
    border-radius: 9px;
    background: var(--teal);
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo svg { width: 21px; height: 21px; }

.brand-name { font-weight: 800; font-size: 19px; }
.brand-tag { font-size: 10px; letter-spacing: 1px; color: #8fc9c2; font-weight: 700; }

.nav-group-label {
    font-size: 11px;
    letter-spacing: .7px;
    color: #7fa7a1;
    font-weight: 750;
    margin: 18px 11px 9px;
    text-transform: uppercase;
}

.nav-item, .logout-btn {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 12px 13px;
    border-radius: 9px;
    font-weight: 600;
    margin-bottom: 3px;
}

.nav-item { color: #dce9e7; }
.nav-item:hover, .logout-btn:hover { background: rgba(255,255,255,.08); }
.nav-item.active { background: var(--teal); color: #fff; }

.nav-item svg, .logout-btn svg { width: 18px; height: 18px; flex-shrink: 0; }

.sidebar-footer {
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,.12);
}

.logout-btn { color: #f3c9c9; }

.main {
    flex: 1;
    min-width: 0;
    margin-left: 260px;
    padding: 30px 40px;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 28px;
}

h1 { font-size: 26px; font-weight: 850; margin: 0 0 5px; color: #18343a; }
.subtitle { margin: 0; color: var(--muted); }

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 0;
    border-radius: 9px;
    padding: 11px 15px;
    background: var(--teal-dark);
    color: #fff;
    font-weight: 700;
    cursor: pointer;
}

.btn:hover { background: var(--teal-deep); }

.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 23px;
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

.summary-icon svg { width: 23px; height: 23px; }
.summary-label { color: var(--muted); font-size: 12px; font-weight: 600; }
.summary-value { font-size: 25px; font-weight: 800; margin-top: 4px; }
.summary-note { font-size: 11px; color: var(--muted); margin-top: 3px; }

.panel {
    background: var(--cream);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    padding: 23px;
    margin-bottom: 20px;
}

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.panel-title { margin: 0; font-size: 17px; font-weight: 800; color: #172f32; }

.record-count {
    padding: 6px 11px;
    border-radius: 999px;
    background: #d9efe9;
    color: var(--teal-dark);
    font-size: 11px;
    font-weight: 750;
    white-space: nowrap;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: 10px;
    margin-bottom: 18px;
}

.filter-form label {
    display: block;
    font-size: 12px;
    color: var(--muted);
    font-weight: 700;
    margin-bottom: 6px;
}

.filter-form select {
    min-width: 220px;
    max-width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--card-border);
    border-radius: 8px;
    background: #fff;
    color: var(--ink);
}

.btn-light {
    background: #e4e0d6;
    color: var(--ink);
}

.btn-light:hover { background: #d8d3c7; }

.table-wrapper { width: 100%; overflow-x: auto; }

table { width: 100%; border-collapse: collapse; white-space: nowrap; }

thead th {
    text-align: left;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--muted);
    font-weight: 750;
    padding: 12px;
    border-bottom: 1px solid var(--card-border);
}

tbody td {
    padding: 15px 12px;
    font-size: 13px;
    color: #17343b;
    border-bottom: 1px solid var(--card-border);
}

tbody tr:last-child td { border-bottom: none; }
tbody tr:hover { background: rgba(255,255,255,.4); }

.username-text { font-weight: 650; }
.time-date { font-weight: 600; }
.time-clock { margin-top: 4px; font-size: 11px; color: #87938e; }

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.badge-activity, .badge-admin { background: #d9efe9; color: #0f6b64; }
.badge-petugas { background: #fdecd2; color: #8a5a10; }
.badge-peminjam { background: #e4e0e6; color: #5b5363; }
.badge-default { background: #e8e8e3; color: #616b68; }

.empty-state { text-align: center; padding: 40px 15px; white-space: normal; }
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
.empty-icon svg { width: 26px; height: 26px; }
.empty-state h3 { font-size: 15px; margin: 0 0 8px; }
.empty-state p { font-size: 13px; color: var(--muted); margin: 0; }

.pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    border-top: 1px solid var(--card-border);
    padding-top: 17px;
    margin-top: 15px;
}

.pagination-info { color: var(--muted); font-size: 12px; }
.pagination-links { display: flex; gap: 8px; align-items: center; }
.page-link {
    padding: 8px 12px;
    border: 1px solid var(--card-border);
    border-radius: 8px;
    background: #fff;
    font-size: 12px;
    font-weight: 700;
}
.page-link.disabled { opacity: .45; pointer-events: none; }
.page-link.current { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }

.page-footer { padding: 15px 0 5px; text-align: center; font-size: 12px; color: #87938e; }

@media (max-width: 850px) {
    .sidebar { width: 225px; min-width: 225px; }
    .main { margin-left: 225px; padding: 25px 20px; }
}

@media (max-width: 650px) {
    .sidebar { position: relative; width: 100%; min-width: 0; min-height: auto; }
    .main { margin-left: 0; padding: 20px 14px; }
    .topbar { align-items: flex-start; flex-direction: column; }
    .summary-grid { grid-template-columns: 1fr; }
    .panel { padding: 16px 12px; }
}
</style>
</head>

<body>
<div class="layout">

<aside class="sidebar">
    <div class="brand">
        <div class="logo"><?= log_icon('grid') ?></div>
        <div>
            <div class="brand-name">pinjam.in</div>
            <div class="brand-tag">ADMIN PANEL</div>
        </div>
    </div>

    <div class="nav-group-label">Menu Utama</div>
    <a class="nav-item" href="<?= log_h($dashboardUrl) ?>">
        <?= log_icon('grid') ?><span>Dashboard</span>
    </a>

    <div class="nav-group-label">Kelola Data</div>
    <a class="nav-item" href="<?= log_h($userUrl) ?>"><?= log_icon('user') ?><span>Data User</span></a>
    <a class="nav-item" href="<?= log_h($alatUrl) ?>"><?= log_icon('tool') ?><span>Data Alat</span></a>
    <a class="nav-item" href="<?= log_h($kategoriUrl) ?>"><?= log_icon('tag') ?><span>Kategori</span></a>
    <a class="nav-item" href="<?= log_h($peminjamanUrl) ?>"><?= log_icon('clipboard') ?><span>Data Peminjaman</span></a>
    <a class="nav-item" href="<?= log_h($pengembalianUrl) ?>"><?= log_icon('return') ?><span>Data Pengembalian</span></a>

    <div class="nav-group-label">Lainnya</div>
    <a class="nav-item active" href="<?= log_h($logUrl) ?>"><?= log_icon('clock') ?><span>Log Aktivitas</span></a>

    <div class="sidebar-footer">
        <a class="logout-btn" href="<?= log_h($logoutUrl) ?>"><?= log_icon('logout') ?><span>Keluar</span></a>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div>
            <h1>Log Aktivitas</h1>
            <p class="subtitle">Riwayat aktivitas pengguna sistem.</p>
        </div>
        <a href="<?= log_h($dashboardUrl) ?>" class="btn">&larr; Dashboard</a>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon"><?= log_icon('activity') ?></div>
            <div>
                <div class="summary-label">Total Log</div>
                <div class="summary-value"><?= $total ?></div>
                <div class="summary-note">Jumlah seluruh aktivitas sesuai filter</div>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon"><?= log_icon('user') ?></div>
            <div>
                <div class="summary-label">User pada Halaman Ini</div>
                <div class="summary-value"><?= $totalUserAktif ?></div>
                <div class="summary-note">Pengguna unik pada data yang ditampilkan</div>
            </div>
        </div>
    </div>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Riwayat Aktivitas</h2>
            <span class="record-count"><?= count($rows) ?> data di halaman ini</span>
        </div>

        <form class="filter-form" method="GET" action="../Controller/c_log.php">
            <div>
                <label for="jenis">Filter aktivitas</label>
                <select name="jenis" id="jenis">
                    <option value="">Semua aktivitas</option>
                    <?php foreach ($daftar_aksi as $aksi): ?>
                        <option value="<?= log_h($aksi) ?>" <?= $jenisTerpilih === $aksi ? 'selected' : '' ?>>
                            <?= log_h($aksi) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Terapkan</button>
            <a href="../Controller/c_log.php" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Username</th>
                        <th>Waktu Aktivitas</th>
                        <th>Aktivitas</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($rows) > 0): ?>
                    <?php foreach ($rows as $index => $row): ?>
                        <?php
                        $username = $row['username'] ?? $row['nama'] ?? ('User #' . ($row['id_user'] ?? '?'));
                        $waktu = $row['waktu'] ?? '';
                        $timestamp = $waktu !== '' ? strtotime($waktu) : false;
                        $roleRow = strtolower($row['role'] ?? '-');

                        if (in_array($roleRow, ['admin', 'administrator'], true)) {
                            $roleClass = 'badge-admin';
                        } elseif ($roleRow === 'petugas') {
                            $roleClass = 'badge-petugas';
                        } elseif ($roleRow === 'peminjam') {
                            $roleClass = 'badge-peminjam';
                        } else {
                            $roleClass = 'badge-default';
                        }

                        $nomor = (($page - 1) * 20) + $index + 1;
                        ?>
                        <tr>
                            <td><?= $nomor ?></td>
                            <td><span class="username-text"><?= log_h($username) ?></span></td>
                            <td>
                                <div class="time-date">
                                    <?= $timestamp !== false ? log_h(date('d-m-Y', $timestamp)) : log_h($waktu ?: '-') ?>
                                </div>
                                <?php if ($timestamp !== false): ?>
                                    <div class="time-clock"><?= log_h(date('H:i:s', $timestamp)) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-activity"><?= log_h($row['aktivitas'] ?? '-') ?></span></td>
                            <td><span class="badge <?= $roleClass ?>"><?= log_h(ucfirst($roleRow)) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><?= log_icon('clock') ?></div>
                                <h3>Belum Ada Aktivitas</h3>
                                <p>Data aktivitas belum diterima oleh halaman ini.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPage > 1): ?>
            <div class="pagination">
                <div class="pagination-info">
                    Halaman <?= $page ?> dari <?= $totalPage ?>
                </div>
                <div class="pagination-links">
                    <?php
                    $queryFilter = $jenisTerpilih !== ''
                        ? '&jenis=' . urlencode($jenisTerpilih)
                        : '';
                    ?>
                    <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
                       href="?page=<?= max(1, $page - 1) . $queryFilter ?>">Sebelumnya</a>
                    <span class="page-link current"><?= $page ?></span>
                    <a class="page-link <?= $page >= $totalPage ? 'disabled' : '' ?>"
                       href="?page=<?= min($totalPage, $page + 1) . $queryFilter ?>">Berikutnya</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <footer class="page-footer">
        pinjam.in &copy; <?= date('Y') ?> — Sistem Peminjaman Elektronik
    </footer>
</main>
</div>
</body>
</html>