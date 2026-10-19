
<?php
session_start();

if (
    !isset($_SESSION["data"]) ||
    ($_SESSION["data"]["role"] ?? "") !== "admin"
) {
    header("Location: v_login.php");
    exit;
}

require_once __DIR__ . "/../Model/m_peminjaman.php";

$model_peminjaman = new m_peminjaman();
$daftar_user = $model_peminjaman->daftar_user();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pinjam.in — Tambah Peminjaman</title>

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
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--cream-deep);
            color: var(--ink);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 230px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            padding: 22px 16px;
            background: var(--teal-deep);
            color: white;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 6px;
            margin-bottom: 32px;
        }

        .logo {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--teal);
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 800;
        }

        .brand-tag {
            color: #8fc9c2;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav-group-label {
            margin: 18px 10px 8px;
            color: #7fa7a1;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .nav-item {
            display: block;
            padding: 11px 12px;
            margin-bottom: 3px;
            border-radius: 8px;
            color: #dce9e7;
            font-size: 13.5px;
            font-weight: 600;
        }

        .nav-item:hover {
            background: rgba(255,255,255,.08);
        }

        .nav-item.active {
            background: var(--teal);
            color: white;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,.12);
        }

        .logout-btn {
            display: block;
            padding: 10px 12px;
            color: #f3c9c9;
            font-size: 13.5px;
            font-weight: 700;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 32px 40px;
        }

        .topbar {
            margin-bottom: 25px;
        }

        .topbar h1 {
            margin: 0 0 5px;
            font-size: 24px;
            font-weight: 800;
        }

        .topbar p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .panel {
            width: 100%;
            max-width: 760px;
            padding: 25px;
            border: 1px solid var(--card-border);
            border-radius: 14px;
            background: var(--cream);
        }

        .panel-title {
            margin: 0 0 22px;
            font-size: 16px;
            font-weight: 800;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            display: block;
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            outline: none;
            background: white;
            color: var(--ink);
            font-family: inherit;
            font-size: 13px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--teal);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-action {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 25px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-save {
            background: var(--teal-dark);
            color: white;
        }

        .btn-save:hover {
            background: var(--teal-deep);
        }

        .btn-cancel {
            background: #e4e0d6;
            color: var(--ink);
        }

        .empty-message {
            padding: 12px;
            border-radius: 8px;
            background: #fff4dc;
            color: #72571e;
            font-size: 13px;
        }

        @media (max-width: 800px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .sidebar-footer {
                margin-top: 10px;
            }

            .main {
                padding: 22px 16px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="logo">P</div>
            <div>
                <div class="brand-name">pinjam.in</div>
                <div class="brand-tag">ADMIN PANEL</div>
            </div>
        </div>

        <div class="nav-group-label">Menu Utama</div>
        <a class="nav-item" href="v_homeadmin.php">Dashboard</a>

        <div class="nav-group-label">Kelola Data</div>
        <a class="nav-item" href="v_user.php">Data User</a>
        <a class="nav-item" href="v_alat.php">Data Alat</a>
        <a class="nav-item" href="v_kategori.php">Kategori</a>
        <a class="nav-item active" href="v_peminjaman.php">Data Peminjaman</a>
        <a class="nav-item" href="v_pengembalian.php">Data Pengembalian</a>

        <div class="nav-group-label">Lainnya</div>
        <a class="nav-item" href="v_log.php">Log Aktivitas</a>

        <div class="sidebar-footer">
            <a class="logout-btn" href="../Controller/c_logout.php">
                Keluar
            </a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <h1>Tambah Peminjaman</h1>
            <p>Tambahkan data peminjaman melalui panel admin.</p>
        </div>

        <section class="panel">
            <h2 class="panel-title">Form Tambah Peminjaman</h2>

            <?php if (empty($daftar_user)): ?>
                <div class="empty-message">
                    Belum ada pengguna aktif. Aktifkan atau tambahkan pengguna
                    terlebih dahulu di menu Data User.
                </div>
            <?php else: ?>

            <form action="../Controller/c_peminjaman.php" method="POST">

                <div class="form-group">
                    <label for="id_user">Nama Peminjam</label>
                    <select id="id_user" name="id_user" required>
                        <option value="">-- Pilih Peminjam --</option>
                        <?php foreach ($daftar_user as $user): ?>
                            <option value="<?= (int) $user["id_user"] ?>">
                                <?= htmlspecialchars(
                                    $user["nama"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                                (<?= htmlspecialchars(
                                    $user["username"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                        <label for="tanggal_pinjam">
                            Tanggal Peminjaman
                        </label>
                        <input
                            type="date"
                            id="tanggal_pinjam"
                            name="tanggal_pinjam"
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
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keperluan">Keperluan</label>
                    <textarea
                        id="keperluan"
                        name="keperluan"
                        placeholder="Contoh: Presentasi tugas RPL"
                        required
                    ></textarea>
                </div>

                <div class="form-action">
                    <button
                        type="submit"
                        name="tambah"
                        value="1"
                        class="btn btn-save"
                    >
                        Simpan Peminjaman
                    </button>

                    <a
                        href="v_peminjaman.php"
                        class="btn btn-cancel"
                    >
                        Kembali
                    </a>
                </div>
            </form>

            <?php endif; ?>
        </section>
    </main>

</div>
</body>
</html>
