<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION["data"]) ||
    $_SESSION["data"]["role"] !== "petugas"
) {
    header("Location: v_login.php");
    exit;
}

function e_kembali($nilai) {
    return htmlspecialchars((string)($nilai ?? ""), ENT_QUOTES, "UTF-8");
}

$pesan = $_SESSION["pesan_pengajuan_kembali"] ?? "";
unset($_SESSION["pesan_pengajuan_kembali"]);

$data_pengajuan = $data_pengajuan ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Pengembalian - Petugas</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f1ea;
            color: #263b38;
        }

        .container {
            max-width: 1200px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 27px;
            margin-bottom: 8px;
        }

        .header p {
            color: #71817d;
            font-size: 14px;
        }

        .btn-back {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 8px;
            background: #0f6b64;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .alert {
            padding: 13px 16px;
            background: #e1f3e8;
            color: #22623d;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 4px 18px rgba(30, 60, 50, 0.06);
        }

        .card h2 {
            font-size: 19px;
            margin-bottom: 18px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #e9eeeb;
            font-size: 13px;
            vertical-align: top;
        }

        th {
            background: #f5f8f6;
            color: #526761;
            font-weight: 600;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #fff0cf;
            color: #8b620b;
        }

        .status.selesai {
            background: #dff4e6;
            color: #23663e;
        }

        .status.ditolak {
            background: #fde4e4;
            color: #a52e2e;
        }

        .form-konfirmasi {
            min-width: 200px;
        }

        .form-konfirmasi label {
            display: block;
            margin: 8px 0 5px;
            font-size: 12px;
            color: #526761;
        }

        select, textarea {
            width: 100%;
            padding: 9px;
            border: 1px solid #d7e0dc;
            border-radius: 7px;
            font-family: inherit;
            font-size: 13px;
            background: white;
        }

        textarea {
            resize: vertical;
            min-height: 60px;
        }

        .btn-confirm {
            width: 100%;
            border: none;
            border-radius: 7px;
            padding: 10px;
            margin-top: 10px;
            background: #0f6b64;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-confirm:hover {
            background: #0b554f;
        }

        .empty {
            text-align: center;
            color: #71817d;
            padding: 35px 15px;
        }

        .small-text {
            font-size: 12px;
            color: #71817d;
            margin-top: 5px;
        }

        @media (max-width: 600px) {
            .container {
                margin-top: 22px;
                padding: 0 12px;
            }

            .card {
                padding: 14px;
            }

            .header h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <div>
            <h1>Pengajuan Pengembalian</h1>
            <p>Periksa pengajuan peminjam dan konfirmasi alat yang sudah diterima.</p>
        </div>

        <a href="../View/v_home_petugas.php" class="btn-back">
            Kembali ke Dashboard
        </a>
    </div>

    <?php if ($pesan !== ""): ?>
        <div class="alert">
            <?= e_kembali($pesan) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Daftar Pengajuan</h2>

        <?php if (empty($data_pengajuan)): ?>

            <div class="empty">
                <p>Belum ada pengajuan pengembalian.</p>
            </div>

        <?php else: ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Keterangan Peminjam</th>
                        <th>Status</th>
                        <th>Konfirmasi Petugas</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $no = 1; ?>

                    <?php foreach ($data_pengajuan as $item): ?>
                        <?php
                        $status = $item["status_pengajuan"] ?? "menunggu";

                        $status_label = [
                            "menunggu" => "Menunggu",
                            "diproses" => "Diproses",
                            "diterima" => "Diterima",
                            "ditolak" => "Ditolak"
                        ];

                        $status_tampil = $status_label[$status] ?? ucfirst($status);
                        ?>

                        <tr>
                            <td><?= $no++ ?></td>

                            <td>
                                <strong>
                                    <?= e_kembali($item["nama"] ?? $item["username"] ?? "-") ?>
                                </strong>
                                <div class="small-text">
                                    <?= e_kembali($item["username"] ?? "") ?>
                                </div>
                            </td>

                            <td>
                                <strong><?= e_kembali($item["nama_alat"] ?? "-") ?></strong>
                                <div class="small-text">
                                    Kode: <?= e_kembali($item["kode_alat"] ?? "-") ?>
                                </div>
                            </td>

                            <td><?= e_kembali($item["jumlah"] ?? 0) ?></td>

                            <td>
                                <?= e_kembali($item["tanggal_pengajuan"] ?? "-") ?>
                            </td>

                            <td>
                                <?= nl2br(e_kembali($item["keterangan_pengajuan"] ?? "-")) ?>
                            </td>

                            <td>
                                <span class="status <?= e_kembali($status) ?>">
                                    <?= e_kembali($status_tampil) ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($status === "menunggu"): ?>

                                    <form
                                        class="form-konfirmasi"
                                        method="POST"
                                        action="../Controller/c_pengajuan_pengembalian.php?aksi=konfirmasi"
                                        onsubmit="return confirm('Pastikan alat sudah diterima dan kondisinya sudah diperiksa. Lanjutkan konfirmasi?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id_pengajuan_kembali"
                                            value="<?= e_kembali($item["id_pengajuan_kembali"] ?? "") ?>"
                                        >

                                        <label>Kondisi alat saat kembali</label>
                                        <select name="kondisi_kembali" required>
                                            <option value="">Pilih kondisi</option>
                                            <option value="baik">Baik</option>
                                            <option value="rusak_ringan">Rusak ringan</option>
                                            <option value="rusak_berat">Rusak berat</option>
                                        </select>

                                        <label>Keterangan pemeriksaan</label>
                                        <textarea
                                            name="keterangan"
                                            placeholder="Catatan kondisi alat (opsional)"
                                        ></textarea>

                                        <button type="submit" class="btn-confirm">
                                            Konfirmasi Pengembalian
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <span class="small-text">
                                        Pengajuan sudah diproses.
                                    </span>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>
</div>
</body>
</html>