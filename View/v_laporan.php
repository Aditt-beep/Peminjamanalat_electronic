<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e_lap($value) {
    return htmlspecialchars((string)($value ?? "-"), ENT_QUOTES, "UTF-8");
}

function tgl_lap($value) {
    if (empty($value)) {
        return "-";
    }

    return date("d-m-Y", strtotime($value));
}

function rupiah_lap($value) {
    return "Rp " . number_format((float)$value, 0, ",", ".");
}

$jenis = $jenis ?? "";
$data_laporan = $data_laporan ?? [];

$role = strtolower(trim($_SESSION["data"]["role"] ?? ""));

if ($role === "petugas") {
    $kembali = "../View/v_home_petugas.php";
} elseif ($role === "admin") {
    $kembali = "../View/v_homeadmin.php";
} else {
    $kembali = "../View/v_homepeminjam.php";
}

$judul = $jenis === "peminjaman"
    ? "Laporan Peminjaman"
    : "Laporan Pengembalian";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Laporan - Pinjam.in</title>

<style>
:root {
    --cream: #f4f1ea;
    --cream-deep: #efebe1;
    --teal-dark: #0f6b64;
    --teal: #16877d;
    --text: #263b39;
    --muted: #75817e;
    --white: #ffffff;
    --border: #dedfd7;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 30px;
    background: var(--cream);
    color: var(--text);
    font-family: Arial, sans-serif;
}

.container {
    max-width: 1400px;
    margin: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

h1 {
    margin: 0 0 8px;
    font-size: 28px;
}

.subtitle {
    margin: 0;
    color: var(--muted);
}

.btn {
    display: inline-block;
    padding: 11px 16px;
    border: none;
    border-radius: 8px;
    background: var(--teal-dark);
    color: white;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
}

.btn:hover {
    background: var(--teal);
}

.btn.secondary {
    background: #e4e5dd;
    color: var(--text);
}

.btn.secondary:hover {
    background: #d8dbd1;
}

.pilihan {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 25px;
}

.kartu {
    display: block;
    padding: 24px;
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    text-decoration: none;
    color: var(--text);
    transition: border-color 0.2s;
}

.kartu:hover {
    border-color: var(--teal);
}

.kartu.aktif {
    border: 2px solid var(--teal-dark);
    background: #f5fbf9;
}

.kartu .ikon {
    font-size: 27px;
    margin-bottom: 12px;
}

.kartu h2 {
    margin: 0 0 8px;
    font-size: 19px;
}

.kartu p {
    margin: 0;
    font-size: 14px;
    color: var(--muted);
    line-height: 1.5;
}

.panel {
    padding: 22px;
    margin-bottom: 18px;
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
}

.panel h2 {
    margin: 0 0 8px;
}

.info {
    margin: 0;
    color: var(--muted);
    font-size: 14px;
}

.table-wrap {
    overflow-x: auto;
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}

th, td {
    padding: 13px 12px;
    border-bottom: 1px solid #ecece6;
    text-align: left;
    font-size: 13px;
    vertical-align: top;
}

th {
    background: var(--teal-dark);
    color: white;
    white-space: nowrap;
}

tr:last-child td {
    border-bottom: none;
}

.empty {
    padding: 35px;
    text-align: center;
    color: var(--muted);
}

.actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.print-header {
    display: none;
}

@media (max-width: 650px) {
    body {
        padding: 15px;
    }

    .pilihan {
        grid-template-columns: 1fr;
    }

    h1 {
        font-size: 23px;
    }
}

@media print {
    @page {
        size: landscape;
        margin: 12mm;
    }

    body {
        padding: 0;
        background: white;
        color: black;
    }

    .no-print {
        display: none !important;
    }

    .print-header {
        display: block;
        text-align: center;
        margin-bottom: 20px;
    }

    .table-wrap {
        overflow: visible;
        border: none;
        border-radius: 0;
    }

    table {
        width: 100%;
        min-width: 0;
    }

    th, td {
        padding: 7px 5px;
        border: 1px solid #999;
        color: black;
        font-size: 9px;
    }

    th {
        background: #e8e8e8 !important;
        color: black !important;
        print-color-adjust: exact;
    }

    tr {
        break-inside: avoid;
    }
}
</style>
</head>

<body>
<div class="container">

    <div class="header no-print">
        <div>
            <h1>Cetak Laporan</h1>
            <p class="subtitle">
                Pilih jenis laporan yang ingin ditampilkan dan dicetak.
            </p>
        </div>

        <a class="btn secondary" href="<?= e_lap($kembali) ?>">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="pilihan no-print">

        <a class="kartu <?= $jenis === "peminjaman" ? "aktif" : "" ?>"
           href="../Controller/c_laporan.php?jenis=peminjaman">
            <div class="ikon">📦</div>
            <h2>Laporan Peminjaman</h2>
            <p>
                Lihat data peminjaman, peminjam, alat, jumlah,
                tanggal pinjam, dan status transaksi.
            </p>
        </a>

        <a class="kartu <?= $jenis === "pengembalian" ? "aktif" : "" ?>"
           href="../Controller/c_laporan.php?jenis=pengembalian">
            <div class="ikon">↩️</div>
            <h2>Laporan Pengembalian</h2>
            <p>
                Lihat data pengembalian alat, tanggal kembali,
                kondisi alat, denda, dan status pembayaran.
            </p>
        </a>

    </div>

    <?php if ($jenis === ""): ?>

        <div class="panel no-print">
            <h2>Pilih Jenis Laporan</h2>
            <p class="info">
                Klik salah satu pilihan di atas untuk menampilkan
                data laporan. Setelah data muncul, lo bisa mencetaknya.
            </p>
        </div>

    <?php elseif ($jenis === "peminjaman" || $jenis === "pengembalian"): ?>

        <div class="print-header">
            <h2>PINJAM.IN</h2>
            <h3><?= e_lap($judul) ?></h3>
            <p>Tanggal cetak: <?= date("d-m-Y") ?></p>
        </div>

        <div class="panel">
            <h2><?= e_lap($judul) ?></h2>
            <p class="info">
                Total data: <?= count($data_laporan) ?> baris
            </p>
        </div>

        <div class="actions no-print">
            <button type="button" class="btn" onclick="window.print()">
                🖨 Cetak Laporan
            </button>

            <a class="btn secondary"
               href="../Controller/c_laporan.php">
                Pilih Laporan Lain
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>

                <?php if ($jenis === "peminjaman"): ?>
                    <tr>
                        <th>No.</th>
                        <th>ID Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Kode Alat</th>
                        <th>Nama Alat</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th>No.</th>
                        <th>ID Pengembalian</th>
                        <th>Nama Peminjam</th>
                        <th>Kode Alat</th>
                        <th>Nama Alat</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Kondisi Kembali</th>
                        <th>Denda</th>
                        <th>Status Bayar</th>
                        <th>Keterangan</th>
                    </tr>
                <?php endif; ?>

                </thead>

                <tbody>

                <?php if (empty($data_laporan)): ?>

                    <tr>
                        <td colspan="<?= $jenis === "peminjaman" ? 11 : 12 ?>"
                            class="empty">
                            Belum ada data untuk laporan ini.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($data_laporan as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>

                            <?php if ($jenis === "peminjaman"): ?>

                                <td><?= e_lap($row["id_peminjaman"] ?? "-") ?></td>
                                <td><?= e_lap($row["nama_peminjam"] ?? "-") ?></td>
                                <td><?= e_lap($row["kode_alat"] ?? "-") ?></td>
                                <td><?= e_lap($row["nama_alat"] ?? "-") ?></td>
                                <td><?= e_lap($row["jumlah"] ?? 0) ?></td>
                                <td><?= e_lap(tgl_lap($row["tanggal_pengajuan"] ?? null)) ?></td>
                                <td><?= e_lap(tgl_lap($row["tanggal_pinjam"] ?? null)) ?></td>
                                <td><?= e_lap(tgl_lap($row["tanggal_kembali_rencana"] ?? null)) ?></td>
                                <td><?= e_lap($row["keperluan"] ?? "-") ?></td>
                                <td><?= e_lap(ucfirst($row["status"] ?? "-")) ?></td>

                            <?php else: ?>

                                <td><?= e_lap($row["id_pengembalian"] ?? "-") ?></td>
                                <td><?= e_lap($row["nama_peminjam"] ?? "-") ?></td>
                                <td><?= e_lap($row["kode_alat"] ?? "-") ?></td>
                                <td><?= e_lap($row["nama_alat"] ?? "-") ?></td>
                                <td><?= e_lap($row["jumlah"] ?? 0) ?></td>
                                <td><?= e_lap(tgl_lap($row["tanggal_pinjam"] ?? null)) ?></td>
                                <td><?= e_lap(tgl_lap($row["tanggal_kembali"] ?? null)) ?></td>
                                <td><?= e_lap($row["kondisi_kembali"] ?? "-") ?></td>
                                <td><?= e_lap(rupiah_lap($row["denda"] ?? 0)) ?></td>
                                <td>
                                    <?= e_lap(
                                        ucwords(
                                            str_replace(
                                                "_",
                                                " ",
                                                $row["status_bayar"] ?? "-"
                                            )
                                        )
                                    ) ?>
                                </td>
                                <td><?= e_lap($row["keterangan"] ?? "-") ?></td>

                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="panel">
            <h2>Jenis laporan tidak ditemukan</h2>
            <p class="info">
                Silakan pilih laporan peminjaman atau pengembalian.
            </p>
            <br>
            <a class="btn" href="../Controller/c_laporan.php">
                Kembali ke Pilihan Laporan
            </a>
        </div>

    <?php endif; ?>

</div>
</body>
</html>