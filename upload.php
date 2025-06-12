<?php
// Konfigurasi upload
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$max_size = 1 * 1024 * 1024; // 1MB dalam bytes
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Buat folder uploads jika belum ada
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Pesan error default
$error_message = "";

// ========== VALIDASI FILE ========== //

// 1. Cek apakah file benar-benar diupload
if (!isset($_FILES["fileToUpload"]) || $_FILES["fileToUpload"]["error"] != UPLOAD_ERR_OK) {
    $error_message = "Tidak ada file yang diunggah atau terjadi kesalahan upload.";
    $uploadOk = 0;
}

// 2. Cek apakah file sudah ada
if ($uploadOk && file_exists($target_file)) {
    $error_message = "Maaf, file dengan nama tersebut sudah ada.";
    $uploadOk = 0;
}

// 3. Cek ukuran file
if ($uploadOk && $_FILES["fileToUpload"]["size"] > $max_size) {
    $file_size_mb = number_format($_FILES["fileToUpload"]["size"] / (1024 * 1024), 2);
    $error_message = "Ukuran file ($file_size_mb MB) melebihi batas maksimal 1MB.";
    $uploadOk = 0;
}

// 4. Batasi tipe file yang diperbolehkan
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip', 'rar'];
if ($uploadOk && !in_array($fileType, $allowed_types)) {
    $error_message = "Hanya file JPG, JPEG, PNG, GIF, PDF, DOC, DOCX, TXT, ZIP, dan RAR yang diperbolehkan.";
    $uploadOk = 0;
}

// 5. Cek apakah file adalah file gambar asli (untuk gambar saja)
if ($uploadOk && in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        $error_message = "File bukan gambar yang valid.";
        $uploadOk = 0;
    }
}

// ========== PROSES UPLOAD ========== //

if ($uploadOk == 0) {
    // Jika ada error, redirect ke halaman list dengan pesan error
    header("Location: list_files.php?status=error&message=" . urlencode($error_message));
    exit();
} else {
    // Coba upload file
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Jika berhasil, redirect ke halaman list dengan pesan sukses
        $success_message = "File " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " berhasil diunggah.";
        header("Location: list_files.php?status=success&message=" . urlencode($success_message));
        exit();
    } else {
        // Jika gagal tanpa alasan yang jelas
        $error_message = "Terjadi kesalahan saat mengunggah file.";
        header("Location: list_files.php?status=error&message=" . urlencode($error_message));
        exit();
    }
}
?>