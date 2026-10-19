<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pesan = $_SESSION["pesan_pengajuan"] ?? "";
unset($_SESSION["pesan_pengajuan"]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengajuan Peminjaman - Petugas</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    /* Warna inti */
    --cream: #f4f1ea;
    --white: #ffffff;
    --teal: #0f6b64;
    --teal-dark: #0a514c;
    --text: #24332f;
    --muted: #73807b;
    --border: #e3e7e2;

    /* Turunan */
    --teal-soft: #e9f2ef;
    --amber: #d99a1c;
    --amber-soft: #fff0c2;
    --green: #2f8f57;
    --green-soft: #d9f1df;
    --red: #b64242;
    --red-dark: #983535;
    --red-soft: #f8dddd;
    --blue: #4f65b3;
    --blue-soft: #e3e8f8;

    --radius: 16px;
    --shadow: 0 1px 2px rgba(37, 59, 55, .05), 0 8px 24px -12px rgba(37, 59, 55, .12);
}

* { box-sizing: border-box; }

body {
    margin: 0;
    padding: 28px 20px 40px;
    font-family: "Plus Jakarta Sans", "Segoe UI", Arial, sans-serif;
    background: var(--cream);
    color: var(--text);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

:focus-visible {
    outline: 3px solid var(--teal);
    outline-offset: 2px;
    border-radius: 6px;
}

.container {
    max-width: 1100px;
    margin: auto;
}

/* ---------- Judul halaman ---------- */
.back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 18px;
    padding: 8px 14px 8px 10px;
    background: white;
    border: 1px solid var(--border);
    border-radius: 999px;
    color: var(--teal);
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s, border-color .15s;
}

.back:hover {
    background: var(--teal-soft);
    border-color: var(--teal);
}

.back svg { width: 17px; height: 17px; }

.page-head {
    position: relative;
    overflow: hidden;
    margin-bottom: 22px;
    padding: 30px 32px;
    background:
        radial-gradient(circle at 92% 8%, rgba(255, 255, 255, .13) 0, rgba(255, 255, 255, 0) 38%),
        linear-gradient(135deg, var(--teal) 0%, var(--teal-dark) 100%);
    color: white;
    border-radius: 22px;
    box-shadow: 0 18px 40px -22px rgba(10, 81, 76, .8);
}

.page-head::after {
    content: "";
    position: absolute;
    right: -60px;
    bottom: -80px;
    width: 220px;
    height: 220px;
    border: 32px solid rgba(255, 255, 255, .07);
    border-radius: 50%;
    pointer-events: none;
}

.page-head h1 {
    position: relative;
    z-index: 1;
    margin: 0 0 8px;
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 800;
    letter-spacing: -.025em;
    line-height: 1.15;
}

.page-head p {
    position: relative;
    z-index: 1;
    margin: 0;
    max-width: 520px;
    color: #d6ece7;
    font-size: 15px;
}

/* ---------- Pesan ---------- */
.pesan {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 20px;
    padding: 14px 18px;
    background: #e3f2e8;
    color: #205b3b;
    border: 1px solid #c6e4d0;
    border-radius: 12px;
    font-weight: 500;
}

.pesan svg { width: 20px; height: 20px; flex: none; }

/* ---------- Panel & tabel ---------- */
.panel {
    padding: 8px 20px 14px;
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 16px 12px;
    border-bottom: 1px solid var(--border);
    text-align: left;
    vertical-align: top;
}

tbody tr:last-child td { border-bottom: none; }

th {
    color: var(--muted);
    font-size: 13px;
    font-weight: 600;
    background: transparent;
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
}

.id-cell {
    color: var(--teal);
    font-weight: 700;
    white-space: nowrap;
}

/* Peminjam */
.person {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 150px;
}

.avatar {
    display: grid;
    place-items: center;
    flex: none;
    width: 38px;
    height: 38px;
    background: var(--teal-soft);
    color: var(--teal-dark);
    border-radius: 50%;
    font-size: 14px;
    font-weight: 700;
}

.person strong { display: block; font-weight: 600; }
.person small { color: var(--muted); font-size: 13px; }

/* Tanggal */
.tgl {
    min-width: 190px;
    font-size: 14px;
}

.tgl div {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    padding: 2px 0;
}

.tgl span { color: var(--muted); }
.tgl b { font-weight: 600; white-space: nowrap; }

.keperluan {
    min-width: 170px;
    font-size: 14px;
}

/* Status */
.status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    background: var(--amber-soft);
    color: #7a5608;
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 700;
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

.status-disetujui { background: var(--green-soft); color: #1f5f3a; }
.status-disetujui::before { background: var(--green); }

.status-ditolak { background: var(--red-soft); color: #8a2f2c; }
.status-ditolak::before { background: var(--red); }

.status-selesai { background: var(--blue-soft); color: #33437f; }
.status-selesai::before { background: var(--blue); }

/* Form proses */
.proses { min-width: 220px; }

textarea {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px 12px;
    background: #fafbfa;
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--text);
    font: inherit;
    font-size: 14px;
    resize: vertical;
    transition: border-color .15s, background .15s, box-shadow .15s;
}

textarea::placeholder { color: #9aa5a0; }

textarea:focus {
    outline: none;
    background: white;
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(15, 107, 100, .15);
}

.aksi {
    display: flex;
    gap: 8px;
}

button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex: 1;
    padding: 10px 12px;
    border: 0;
    border-radius: 10px;
    color: white;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s, transform .15s, box-shadow .15s;
}

button:active { transform: translateY(1px); }

button svg { width: 16px; height: 16px; flex: none; }

.setuju { background: var(--teal); }
.setuju:hover {
    background: var(--teal-dark);
    box-shadow: 0 6px 16px -8px rgba(10, 81, 76, .8);
}

.tolak {
    background: white;
    color: var(--red);
    box-shadow: inset 0 0 0 1.5px var(--red);
}
.tolak:hover {
    background: var(--red);
    color: white;
}

.catatan {
    max-width: 260px;
    color: var(--muted);
    font-size: 14px;
}

/* Kosong */
.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 44px 20px;
    text-align: center;
    color: var(--muted);
}

.empty-icon {
    display: grid;
    place-items: center;
    width: 64px;
    height: 64px;
    margin-bottom: 14px;
    background: var(--teal-soft);
    color: var(--teal);
    border-radius: 50%;
}

.empty-icon svg { width: 30px; height: 30px; }
.empty h3 { margin: 0 0 4px; color: var(--text); }
.empty p { margin: 0; }

/* ---------- Responsif: tabel jadi kartu ---------- */
@media (max-width: 800px) {
    body { padding: 18px 14px 32px; }
    .page-head { padding: 24px 22px; border-radius: 18px; }
    .panel { padding: 6px 14px; }

    table, thead, tbody, tr, td { display: block; width: 100%; }
    thead { position: absolute; left: -9999px; }

    tbody tr {
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }

    tbody tr:last-child { border-bottom: none; }

    td {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 8px 0;
        border: none;
    }

    td::before {
        content: attr(data-label);
        flex: none;
        padding-top: 2px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 600;
    }

    td.full { flex-direction: column; }
    td.full::before { padding-top: 0; }

    .tgl, .keperluan, .proses, .person { min-width: 0; }
    .tgl, .keperluan { flex: 1; text-align: right; }
    .keperluan { text-align: right; }
    .proses, .catatan { width: 100%; max-width: none; }
}

@media (prefers-reduced-motion: reduce) {
    * { transition: none !important; }
}
</style>
</head>
<body>
<div class="container">

    <a class="back" href="../View/v_home_petugas.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
        Kembali ke dashboard
    </a>

    <header class="page-head">
        <h1>Pengajuan Peminjaman</h1>
        <p>Periksa data pengajuan sebelum menyetujui atau menolaknya.</p>
    </header>

    <?php if ($pesan !== ""): ?>
        <p class="pesan" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>
            <?= htmlspecialchars($pesan) ?>
        </p>
    <?php endif; ?>

    <div class="panel">
        <?php if (empty($data_pengajuan)): ?>
            <div class="empty">
                <div class="empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13h4l2 3h4l2-3h4"/><path d="M5 5h14l2 8v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-6l2-8Z"/></svg>
                </div>
                <h3>Belum ada pengajuan</h3>
                <p>Belum ada pengajuan peminjaman.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Peminjam</th>
                    <th>Tanggal</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                    <th>Proses petugas</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data_pengajuan as $p):
                $statusClass = "";

                if ($p["status"] === "disetujui") {
                    $statusClass = "status-disetujui";
                } elseif ($p["status"] === "ditolak") {
                    $statusClass = "status-ditolak";
                } elseif ($p["status"] === "selesai") {
                    $statusClass = "status-selesai";
                }

                $namaPeminjam = $p["nama"] ?? $p["username"] ?? "-";
                $inisial = strtoupper(substr($namaPeminjam, 0, 1));
            ?>
                <tr>
                    <td data-label="ID" class="id-cell">
                        #<?= (int)$p["id_peminjaman"] ?>
                    </td>

                    <td data-label="Peminjam">
                        <div class="person">
                            <span class="avatar" aria-hidden="true">
                                <?= htmlspecialchars($inisial) ?>
                            </span>
                            <div>
                                <strong><?= htmlspecialchars($namaPeminjam) ?></strong>
                                <small><?= htmlspecialchars($p["username"] ?? "") ?></small>
                            </div>
                        </div>
                    </td>

                    <td data-label="Tanggal">
                        <div class="tgl">
                            <div><span>Pengajuan</span> <b><?= htmlspecialchars($p["tanggal_pengajuan"]) ?></b></div>
                            <div><span>Pinjam</span> <b><?= htmlspecialchars($p["tanggal_pinjam"]) ?></b></div>
                            <div><span>Kembali</span> <b><?= htmlspecialchars($p["tanggal_kembali_rencana"]) ?></b></div>
                        </div>
                    </td>

                    <td data-label="Keperluan">
                        <div class="keperluan">
                            <?= nl2br(htmlspecialchars($p["keperluan"] ?? "-")) ?>
                        </div>
                    </td>

                    <td data-label="Status">
                        <span class="status <?= $statusClass ?>">
                            <?= htmlspecialchars($p["status"]) ?>
                        </span>
                    </td>

                    <td data-label="Proses petugas" class="full">
                        <?php if ($p["status"] === "menunggu"): ?>
                            <form class="proses" method="POST"
                                  action="../Controller/c_pengajuan.php?aksi=proses">
                                <input type="hidden" name="id_peminjaman"
                                       value="<?= (int)$p["id_peminjaman"] ?>">

                                <textarea name="keterangan" rows="2"
                                          maxlength="1000"
                                          aria-label="Catatan petugas"
                                          placeholder="Catatan petugas (opsional)"></textarea>

                                <div class="aksi">
                                    <button class="setuju" type="submit"
                                            name="pilihan" value="setujui"
                                            onclick="return confirm('Setujui pengajuan ini?')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
                                        Setujui
                                    </button>

                                    <button class="tolak" type="submit"
                                            name="pilihan" value="tolak"
                                            onclick="return confirm('Tolak pengajuan ini?')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <span class="catatan">
                                <?= htmlspecialchars($p["keterangan"] ?: "-") ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>