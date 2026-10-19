<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pesan = $_SESSION["pesan_pengajuan"] ?? "";
unset($_SESSION["pesan_pengajuan"]);

$daftar = (isset($data_pengajuan) && is_array($data_pengajuan)) ? $data_pengajuan : [];

function kelas_status_riwayat($status)
{
    $s = strtolower(trim((string) $status));

    if (in_array($s, ["menunggu", "pending", "diajukan", "menunggu_persetujuan"], true)) {
        return "warn";
    }
    if (in_array($s, ["disetujui", "approved", "dipinjam", "diterima"], true)) {
        return "ok";
    }
    if (in_array($s, ["ditolak", "rejected", "dibatalkan", "terlambat"], true)) {
        return "bad";
    }
    if (in_array($s, ["selesai", "dikembalikan", "kembali"], true)) {
        return "done";
    }
    return "neutral";
}

function label_status_riwayat($status)
{
    $label = str_replace("_", " ", (string) $status);
    return $label !== "" ? ucfirst($label) : "-";
}

$total = count($daftar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Peminjaman - Pinjam.in</title>
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
            --red: #a33f2e;
            --red-light: #fff0ec;
            --green: #23704b;
            --green-light: #e7f6ec;
            --amber: #77571b;
            --amber-light: #fff8e8;
            --shadow-sm: 0 1px 2px rgba(36, 59, 56, 0.05);
            --shadow-md: 0 10px 28px rgba(15, 107, 100, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            background:
                radial-gradient(900px 400px at 100% -5%, #e9efe6 0%, transparent 60%),
                var(--cream);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
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
            gap: 16px;
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

        .nav-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 14px;
            font-weight: 600;
            color: var(--teal-dark);
            background: var(--teal-light);
            padding: 9px 15px;
            border-radius: 999px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-back:hover { background: var(--teal-dark); color: #fff; }

        /* ---------- Layout ---------- */
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 30px 22px 56px;
        }

        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .page-title small {
            display: inline-block;
            background: var(--teal-light);
            color: var(--teal-dark);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            padding: 6px 12px;
            border-radius: 999px;
        }

        .page-title h1 {
            margin: 12px 0 6px;
            font-size: 28px;
            letter-spacing: -0.5px;
        }

        .page-title p {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--teal-dark);
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.15s;
        }

        .btn-primary:hover { background: var(--teal-darker); }

        /* ---------- Alert ---------- */
        .pesan {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            background: var(--teal-light);
            color: var(--teal-dark);
            border: 1px solid #c9e2d9;
            font-size: 14px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .pesan .ic { margin-top: 2px; font-size: 17px; }

        /* ---------- Toolbar ---------- */
        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding: 12px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
        }

        .search-wrap { position: relative; flex: 1; min-width: 220px; }

        .search-wrap .ic {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 17px;
            pointer-events: none;
        }

        .toolbar input, .toolbar select {
            padding: 12px 14px;
            background: var(--cream);
            border: 1px solid transparent;
            border-radius: 10px;
            outline: none;
            color: var(--text);
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        }

        .toolbar input { width: 100%; padding-left: 42px; }
        .toolbar select { min-width: 180px; cursor: pointer; }

        .toolbar input:focus, .toolbar select:focus {
            background: #fff;
            border-color: var(--teal-dark);
            box-shadow: 0 0 0 3px rgba(15, 107, 100, 0.12);
        }

        .result-count {
            align-self: center;
            font-size: 13px;
            font-weight: 600;
            color: var(--teal-dark);
            background: var(--teal-light);
            padding: 8px 13px;
            border-radius: 999px;
            white-space: nowrap;
        }

        /* ---------- Table ---------- */
        .panel {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: var(--teal-light);
            color: var(--teal-dark);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            text-align: left;
            padding: 14px 16px;
            white-space: nowrap;
        }

        tbody td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            vertical-align: top;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr.baris:hover { background: #faf9f5; }

        .nama-alat { font-weight: 600; overflow-wrap: anywhere; }

        .qty {
            display: inline-block;
            background: var(--cream);
            border-radius: 8px;
            padding: 4px 10px;
            font-weight: 600;
        }

        .tanggal {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            color: #52635e;
        }

        .tanggal .ic { color: var(--muted); font-size: 14px; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 6px 12px;
            white-space: nowrap;
        }

        .badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .badge.warn { color: var(--amber); background: var(--amber-light); }
        .badge.ok { color: var(--green); background: var(--green-light); }
        .badge.bad { color: var(--red); background: var(--red-light); }
        .badge.done { color: var(--teal-dark); background: var(--teal-light); }
        .badge.neutral { color: #5d6b66; background: #eceeea; }

        .catatan {
            color: #65736e;
            line-height: 1.6;
            min-width: 170px;
            overflow-wrap: anywhere;
        }

        .empty-state {
            padding: 54px 20px;
            text-align: center;
            color: var(--muted);
            line-height: 1.7;
        }

        .empty-state .ic {
            display: block;
            margin: 0 auto 10px;
            font-size: 36px;
            color: #b4c2bc;
        }

        .empty-state .btn-primary { margin-top: 14px; }

        .footer {
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            padding: 26px 15px;
            border-top: 1px solid var(--cream-dark);
        }

        /* ---------- Mobile: tabel jadi kartu ---------- */
        @media (max-width: 760px) {
            .container { padding: 22px 14px 40px; }
            .page-title h1 { font-size: 24px; }

            .panel { background: transparent; border: none; box-shadow: none; overflow: visible; }

            table, tbody, tr, td { display: block; width: 100%; }
            thead { display: none; }

            tbody tr.baris {
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 8px 4px;
                margin-bottom: 12px;
                box-shadow: var(--shadow-sm);
            }

            tbody td {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 16px;
                border-bottom: 1px dashed var(--border);
                padding: 11px 14px;
                text-align: right;
            }

            tbody tr.baris td:last-child { border-bottom: none; }

            tbody td::before {
                content: attr(data-label);
                font-size: 12px;
                color: var(--muted);
                font-weight: 600;
                text-align: left;
                flex-shrink: 0;
            }

            .catatan { min-width: 0; }
            tbody tr.baris:hover { background: var(--white); }
        }
    </style>
</head>
<body>

<header class="navbar">
    <a href="../View/v_homepeminjam.php" class="brand">
        <div class="brand-mark">
            <svg class="ic" viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18v3h3l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.4 2.4-2.1-.6-.6-2.1z"/></svg>
        </div>
        <div>pinjam<span>.in</span></div>
    </a>

    <a class="nav-back" href="../View/v_homepeminjam.php">
        <svg class="ic" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        Kembali ke dashboard
    </a>
</header>

<main class="container">

    <div class="page-title">
        <div>
            <small>RIWAYAT</small>
            <h1>Riwayat Pengajuan Saya</h1>
            <p>Pantau status semua pengajuan peminjaman alat yang sudah anda kirim.</p>
        </div>

        <a class="btn-primary" href="../View/v_homepeminjam.php#daftar-alat">
            <svg class="ic" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Ajukan Baru
        </a>
    </div>

    <?php if ($pesan !== ""): ?>
        <div class="pesan">
            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <div><?= htmlspecialchars($pesan) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($daftar)): ?>
        <div class="toolbar">
            <div class="search-wrap">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" id="cariRiwayat"
                       placeholder="Cari nama alat..."
                       aria-label="Cari riwayat">
            </div>

            <select id="filterStatus" aria-label="Filter status">
                <option value="">Semua status</option>
                <?php
                $statusUnik = [];
                foreach ($daftar as $p) {
                    $st = strtolower((string) ($p["status"] ?? ""));
                    if ($st !== "") {
                        $statusUnik[$st] = true;
                    }
                }
                foreach (array_keys($statusUnik) as $st):
                ?>
                    <option value="<?= htmlspecialchars($st) ?>">
                        <?= htmlspecialchars(label_status_riwayat($st)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="result-count" id="hitungRiwayat"><?= $total ?> pengajuan</div>
        </div>
    <?php endif; ?>

    <div class="panel">
        <?php if (empty($daftar)): ?>
            <div class="empty-state" style="background:#fff;border:1px dashed #cfd6d0;border-radius:16px;">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                Lo belum memiliki pengajuan peminjaman.
                <br>
                <a class="btn-primary" href="../View/v_homepeminjam.php#daftar-alat">
                    Cari alat untuk dipinjam
                </a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Alat</th>
                            <th>Jumlah</th>
                            <th>Tanggal pinjam</th>
                            <th>Tanggal kembali</th>
                            <th>Status</th>
                            <th>Catatan petugas</th>
                        </tr>
                    </thead>
                    <tbody id="isiRiwayat">
                        <?php foreach ($daftar as $p): ?>
                            <?php
                            $namaAlat = $p["nama_alat"] ?? "-";
                            $statusRaw = (string) ($p["status"] ?? "");
                            ?>
                            <tr class="baris"
                                data-nama="<?= htmlspecialchars(strtolower($namaAlat)) ?>"
                                data-status="<?= htmlspecialchars(strtolower($statusRaw)) ?>">
                                <td data-label="Alat">
                                    <span class="nama-alat"><?= htmlspecialchars($namaAlat) ?></span>
                                </td>
                                <td data-label="Jumlah">
                                    <span class="qty"><?= (int) ($p["jumlah"] ?? 0) ?></span>
                                </td>
                                <td data-label="Tanggal pinjam">
                                    <span class="tanggal">
                                        <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                        <?= htmlspecialchars($p["tanggal_pinjam"] ?? "-") ?>
                                    </span>
                                </td>
                                <td data-label="Tanggal kembali">
                                    <span class="tanggal">
                                        <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                        <?= htmlspecialchars($p["tanggal_kembali_rencana"] ?? "-") ?>
                                    </span>
                                </td>
                                <td data-label="Status">
                                    <span class="badge <?= kelas_status_riwayat($statusRaw) ?>">
                                        <?= htmlspecialchars(label_status_riwayat($statusRaw)) ?>
                                    </span>
                                </td>
                                <td data-label="Catatan petugas" class="catatan">
                                    <?= nl2br(htmlspecialchars($p["keterangan"] ?? "-")) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="empty-state" id="riwayatKosong" style="display:none;">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
                Tidak ada pengajuan yang cocok dengan pencarian lo.
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    const inputCari = document.getElementById("cariRiwayat");
    const filterStatus = document.getElementById("filterStatus");
    const baris = document.querySelectorAll("#isiRiwayat .baris");
    const kosong = document.getElementById("riwayatKosong");
    const hitung = document.getElementById("hitungRiwayat");

    function filterRiwayat() {
        const kata = inputCari.value.trim().toLowerCase();
        const status = filterStatus.value.toLowerCase();
        let cocok = 0;

        baris.forEach(function (tr) {
            const tampil =
                (tr.dataset.nama || "").includes(kata) &&
                (status === "" || tr.dataset.status === status);

            tr.style.display = tampil ? "" : "none";
            if (tampil) cocok++;
        });

        if (kosong) kosong.style.display = cocok === 0 ? "block" : "none";
        if (hitung) hitung.textContent = cocok + " pengajuan";
    }

    if (inputCari && filterStatus) {
        inputCari.addEventListener("input", filterRiwayat);
        filterStatus.addEventListener("change", filterRiwayat);
    }
</script>

</body>
</html>