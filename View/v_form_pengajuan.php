<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pesan = $_SESSION["pesan_pengajuan"] ?? "";
unset($_SESSION["pesan_pengajuan"]);

$stok = (int) $alat["jumlah"];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajukan Peminjaman - Pinjam.in</title>
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
        button, input, textarea { font: inherit; }

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

        .nav-back:hover {
            background: var(--teal-dark);
            color: #fff;
        }

        /* ---------- Layout ---------- */
        .container {
            max-width: 620px;
            margin: auto;
            padding: 30px 18px 56px;
        }

        .page-title { margin-bottom: 20px; }

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
            line-height: 1.6;
        }

        /* ---------- Alert ---------- */
        .pesan {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            background: #fff8e8;
            color: #77571b;
            border: 1px solid #f0dfb5;
            font-size: 14px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .pesan .ic { margin-top: 2px; font-size: 17px; }

        /* ---------- Tool summary ---------- */
        .tool-summary {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0f6b64 0%, #0b554f 100%);
            color: #fff;
            border-radius: 18px;
            padding: 22px 24px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-md);
        }

        .tool-summary::before {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            right: -60px;
            top: -90px;
            pointer-events: none;
        }

        .tool-summary-inner { position: relative; z-index: 1; }

        .tool-summary .label {
            font-size: 11.5px;
            letter-spacing: 1px;
            font-weight: 700;
            color: #d5e9e3;
        }

        .tool-summary h2 {
            margin: 6px 0 14px;
            font-size: 22px;
            letter-spacing: -0.3px;
            overflow-wrap: anywhere;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            padding: 7px 13px;
            font-size: 13px;
        }

        .chip .ic { font-size: 14px; }

        /* ---------- Form card ---------- */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 26px;
            box-shadow: var(--shadow-sm);
        }

        .field { margin-bottom: 20px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .hint {
            display: block;
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--muted);
        }

        input, textarea {
            width: 100%;
            padding: 12px 14px;
            background: var(--cream);
            border: 1px solid transparent;
            border-radius: 10px;
            color: var(--text);
            outline: none;
            transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
        }

        textarea { resize: vertical; min-height: 110px; line-height: 1.6; }

        input:focus, textarea:focus {
            background: #fff;
            border-color: var(--teal-dark);
            box-shadow: 0 0 0 3px rgba(15, 107, 100, 0.12);
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .counter {
            display: block;
            text-align: right;
            margin-top: 6px;
            font-size: 12px;
            color: var(--muted);
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 20px;
            border: 0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.15s;
        }

        .btn-primary {
            flex: 1;
            background: var(--teal-dark);
            color: #fff;
        }

        .btn-primary:hover { background: var(--teal-darker); }
        .btn-primary:hover .ic { transform: translateX(3px); }
        .btn-primary .ic { transition: transform 0.15s; }

        .btn-back {
            background: var(--cream);
            color: #52635e;
            border: 1px solid var(--border);
        }

        .btn-back:hover { background: var(--cream-dark); }

        @media (max-width: 560px) {
            .container { padding: 22px 14px 40px; }
            .card { padding: 20px; }
            .row { grid-template-columns: 1fr; gap: 0; }
            .page-title h1 { font-size: 24px; }
            .actions { flex-direction: column-reverse; }
            .btn-back { width: 100%; }
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
        Kembali
    </a>
</header>

<main class="container">

    <div class="page-title">
        <small>FORM PENGAJUAN</small>
        <h1>Ajukan Peminjaman</h1>
        <p>Isi data di bawah ini dengan lengkap. Pengajuan lo akan diproses oleh petugas.</p>
    </div>

    <?php if ($pesan !== ""): ?>
        <div class="pesan">
            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
            <div><?= htmlspecialchars($pesan) ?></div>
        </div>
    <?php endif; ?>

    <section class="tool-summary">
        <div class="tool-summary-inner">
            <div class="label">ALAT YANG DIPINJAM</div>
            <h2><?= htmlspecialchars($alat["nama_alat"]) ?></h2>

            <div class="chips">
                <span class="chip">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><path d="M7 7h.01"/></svg>
                    Kode: <?= htmlspecialchars($alat["kode_alat"]) ?>
                </span>
                <span class="chip">
                    <svg class="ic" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/></svg>
                    Stok tercatat: <?= $stok ?> unit
                </span>
            </div>
        </div>
    </section>

    <form class="card" method="POST"
          action="../Controller/c_pengajuan.php?aksi=simpan">
        <input type="hidden" name="id_alat"
               value="<?= (int)$alat["id_alat"] ?>">

        <div class="field">
            <label for="jumlah">Jumlah yang ingin dipinjam</label>
            <input type="number" id="jumlah" name="jumlah"
                   min="1" max="<?= $stok ?>"
                   value="1" required>
            <span class="hint">Maksimal <?= $stok ?> unit sesuai stok yang tercatat.</span>
        </div>

        <div class="row">
            <div class="field">
                <label for="tanggal_pinjam">Tanggal mulai pinjam</label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam"
                       min="<?= date("Y-m-d") ?>" required>
            </div>

            <div class="field">
                <label for="tanggal_kembali_rencana">Rencana tanggal kembali</label>
                <input type="date" id="tanggal_kembali_rencana"
                       name="tanggal_kembali_rencana"
                       min="<?= date("Y-m-d") ?>" required>
            </div>
        </div>

        <div class="field">
            <label for="keperluan">Keperluan peminjaman</label>
            <textarea id="keperluan" name="keperluan"
                      rows="4" maxlength="500" required
                      placeholder="Contoh: dipakai untuk dokumentasi..."></textarea>
            <span class="counter"><span id="hitungKarakter">0</span>/500</span>
        </div>

        <div class="actions">
            <a class="btn btn-back" href="../View/v_homepeminjam.php">
                <svg class="ic" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Kembali
            </a>

            <button type="submit" class="btn btn-primary">
                Kirim Pengajuan
                <svg class="ic" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
        </div>
    </form>
</main>

<script>
    const tglPinjam = document.getElementById("tanggal_pinjam");
    const tglKembali = document.getElementById("tanggal_kembali_rencana");
    const keperluan = document.getElementById("keperluan");
    const hitung = document.getElementById("hitungKarakter");

    // Tanggal kembali tidak boleh sebelum tanggal mulai pinjam
    tglPinjam.addEventListener("change", function () {
        if (tglPinjam.value) {
            tglKembali.min = tglPinjam.value;
            if (tglKembali.value && tglKembali.value < tglPinjam.value) {
                tglKembali.value = tglPinjam.value;
            }
        }
    });

    keperluan.addEventListener("input", function () {
        hitung.textContent = keperluan.value.length;
    });
</script>

</body>
</html>