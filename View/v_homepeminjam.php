<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION["data"]["id_user"]) ||
    strtolower(trim($_SESSION["data"]["role"] ?? "")) !== "peminjam"
) {
    header("Location: ../View/v_login.php");
    exit;
}

require_once __DIR__ . "/../Model/m_alat.php";
require_once __DIR__ . "/../Model/m_kategori.php";

$user = $_SESSION["data"];
$namaUser = $user["nama"] ?? $user["username"] ?? "Peminjam";

$alatModel = new m_alat();
$kategoriModel = new m_kategori();

$semuaAlat = $alatModel->tampil_data();
$semuaKategori = $kategoriModel->tampil_data();

$pesanPengajuan = $_SESSION["pesan_pengajuan"] ?? "";
unset($_SESSION["pesan_pengajuan"]);

$pesanPengembalian = $_SESSION["pesan_pengajuan_kembali"] ?? "";
unset($_SESSION["pesan_pengajuan_kembali"]);

function h_home_peminjam($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, "UTF-8");
}

function alat_tersedia_home($alat)
{
    return (
        ($alat["status"] ?? "") === "tersedia" &&
        (int) ($alat["jumlah"] ?? 0) > 0 &&
        ($alat["kondisi"] ?? "") !== "rusak_berat"
    );
}

// Ringkasan angka untuk kartu statistik
$totalAlat = is_array($semuaAlat) ? count($semuaAlat) : 0;
$totalTersedia = 0;
$totalKategori = is_array($semuaKategori) ? count($semuaKategori) : 0;

if (!empty($semuaAlat)) {
    foreach ($semuaAlat as $a) {
        if (alat_tersedia_home($a)) {
            $totalTersedia++;
        }
    }
}

$inisial = strtoupper(mb_substr($namaUser, 0, 1, "UTF-8"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam.in — Dashboard Peminjam</title>

    <style>
        :root {
            --cream: #f4f1ea;
            --cream-dark: #ebe7dc;
            --teal-dark: #0f6b64;
            --teal-darker: #0b554f;
            --teal-light: #e4f1ed;
            --text: #243b38;
            --muted: #74817c;
            --white: #ffffff;
            --border: #e4e7e2;
            --red-light: #fff0ec;
            --red: #a33f2e;
            --green: #23704b;
            --green-light: #e7f6ec;
            --shadow-sm: 0 1px 2px rgba(36, 59, 56, 0.05);
            --shadow-md: 0 10px 28px rgba(15, 107, 100, 0.12);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            background:
                radial-gradient(900px 400px at 100% -5%, #e9efe6 0%, transparent 60%),
                var(--cream);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; color: inherit; }
        button, input, select { font: inherit; }

        svg.ic {
            width: 1em;
            height: 1em;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        /* ---------- Navbar ---------- */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 14px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--teal-dark);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -1px;
            white-space: nowrap;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--teal-dark);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .brand span { color: var(--text); }

        .nav-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            flex-wrap: wrap;
        }

        .nav-link {
            font-size: 14px;
            font-weight: 500;
            color: #52635e;
            padding: 9px 14px;
            border-radius: 8px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-link:hover {
            color: var(--teal-dark);
            background: var(--teal-light);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-left: 8px;
            padding: 4px 6px 4px 4px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--white);
        }

        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--teal-light);
            color: var(--teal-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        .logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--teal-dark);
            color: white;
            border-radius: 999px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.15s;
        }

        .logout:hover { background: var(--teal-darker); }

        /* ---------- Layout ---------- */
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 30px 22px 56px;
        }

        /* ---------- Hero ---------- */
        .welcome {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0f6b64 0%, #0b554f 100%);
            color: white;
            padding: 38px 36px;
            border-radius: 20px;
            margin-bottom: 22px;
            box-shadow: var(--shadow-md);
        }

        .welcome::before,
        .welcome::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            pointer-events: none;
        }

        .welcome::before { width: 280px; height: 280px; right: -70px; top: -110px; }
        .welcome::after  { width: 180px; height: 180px; right: 120px; bottom: -100px; }

        .welcome-inner { position: relative; z-index: 1; }

        .welcome small {
            display: inline-block;
            background: rgba(255, 255, 255, 0.14);
            color: #d5e9e3;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            padding: 6px 12px;
            border-radius: 999px;
        }

        .welcome h1 {
            font-size: 32px;
            letter-spacing: -0.5px;
            margin: 16px 0 10px;
        }

        .welcome p {
            color: #e0eeea;
            line-height: 1.7;
            margin: 0 0 22px;
            max-width: 600px;
            font-size: 15px;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.15s, background 0.15s;
        }

        .hero-btn:hover { transform: translateY(-1px); }

        .hero-btn.light { background: #fff; color: var(--teal-dark); }
        .hero-btn.ghost {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .hero-btn.ghost:hover { background: rgba(255, 255, 255, 0.2); }

        /* ---------- Alerts ---------- */
        .message {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 14px;
            background: var(--teal-light);
            color: var(--teal-dark);
            border: 1px solid #c9e2d9;
            overflow-wrap: anywhere;
            font-size: 14px;
            line-height: 1.5;
        }

        .message .ic { margin-top: 2px; font-size: 17px; }

        .message-return {
            background: #fff8e8;
            color: #77571b;
            border-color: #f0dfb5;
        }

        /* ---------- Stats ---------- */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--teal-light);
            color: var(--teal-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .stat strong {
            display: block;
            font-size: 24px;
            line-height: 1.1;
            letter-spacing: -0.5px;
        }

        .stat span {
            font-size: 12.5px;
            color: var(--muted);
        }

        /* ---------- Quick links ---------- */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 40px;
        }

        .quick-card {
            position: relative;
            background: var(--white);
            padding: 22px;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.18s, border-color 0.18s, box-shadow 0.18s;
        }

        .quick-card:hover {
            transform: translateY(-3px);
            border-color: var(--teal-dark);
            box-shadow: var(--shadow-md);
        }

        .quick-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--teal-light);
            color: var(--teal-dark);
            font-size: 22px;
            margin-bottom: 14px;
            transition: background 0.18s, color 0.18s;
        }

        .quick-card:hover .quick-icon {
            background: var(--teal-dark);
            color: #fff;
        }

        .quick-card h3 { font-size: 16px; margin: 0 0 7px; }

        .quick-card p {
            font-size: 13px;
            line-height: 1.6;
            color: var(--muted);
            margin: 0;
            padding-right: 22px;
        }

        .quick-arrow {
            position: absolute;
            right: 20px;
            bottom: 22px;
            color: var(--teal-dark);
            font-size: 18px;
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity 0.18s, transform 0.18s;
        }

        .quick-card:hover .quick-arrow { opacity: 1; transform: none; }

        /* ---------- Section ---------- */
        #daftar-alat { scroll-margin-top: 90px; }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .section-heading h2 {
            margin: 0 0 6px;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        .section-heading p {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        .result-count {
            font-size: 13px;
            color: var(--teal-dark);
            background: var(--teal-light);
            padding: 7px 13px;
            border-radius: 999px;
            font-weight: 600;
        }

        /* ---------- Filters ---------- */
        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 22px;
            padding: 12px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-wrap .ic {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 17px;
            pointer-events: none;
        }

        .filters input, .filters select {
            padding: 12px 14px;
            background: var(--cream);
            border: 1px solid transparent;
            border-radius: 10px;
            outline: none;
            color: var(--text);
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        }

        .filters input { width: 100%; padding-left: 42px; }
        .filters select { min-width: 190px; cursor: pointer; }

        .filters input:focus, .filters select:focus {
            background: #fff;
            border-color: var(--teal-dark);
            box-shadow: 0 0 0 3px rgba(15, 107, 100, 0.12);
        }

        /* ---------- Tool cards ---------- */
        .tool-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .tool-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            min-width: 0;
            box-shadow: var(--shadow-sm);
            transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
        }

        .tool-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: #c9e2d9;
        }

        .tool-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 16px;
        }

        .tool-category {
            font-size: 12px;
            font-weight: 600;
            color: var(--teal-dark);
            background: var(--teal-light);
            border-radius: 999px;
            padding: 6px 11px;
            overflow-wrap: anywhere;
        }

        .tool-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--green);
            background: var(--green-light);
            border-radius: 999px;
            padding: 6px 11px;
            white-space: nowrap;
        }

        .tool-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .tool-status.unavailable {
            color: var(--red);
            background: var(--red-light);
        }

        .tool-card h3 {
            font-size: 18px;
            margin: 0 0 6px;
            letter-spacing: -0.2px;
            overflow-wrap: anywhere;
        }

        .tool-code {
            display: inline-block;
            align-self: flex-start;
            color: var(--muted);
            font-family: ui-monospace, "SF Mono", Consolas, monospace;
            font-size: 12px;
            background: var(--cream);
            padding: 3px 8px;
            border-radius: 6px;
            margin-bottom: 14px;
        }

        .tool-description {
            color: #65736e;
            font-size: 13.5px;
            line-height: 1.65;
            margin: 0 0 18px;
            overflow-wrap: anywhere;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tool-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: auto;
            margin-bottom: 16px;
        }

        .meta-box {
            background: var(--cream);
            border-radius: 10px;
            padding: 10px 12px;
            min-width: 0;
        }

        .meta-box span {
            display: block;
            font-size: 11.5px;
            color: var(--muted);
            margin-bottom: 3px;
        }

        .meta-box strong {
            font-size: 14px;
            text-transform: capitalize;
            overflow-wrap: anywhere;
        }

        .btn-primary, .btn-disabled {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--teal-dark);
            color: white;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-primary .ic { transition: transform 0.15s; }
        .btn-primary:hover { background: var(--teal-darker); }
        .btn-primary:hover .ic { transform: translateX(3px); }

        .btn-disabled {
            background: #eceeea;
            color: #818982;
            cursor: not-allowed;
        }

        .empty-state {
            grid-column: 1 / -1;
            background: white;
            padding: 48px 20px;
            text-align: center;
            border-radius: 16px;
            border: 1px dashed #cfd6d0;
            color: var(--muted);
            line-height: 1.7;
        }

        .empty-state .ic {
            display: block;
            margin: 0 auto 10px;
            font-size: 34px;
            color: #b4c2bc;
        }

        .footer {
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            padding: 26px 15px;
            border-top: 1px solid var(--cream-dark);
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 900px) {
            .tool-grid, .quick-links {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .navbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

            .nav-right { justify-content: flex-start; }
            .user-chip { margin-left: 0; }

            .container { padding: 22px 15px 38px; }
            .welcome { padding: 28px 22px; border-radius: 16px; }
            .welcome h1 { font-size: 25px; }

            .stats, .quick-links, .tool-grid { grid-template-columns: 1fr; }

            .filters select { width: 100%; min-width: 0; }
        }
    </style>
</head>
<body>

<header class="navbar">
    <a href="v_homepeminjam.php" class="brand">
        <div class="brand-mark">
            <svg class="ic" viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.4 2.4-2.1-.6-.6-2.1z"/></svg>
        </div>
        <div>pinjam<span>.in</span></div>
    </a>

    <nav class="nav-right">
        <a class="nav-link" href="#daftar-alat">Daftar Alat</a>

        <a class="nav-link"
           href="../Controller/c_pengajuan.php?aksi=riwayat">
            Riwayat Peminjaman
        </a>

        <div class="user-chip">
            <div class="avatar"><?= h_home_peminjam($inisial) ?></div>
            <a class="logout" href="../Controller/c_logout.php">
                Keluar
                <svg class="ic" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
            </a>
        </div>
    </nav>
</header>

<main class="container">

    <section class="welcome">
        <div class="welcome-inner">
            <small>DASHBOARD PEMINJAM</small>
            <h1>Halo, <?= h_home_peminjam($namaUser) ?>! 👋</h1>
            <p>
                Selamat datang di Pinjam.in. Cari alat yang anda butuhkan,
                ajukan peminjaman, dan ajukan pengembalian alat melalui dashboard ini.
            </p>

            <div class="hero-actions">
                <a class="hero-btn light" href="#daftar-alat">
                    Cari Alat
                    <svg class="ic" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
                <a class="hero-btn ghost"
                   href="../Controller/c_pengajuan.php?aksi=riwayat">
                    Lihat Riwayat
                </a>
            </div>
        </div>
    </section>

    <?php if ($pesanPengajuan !== ""): ?>
        <div class="message">
            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <div><?= h_home_peminjam($pesanPengajuan) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($pesanPengembalian !== ""): ?>
        <div class="message message-return">
            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
            <div><?= h_home_peminjam($pesanPengembalian) ?></div>
        </div>
    <?php endif; ?>

    <section class="stats">
        <div class="stat">
            <div class="stat-icon">
                <svg class="ic" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/></svg>
            </div>
            <div>
                <strong><?= $totalAlat ?></strong>
                <span>Total alat terdaftar</span>
            </div>
        </div>

        <div class="stat">
            <div class="stat-icon">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
            <div>
                <strong><?= $totalTersedia ?></strong>
                <span>Siap dipinjam</span>
            </div>
        </div>

        <div class="stat">
            <div class="stat-icon">
                <svg class="ic" viewBox="0 0 24 24"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><path d="M7 7h.01"/></svg>
            </div>
            <div>
                <strong><?= $totalKategori ?></strong>
                <span>Kategori alat</span>
            </div>
        </div>
    </section>

    <section class="quick-links">
        <a class="quick-card" href="#daftar-alat">
            <div class="quick-icon">
                <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </div>
            <h3>Daftar Alat</h3>
            <p>Lihat alat yang tersedia dan ajukan peminjaman sesuai kebutuhan.</p>
            <svg class="ic quick-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>

        <a class="quick-card"
           href="../Controller/c_pengajuan.php?aksi=riwayat">
            <div class="quick-icon">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <h3>Riwayat Peminjaman</h3>
            <p>Pantau status pengajuan peminjaman alat yang sudah anda kirim.</p>
            <svg class="ic quick-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>

        <a class="quick-card"
           href="../Controller/c_pengajuan_pengembalian.php?aksi=form">
            <div class="quick-icon">
                <svg class="ic" viewBox="0 0 24 24"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
            </div>
            <h3>Ajukan Pengembalian</h3>
            <p>Kirim pengajuan pengembalian untuk alat yang masih anda pinjam.</p>
            <svg class="ic quick-arrow" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
    </section>

    <section id="daftar-alat">
        <div class="section-heading">
            <div>
                <h2>Daftar Alat</h2>
                <p>Pilih alat yang tersedia untuk mengajukan peminjaman.</p>
            </div>
            <div class="result-count" id="hitungAlat"><?= $totalAlat ?> alat</div>
        </div>

        <div class="filters">
            <div class="search-wrap">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" id="cariAlat"
                       placeholder="Cari nama atau kode alat..."
                       aria-label="Cari alat">
            </div>

            <select id="filterKategori" aria-label="Filter kategori">
                <option value="">Semua kategori</option>

                <?php foreach ($semuaKategori as $kategori): ?>
                    <option value="<?= h_home_peminjam(
                        strtolower($kategori["nama_kategori"])
                    ) ?>">
                        <?= h_home_peminjam($kategori["nama_kategori"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="tool-grid" id="daftarAlat">
            <?php
            $jumlahTampil = 0;

            if (!empty($semuaAlat)):
                foreach ($semuaAlat as $alat):
                    $namaAlat = $alat["nama_alat"] ?? "Alat";
                    $kodeAlat = $alat["kode_alat"] ?? "";
                    $namaKategori = $alat["nama_kategori"] ?? "Lainnya";
                    $jumlahStok = (int) ($alat["jumlah"] ?? 0);
                    $kondisiAlat = $alat["kondisi"] ?? "";
                    $deskripsi = $alat["deskripsi"] ?? "";

                    $tersedia = alat_tersedia_home($alat);

                    $jumlahTampil++;
            ?>
                <article class="tool-card"
                    data-nama="<?= h_home_peminjam(strtolower($namaAlat)) ?>"
                    data-kode="<?= h_home_peminjam(strtolower($kodeAlat)) ?>"
                    data-kategori="<?= h_home_peminjam(strtolower($namaKategori)) ?>">

                    <div class="tool-top">
                        <span class="tool-category">
                            <?= h_home_peminjam($namaKategori) ?>
                        </span>

                        <span class="tool-status <?= $tersedia ? "" : "unavailable" ?>">
                            <?= $tersedia ? "Tersedia" : "Tidak tersedia" ?>
                        </span>
                    </div>

                    <h3><?= h_home_peminjam($namaAlat) ?></h3>

                    <div class="tool-code">
                        <?= h_home_peminjam($kodeAlat ?: "-") ?>
                    </div>

                    <p class="tool-description">
                        <?= h_home_peminjam(
                            $deskripsi !== "" ? $deskripsi : "Tidak ada deskripsi alat."
                        ) ?>
                    </p>

                    <div class="tool-meta">
                        <div class="meta-box">
                            <span>Stok tersedia</span>
                            <strong><?= $jumlahStok ?> unit</strong>
                        </div>

                        <div class="meta-box">
                            <span>Kondisi</span>
                            <strong>
                                <?= h_home_peminjam(
                                    str_replace("_", " ", $kondisiAlat ?: "-")
                                ) ?>
                            </strong>
                        </div>
                    </div>

                    <?php if ($tersedia): ?>
                        <a class="btn-primary"
                           href="../Controller/c_pengajuan.php?aksi=form&id_alat=<?= (int) $alat["id_alat"] ?>">
                            Ajukan Peminjaman
                            <svg class="ic" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    <?php else: ?>
                        <button class="btn-disabled" disabled>
                            Tidak Tersedia
                        </button>
                    <?php endif; ?>
                </article>
            <?php
                endforeach;
            endif;
            ?>

            <div class="empty-state" id="alatTidakDitemukan" style="display:none;">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
                Alat yang lo cari tidak ditemukan. Coba kata kunci atau kategori lain.
            </div>

            <?php if ($jumlahTampil === 0): ?>
                <div class="empty-state">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/></svg>
                    Belum ada data alat yang tersedia di sistem.
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    const inputCari = document.getElementById("cariAlat");
    const filterKategori = document.getElementById("filterKategori");
    const kartuAlat = document.querySelectorAll(".tool-card");
    const pesanKosong = document.getElementById("alatTidakDitemukan");
    const hitungAlat = document.getElementById("hitungAlat");

    function filterAlat() {
        const kataKunci = inputCari.value.trim().toLowerCase();
        const kategoriPilihan = filterKategori.value.toLowerCase();
        let jumlahCocok = 0;

        kartuAlat.forEach(function(kartu) {
            const nama = kartu.dataset.nama || "";
            const kode = kartu.dataset.kode || "";
            const kategori = kartu.dataset.kategori || "";

            const cocokCari =
                nama.includes(kataKunci) || kode.includes(kataKunci);

            const cocokKategori =
                kategoriPilihan === "" || kategori === kategoriPilihan;

            const tampil = cocokCari && cocokKategori;
            kartu.style.display = tampil ? "flex" : "none";

            if (tampil) jumlahCocok++;
        });

        if (pesanKosong) {
            pesanKosong.style.display =
                (kartuAlat.length > 0 && jumlahCocok === 0) ? "block" : "none";
        }

        if (hitungAlat) {
            hitungAlat.textContent = jumlahCocok + " alat";
        }
    }

    inputCari.addEventListener("input", filterAlat);
    filterKategori.addEventListener("change", filterAlat);
</script>

</body>
</html>