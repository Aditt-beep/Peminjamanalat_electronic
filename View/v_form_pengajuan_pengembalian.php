
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION["data"]["id_user"]) ||
    strtolower(trim($_SESSION["data"]["role"] ?? "")) !== "peminjam"
) {
    header("Location: v_login.php");
    exit;
}

function e_pengembalian($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$pesan = $_SESSION["pesan_pengajuan_kembali"] ?? "";
unset($_SESSION["pesan_pengajuan_kembali"]);

$nama = $_SESSION["data"]["nama"]
    ?? $_SESSION["data"]["username"]
    ?? "Peminjam";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form Pengembalian - Pinjam.in</title>

    <style>
        :root {
            --cream: #f4f1ea;
            --teal-dark: #0f6b64;
            --teal-light: #e5f4ef;
            --text: #263238;
            --muted: #697575;
            --white: #ffffff;
            --border: #e1e5df;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--cream);
            color: var(--text);
            font-family: Arial, sans-serif;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 5%;
            background: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .brand {
            color: var(--teal-dark);
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .brand span {
            color: var(--text);
        }

        .user-name {
            color: var(--muted);
            font-size: 14px;
        }

        .container {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
            padding: 35px 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--teal-dark);
            font-size: 14px;
            text-decoration: none;
        }

        .page-title {
            margin: 0 0 10px;
            font-size: 28px;
        }

        .description {
            margin: 0 0 25px;
            color: var(--muted);
            line-height: 1.7;
        }

        .form-card {
            padding: 28px;
            border: 1px solid var(--border);
            border-radius: 15px;
            background: var(--white);
        }

        .notice {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 9px;
            background: var(--teal-light);
            color: var(--teal-dark);
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 21px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-size: 14px;
            font-weight: bold;
        }

        select,
        textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: #ffffff;
            color: var(--text);
            font-family: inherit;
            font-size: 14px;
        }

        select:focus,
        textarea:focus {
            outline: 2px solid var(--teal-light);
            border-color: var(--teal-dark);
        }

        textarea {
            min-height: 115px;
            resize: vertical;
        }

        .help-text {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .button-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 18px;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            background: var(--teal-dark);
            color: var(--white);
        }

        .button-secondary {
            background: #efebe1;
            color: var(--text);
        }

        .empty-state {
            padding: 22px;
            border: 1px dashed var(--border);
            border-radius: 10px;
            text-align: center;
        }

        .empty-state p {
            color: var(--muted);
            line-height: 1.7;
        }

        @media (max-width: 520px) {
            .navbar {
                padding: 16px;
            }

            .container {
                padding: 25px 15px;
            }

            .form-card {
                padding: 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .button-row {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <a href="v_homepeminjam.php" class="brand">
            pinjam<span>.in</span>
        </a>

        <span class="user-name">
            Halo, <?= e_pengembalian($nama) ?>
        </span>
    </header>

    <main class="container">

        <a href="../View/v_homepeminjam.php" class="back-link">
            ← Kembali ke dashboard
        </a>

        <h1 class="page-title">Form Pengembalian Alat</h1>

        <p class="description">
            Pilih alat yang ingin lo kembalikan dan kirim pengajuan
            kepada petugas untuk diperiksa dan dikonfirmasi.
        </p>

        <?php if ($pesan !== ""): ?>
            <div class="notice">
                <?= e_pengembalian($pesan) ?>
            </div>
        <?php endif; ?>

        <section class="form-card">

            <?php if (!empty($data_detail)): ?>

                <form
                    action="../Controller/c_pengajuan_pengembalian.php?aksi=simpan"
                    method="POST"
                >

                    <div class="form-group">
                        <label for="id_detail">
                            Pilih alat yang dikembalikan
                        </label>

                        <select
                            name="id_detail"
                            id="id_detail"
                            required
                        >
                            <option value="">-- Pilih alat --</option>

                            <?php foreach ($data_detail as $detail): ?>
                                <option
                                    value="<?= (int) $detail["id_detail"] ?>"
                                >
                                    <?= e_pengembalian($detail["nama_alat"]) ?>
                                    (<?= e_pengembalian($detail["kode_alat"]) ?>)
                                    - Jumlah:
                                    <?= (int) $detail["jumlah"] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <p class="help-text">
                            Daftar hanya menampilkan alat yang masih
                            tercatat dipinjam dan belum memiliki
                            pengembalian.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">
                            Keterangan pengembalian
                        </label>

                        <textarea
                            name="keterangan"
                            id="keterangan"
                            placeholder="Contoh: alat akan dikembalikan dalam kondisi baik."
                        ></textarea>

                        <p class="help-text">
                            Jelaskan kondisi atau informasi lain
                            yang perlu diketahui petugas.
                        </p>
                    </div>

                    <div class="button-row">
                        <button
                            type="submit"
                            class="button button-primary"
                        >
                            Ajukan Pengembalian
                        </button>

                        <a
                            href="v_homepeminjam.php"
                            class="button button-secondary"
                        >
                            Batal
                        </a>
                    </div>

                </form>

            <?php else: ?>

                <div class="empty-state">
                    <h3>Tidak Ada Alat untuk Dikembalikan</h3>

                    <p>
                        Belum ada alat dari peminjaman yang disetujui
                        dan memenuhi syarat untuk diajukan pengembaliannya.
                    </p>

                    <a
                        href="../View/v_homepeminjam.php"
                        class="button button-primary"
                    >
                        Kembali ke Dashboard
                    </a>
                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
