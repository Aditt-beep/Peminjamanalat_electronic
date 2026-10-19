
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Model/m_peminjaman.php";

if (
    !isset($_SESSION["data"]) ||
    ($_SESSION["data"]["role"] ?? "") !== "admin"
) {
    header("Location: v_login.php");
    exit;
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: v_peminjaman.php");
    exit;
}

$model = new m_peminjaman();
$peminjaman = $model->get_by_id($id);

if (!$peminjaman) {
    header("Location: v_peminjaman.php");
    exit;
}

$daftar_user = $model->daftar_user();
$idUserSekarang = (int) $peminjaman["id_user"];
$userDitemukan = false;

foreach ($daftar_user as $user) {
    if ((int) $user["id_user"] === $idUserSekarang) {
        $userDitemukan = true;
        break;
    }
}

if (!$userDitemukan) {
    $daftar_user[] = [
        "id_user" => $idUserSekarang,
        "nama" => "User ID " . $idUserSekarang,
        "username" => ""
    ];
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman - pinjam.in</title>

    <style>
        :root {
            --cream: #f4f1ea;
            --cream-deep: #efebe1;
            --teal-dark: #0f6b64;
            --teal: #1a7a72;
            --teal-deep: #0a4a45;
            --ink: #1c2b29;
            --muted: #82918f;
            --border: #e4e0d6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--cream-deep);
            color: var(--ink);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 286px;
            min-height: 100vh;
            padding: 20px 19px;
            background: #0b4945;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 8px 65px;
            padding-top: 0;
            font-size: 20px;
            font-weight: 800;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #197c73;
            border-radius: 11px;
            flex-shrink: 0;
        }

        .brand-icon svg {
            width: 23px;
            height: 23px;
        }

        .brand-name {
            line-height: 19px;
            white-space: nowrap;
        }

        .brand small {
            display: block;
            color: #91c7c1;
            font-size: 10px;
            letter-spacing: 1.1px;
            line-height: 12px;
        }

        .nav-section {
            margin-bottom: 25px;
        }

        .nav-label {
            margin: 0 13px 12px;
            color: #83b8b2;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            min-height: 46px;
            padding: 11px 14px;
            margin-bottom: 5px;
            border-radius: 10px;
            color: #e6f0ee;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background .2s;
        }

        .nav-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            stroke: #b6d4d0;
        }

        .nav-item:hover {
            background: rgba(25, 124, 115, .55);
        }

        .nav-item.active {
            background: #197c73;
            color: #ffffff;
        }

        .nav-item.active svg {
            stroke: #ffffff;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        .sidebar-bottom .nav-item {
            color: #f2d9d7;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 42px 45px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .page-header h1 {
            margin: 0 0 7px;
            font-size: 26px;
            font-weight: 800;
            color: #172b2a;
        }

        .page-header p {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 17px;
            border-radius: 9px;
            background: #e2eeeb;
            color: var(--teal-dark);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .back-link:hover {
            background: #d4e6e2;
        }

        .panel {
            width: 100%;
            padding: 28px 25px;
            background: #f5f2eb;
            border: 1px solid #e5e0d6;
            border-radius: 17px;
        }

        .panel h2 {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 800;
        }

        .panel-description {
            margin: 0 0 28px;
            color: var(--muted);
            font-size: 13px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 22px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            font-size: 13px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #e1ddd3;
            border-radius: 9px;
            outline: none;
            background: #fffefa;
            color: #203331;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(15, 107, 100, .09);
        }

        .form-group textarea {
            min-height: 105px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 5px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 12px 19px;
            border: none;
            border-radius: 9px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-save {
            background: #0f6b64;
            color: #ffffff;
        }

        .btn-save:hover {
            background: #0a4a45;
        }

        .btn-cancel {
            background: #e6e2d9;
            color: #344744;
        }

        .btn-cancel:hover {
            background: #dcd7cd;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 250px;
            }

            .main {
                padding: 28px 22px;
            }
        }

        @media (max-width: 650px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .brand {
                margin-bottom: 35px;
            }

            .sidebar-bottom {
                margin-top: 10px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .main {
                padding: 24px 16px;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-icon">
                <svg viewBox="0 0 24 24" fill="white" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1.3"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.3"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.3"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.3"/>
                </svg>
            </span>
            <span class="brand-name">
                pinjam.in
                <small>ADMIN PANEL</small>
            </span>
        </div>

        <div class="nav-section">
            <div class="nav-label">MENU UTAMA</div>

            <a class="nav-item" href="v_homeadmin.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">KELOLA DATA</div>

            <a class="nav-item" href="v_user.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4.5 21a7.5 7.5 0 0 1 15 0"/>
                </svg>
                Data User
            </a>

            <a class="nav-item" href="v_alat.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a5 5 0 0 0-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 0 0 6.4-6.4L14 13l-3-3z"/>
                    <path d="m16 5 3-3 3 3-3 3"/>
                </svg>
                Data Alat
            </a>

            <a class="nav-item" href="v_kategori.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.5 13.5 13 21l-10-10V3h8z"/>
                    <circle cx="7.5" cy="7.5" r="1"/>
                </svg>
                Kategori Alat
            </a>

            <a class="nav-item active" href="v_peminjaman.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="5" width="14" height="17" rx="2"/>
                    <path d="M9 3h6v4H9z"/>
                </svg>
                Data Peminjaman
            </a>

            <a class="nav-item" href="v_pengembalian.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 3-6.7L3 8"/>
                    <path d="M3 3v5h5"/>
                </svg>
                Data Pengembalian
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">LAINNYA</div>

            <a class="nav-item" href="v_log_aktivitas.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
                Log Aktivitas
            </a>
        </div>

        <div class="sidebar-bottom">
            <a class="nav-item" href="../Controller/c_logout.php">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 17l5-5-5-5"/>
                    <path d="M15 12H3"/>
                    <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                </svg>
                Keluar
            </a>
        </div>
    </aside>

    <main class="main">
        <header class="page-header">
            <div>
                <h1>Edit Peminjaman</h1>
                <p>Kelola dan perbarui data peminjaman yang tersedia.</p>
            </div>

            <a href="v_peminjaman.php" class="back-link">← Kembali</a>
        </header>

        <section class="panel">
            <h2>Form Edit Peminjaman</h2>
            <p class="panel-description">Perbarui informasi peminjaman pada formulir berikut.</p>

            <form action="../Controller/c_peminjaman.php" method="POST">
                <input
                    type="hidden"
                    name="id_peminjaman"
                    value="<?= (int) $peminjaman["id_peminjaman"] ?>"
                >

                <div class="form-group">
                    <label for="id_user">Nama Peminjam</label>
                    <select name="id_user" id="id_user" required>
                        <?php foreach ($daftar_user as $user): ?>
                            <option
                                value="<?= (int) $user["id_user"] ?>"
                                <?= (int) $user["id_user"] === $idUserSekarang ? "selected" : "" ?>
                            >
                                <?= e(($user["nama"] ?? "") ?: ($user["username"] ?? "User")) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal_pengajuan">Tanggal Pengajuan</label>
                        <input
                            type="date"
                            id="tanggal_pengajuan"
                            name="tanggal_pengajuan"
                            value="<?= e($peminjaman["tanggal_pengajuan"] ?? "") ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tanggal_pinjam">Tanggal Peminjaman</label>
                        <input
                            type="date"
                            id="tanggal_pinjam"
                            name="tanggal_pinjam"
                            value="<?= e($peminjaman["tanggal_pinjam"] ?? "") ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tanggal_kembali_rencana">Rencana Tanggal Kembali</label>
                        <input
                            type="date"
                            id="tanggal_kembali_rencana"
                            name="tanggal_kembali_rencana"
                            value="<?= e($peminjaman["tanggal_kembali_rencana"] ?? "") ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Status Peminjaman</label>
                        <select name="status" id="status" required>
                            <?php
                            $statusList = [
                                "menunggu",
                                "disetujui",
                                "ditolak",
                                "selesai"
                            ];
                            ?>

                            <?php foreach ($statusList as $status): ?>
                                <option
                                    value="<?= e($status) ?>"
                                    <?= ($peminjaman["status"] ?? "") === $status ? "selected" : "" ?>
                                >
                                    <?= e(ucfirst($status)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keperluan">Keperluan</label>
                    <textarea
                        name="keperluan"
                        id="keperluan"
                        required
                    ><?= e($peminjaman["keperluan"] ?? "") ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" name="edit" class="btn btn-save">
                        Simpan Perubahan
                    </button>

                    <a href="v_peminjaman.php" class="btn btn-cancel">
                        Batal
                    </a>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
