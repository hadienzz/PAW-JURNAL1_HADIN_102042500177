<?php

session_start();

// ============================================================

//  TUGAS JURNAL PRAKTIKUM - PEMROGRAMAN WEB

//  Sistem Pendaftaran Calon Asisten Praktikum Laboratorium

// ============================================================

//  Nama  : Hadin Pramiadi

//  NIM   : 102042500177

//  Kelas : S1SI 25-06

// ============================================================

// Daftar mata kuliah praktikum

$daftar_matkul = [
    "Algoritma dan Pemrograman",
    "Analisis dan Perancangan Sistem Informasi",
    "Arsitektur Enterprise",
    "Data Warehouse dan Business Intelligence",
    "Komputasi Awan",
    "Pemodelan Proses Bisnis",
    "Pengantar Sistem Informasi",
    "Pengembangan Aplikasi Bergerak",
    "Pengembangan Aplikasi Website",
    "Pengembangan UI Lanjut",
    "Proyek Perangkat Lunak",
    "Sistem Enterprise",
    "Sistem Informasi Akuntansi",
    "Sistem Operasi"
];

// **********************  1  **************************

// Inisialisasi variabel untuk menyimpan nilai input dan error

// Buat variabel: $nama, $whatsapp, $email, $matkul, $motivasi

// Buat variabel error: $namaErr, $waErr, $emailErr, $matkulErr, $motivasiErr

// Beri nilai awal string kosong ""

$nama = "";
$whatsapp = "";
$email = "";
$matkul = "";
$motivasi = "";

$namaErr = "";
$waErr = "";
$emailErr = "";
$matkulErr = "";
$motivasiErr = "";

// Mode tampilan default adalah form

$mode = "form";
$pesan = "";

// Ambil input teks; payload berbentuk array dianggap input kosong.
function inputTeks(string $field): string
{
    $value = $_POST[$field] ?? "";
    return is_string($value) ? trim($value) : "";
}

// Escape saat menampilkan data agar input tidak menjadi kode HTML.
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

// Validasi pendaftaran sebelum menyimpan seluruh data ke sesi.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = inputTeks("nama");
    $whatsapp = inputTeks("whatsapp");
    $email = inputTeks("email");
    $matkul = inputTeks("matkul");
    $motivasi = inputTeks("motivasi");

    if ($nama === "") {
        $namaErr = "Nama lengkap wajib diisi.";
    }

    if ($whatsapp === "") {
        $waErr = "Nomor WhatsApp wajib diisi.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $whatsapp)) {
        $waErr = "Nomor WhatsApp harus berisi 10–15 digit angka.";
    }

    if ($email === "") {
        $emailErr = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Masukkan alamat email yang valid.";
    }

    if ($matkul === "") {
        $matkulErr = "Pilih mata kuliah praktikum.";
    } elseif (!in_array($matkul, $daftar_matkul, true)) {
        $matkulErr = "Mata kuliah yang dipilih tidak tersedia.";
    }

    if ($motivasi === "") {
        $motivasiErr = "Motivasi wajib diisi.";
    }

    if (
        $namaErr === "" && $waErr === "" && $emailErr === ""
        && $matkulErr === "" && $motivasiErr === ""
    ) {
        $_SESSION["data_pendaftar"] = [
            "nama" => $nama,
            "whatsapp" => $whatsapp,
            "email" => $email,
            "matkul" => $matkul,
            "motivasi" => $motivasi
        ];

        // Redirect agar refresh kartu tidak mengirim ulang pendaftaran.
        header("Location: soal.php?page=id_card", true, 303);
        exit;
    }
}

// Cek apakah tombol "Lihat Data Pendaftar" diklik

if (
    $_SERVER["REQUEST_METHOD"] === "GET"
    && isset($_GET['page']) && $_GET['page'] === 'id_card'
) {

    if (!empty($_SESSION['data_pendaftar'])) {

        $nama = $_SESSION['data_pendaftar']['nama'];
        $whatsapp = $_SESSION['data_pendaftar']['whatsapp'];
        $email = $_SESSION['data_pendaftar']['email'];
        $matkul = $_SESSION['data_pendaftar']['matkul'];
        $motivasi = $_SESSION['data_pendaftar']['motivasi'];
        $mode = "id_card";
    } else {
        $pesan = "Belum ada data pendaftar. Isi dan kirim formulir terlebih dahulu.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Calon Asisten Praktikum</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php if ($mode === "form"): ?>
        <main class="container">
            <img src="logo.png" alt="Logo kampus" class="logo">
            <h2>Pendaftaran Calon Asisten Praktikum</h2>
            <p class="subtitle">Laboratorium · Isi data untuk mendaftar sebagai calon asisten.</p>

            <?php if ($pesan !== ""): ?>
                <div class="alert alert-info" role="status"><?= escapeHtml($pesan) ?></div>
            <?php endif; ?>
            <?php if (
                $namaErr !== "" || $waErr !== "" || $emailErr !== ""
                || $matkulErr !== "" || $motivasiErr !== ""
            ): ?>
                <div class="alert alert-danger" role="alert">Pendaftaran belum tersimpan. Periksa kolom yang ditandai.</div>
            <?php endif; ?>

            <form action="soal.php" method="post" novalidate>
                <div class="form-group">
                    <label for="nama">Nama lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama" value="<?= escapeHtml($nama) ?>"
                        placeholder="Contoh: Hadin Pramiadi" autocomplete="name" required
                        aria-invalid="<?= $namaErr !== "" ? "true" : "false" ?>" aria-describedby="nama-error">
                    <span class="error" id="nama-error"><?= escapeHtml($namaErr) ?></span>
                </div>
                <div class="form-group">
                    <label for="whatsapp">Nomor WhatsApp <span class="required">*</span></label>
                    <input type="text" id="whatsapp" name="whatsapp" value="<?= escapeHtml($whatsapp) ?>"
                        placeholder="Contoh: 081234567890" inputmode="numeric" autocomplete="tel"
                        pattern="[0-9]{10,15}" minlength="10" maxlength="15" required
                        aria-invalid="<?= $waErr !== "" ? "true" : "false" ?>" aria-describedby="whatsapp-error">
                    <span class="error" id="whatsapp-error"><?= escapeHtml($waErr) ?></span>
                </div>
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?= escapeHtml($email) ?>"
                        placeholder="Contoh: nama@student.ac.id" autocomplete="email" required
                        aria-invalid="<?= $emailErr !== "" ? "true" : "false" ?>" aria-describedby="email-error">
                    <span class="error" id="email-error"><?= escapeHtml($emailErr) ?></span>
                </div>
                <diWv class="form-group">
                    <label for="matkul">Mata kuliah praktikum <span class="required">*</span></label>
                    <select id="matkul" name="matkul" required
                        aria-invalid="<?= $matkulErr !== "" ? "true" : "false" ?>" aria-describedby="matkul-error">
                        <option value="">Pilih mata kuliah</option>
                        <?php foreach ($daftar_matkul as $mataKuliah): ?>
                            <option value="<?= escapeHtml($mataKuliah) ?>" <?= $matkul === $mataKuliah ? " selected" : "" ?>><?= escapeHtml($mataKuliah) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error" id="matkul-error"><?= escapeHtml($matkulErr) ?></span>
                </diWv>
                <div class="form-group">
                    <label for="motivasi">Motivasi menjadi asisten <span class="required">*</span></label>
                    <textarea id="motivasi" name="motivasi" placeholder="Ceritakan alasan Anda ingin menjadi asisten praktikum." required
                        aria-invalid="<?= $motivasiErr !== "" ? "true" : "false" ?>" aria-describedby="motivasi-error"><?= escapeHtml($motivasi) ?></textarea>
                    <span class="error" id="motivasi-error"><?= escapeHtml($motivasiErr) ?></span>
                </div>
                <div class="button-container">
                    <button type="submit">Daftar Sekarang</button>
                    <a href="soal.php?page=id_card" class="btn-lihat-data">Lihat Data Pendaftar</a>
                </div>
            </form>
        </main>
    <?php else: ?>
        <main class="id-card">
            <div class="id-card-header">
                <img src="logo.png" alt="Logo kampus" class="logo">
                <h2>Kartu Pendaftar</h2>
                <p>Calon Asisten Praktikum Laboratorium</p>
            </div>
            <div class="id-card-body">
                <div class="id-card-row">
                    <span class="id-card-label">Nama lengkap</span>
                    <span class="id-card-value"><?= escapeHtml($nama) ?></span>
                </div>
                <div class="id-card-row">
                    <span class="id-card-label">Nomor WhatsApp</span>
                    <span class="id-card-value"><?= escapeHtml($whatsapp) ?></span>
                </div>
                <div class="id-card-row">
                    <span class="id-card-label">Email</span>
                    <span class="id-card-value"><?= escapeHtml($email) ?></span>
                </div>
                <div class="id-card-row">
                    <span class="id-card-label">Mata kuliah</span>
                    <span class="id-card-value"><?= escapeHtml($matkul) ?></span>
                </div>
                <hr class="id-card-divider">
                <div class="id-card-row">
                    <span class="id-card-label">Motivasi</span>
                    <span class="id-card-value"><?= nl2br(escapeHtml($motivasi)) ?></span>
                </div>
                <span class="id-card-badge">Pendaftaran tersimpan</span>
            </div>
            <a href="soal.php" class="btn-kembali">Kembali ke Formulir</a>
            <p class="id-card-footer">Data pendaftar tersimpan selama sesi browser masih aktif.</p>
        </main>
    <?php endif; ?>
</body>

</html>