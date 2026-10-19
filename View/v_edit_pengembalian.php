
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION["data"]["id_user"]) ||
    ($_SESSION["data"]["role"] ?? "") !== "admin"
) {
    header("Location: v_login.php");
    exit;
}

require_once __DIR__ . "/../Model/m_pengembalian.php";

$model = new m_pengembalian();
$id = (int) ($_GET["id"] ?? 0);
$data = $id > 0 ? $model->get_by_id($id) : false;

if (!$data) {
    $_SESSION["pesan_pengembalian"] = [
        "jenis" => "error",
        "isi" => "Data pengembalian tidak ditemukan."
    ];
    header("Location: v_pengembalian.php");
    exit;
}

function e_edit_pengembalian($value)
{
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}

$pesan = $_SESSION["pesan_pengembalian"] ?? null;
unset($_SESSION["pesan_pengembalian"]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Pengembalian — pinjam.in</title>
<style>
:root {
    --cream: #f4f1ea;
    --cream-deep: #efebe1;
    --teal-dark: #0f6b64;
    --teal-deep: #0a4a45;
    --ink: #1c2b29;
    --muted: #7d8a87;
    --border: #e4e0d6;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--cream-deep);
    color: var(--ink);
}
.layout { display: flex; min-height: 100vh; }
.sidebar {
    width: 230px;
    background: var(--teal-deep);
    color: white;
    padding: 24px 18px;
    flex-shrink: 0;
}
.brand { font-size: 20px; font-weight: 800; margin: 4px 0 8px; }
.tag { color: #8fc9c2; font-size: 10px; letter-spacing: 1px; font-weight: 700; }
.nav { margin-top: 34px; }
.nav a {
    display: block;
    padding: 12px;
    margin-bottom: 5px;
    border-radius: 8px;
    color: #dce9e7;
    text-decoration: none;
    font-size: 13px;
}
.nav a:hover, .nav a.active { background: var(--teal-dark); color: white; }
.main { flex: 1; min-width: 0; padding: 32px; }
h1 { margin: 0 0 6px; font-size: 24px; }
.subtitle { color: var(--muted); margin: 0 0 24px; font-size: 13px; }
.panel {
    max-width: 780px;
    padding: 25px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: var(--cream);
}
.panel h2 { margin: 0 0 22px; font-size: 17px; }
.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.field { display: flex; flex-direction: column; gap: 7px; }
.field.full { grid-column: 1 / -1; }
label { font-size: 13px; font-weight: 700; }
input, select, textarea {
    width: 100%;
    border: 1px solid var(--border);
    background: white;
    border-radius: 8px;
    padding: 11px 12px;
    font: inherit;
    font-size: 13px;
    color: var(--ink);
}
input[readonly] { background: #efebe1; color: #65716e; }
textarea { min-height: 90px; resize: vertical; }
.actions { display: flex; gap: 10px; margin-top: 24px; }
.btn {
    border: 0;
    border-radius: 8px;
    padding: 11px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
}
.btn-save { background: var(--teal-dark); color: white; }
.btn-back { background: #e4e0d6; color: var(--ink); }
.message { padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; }
.success { background: #d9efe9; color: #0f6b64; }
.error { background: #fbdede; color: #a53939; }
@media (max-width: 700px) {
    .layout { flex-direction: column; }
    .sidebar { width: 100%; }
    .main { padding: 20px; }
    .grid { grid-template-columns: 1fr; }
    .field.full { grid-column: auto; }
}
</style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">pinjam.in</div>
        <div class="tag">ADMIN PANEL</div>
        <nav class="nav">
            <a href="v_homeadmin.php">Dashboard</a>
            <a href="v_peminjaman.php">Data Peminjaman</a>
            <a class="active" href="v_pengembalian.php">Data Pengembalian</a>
        </nav>
    </aside>

    <main class="main">
        <h1>Edit Pengembalian</h1>
        <p class="subtitle">Perbarui informasi pengembalian alat.</p>

        <section class="panel">
            <h2>Informasi Pengembalian</h2>

            <?php if ($pesan): ?>
                <div class="message <?= $pesan["jenis"] === "success" ? "success" : "error" ?>">
                    <?= e_edit_pengembalian($pesan["isi"]) ?>
                </div>
            <?php endif; ?>

            <form action="../Controller/c_pengembalian.php?aksi=update_admin"
                  method="POST">
                <input type="hidden" name="id_pengembalian"
                       value="<?= (int) $data["id_pengembalian"] ?>">

                <div class="grid">
                    <div class="field">
                        <label>ID Pengembalian</label>
                        <input type="text"
                               value="<?= (int) $data["id_pengembalian"] ?>"
                               readonly>
                    </div>

                    <div class="field">
                        <label>ID Peminjaman</label>
                        <input type="text"
                               value="<?= e_edit_pengembalian($data["id_peminjaman"]) ?>"
                               readonly>
                    </div>

                    <div class="field full">
                        <label>Nama Alat</label>
                        <input type="text"
                               value="<?= e_edit_pengembalian($data["nama_alat"]) ?>"
                               readonly>
                    </div>

                    <div class="field">
                        <label>Tanggal Jatuh Tempo</label>
                        <input type="text"
                               value="<?= e_edit_pengembalian($data["tanggal_kembali_rencana"]) ?>"
                               readonly>
                    </div>

                    <div class="field">
                        <label>Tanggal Pengembalian</label>
                        <input type="date" name="tanggal_kembali"
                               max="<?= date("Y-m-d") ?>"
                               value="<?= e_edit_pengembalian($data["tanggal_kembali"]) ?>"
                               required>
                    </div>

                    <div class="field">
                        <label>Kondisi Alat</label>
                        <select name="kondisi_kembali" required>
                            <?php foreach (["Baik", "Rusak", "Hilang"] as $opsi): ?>
                                <option value="<?= $opsi ?>"
                                    <?= ($data["kondisi_kembali"] ?? "") === $opsi ? "selected" : "" ?>>
                                    <?= $opsi ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Status Pembayaran Denda</label>
                        <select name="status_bayar" required>
                            <option value="belum_bayar"
                                <?= ($data["status_bayar"] ?? "") === "belum_bayar" ? "selected" : "" ?>>
                                Belum Dibayar
                            </option>
                            <option value="lunas"
                                <?= ($data["status_bayar"] ?? "") === "lunas" ? "selected" : "" ?>>
                                Lunas
                            </option>
                        </select>
                    </div>

                    <div class="field full">
                        <label>Keterangan</label>
                        <textarea name="keterangan"><?= e_edit_pengembalian($data["keterangan"] ?? "") ?></textarea>
                    </div>

                    <div class="field full">
                        <label>Denda Saat Ini</label>
                        <input type="text"
                               value="Rp <?= number_format((float) ($data["denda"] ?? 0), 0, ",", ".") ?>"
                               readonly>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn btn-save" type="submit">Simpan Perubahan</button>
                    <a class="btn btn-back" href="v_pengembalian.php">Kembali</a>
                </div>
            </form>
        </section>
    </main>
</div>
</body>
</html>
