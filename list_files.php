<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Galeri File - Dark Mode</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="gallery-container">
            <div class="gallery-header">
                <h2>Galeri File Anda</h2>
                <a href="index.html" class="upload-another-btn">↑ Unggah Lagi</a>
            </div>

            <?php
            // Tampilkan pesan status
            if (isset($_GET['status']) && isset($_GET['message'])) {
                $class = ($_GET['status'] == 'success') ? 'success' : 'error';
                echo '<div class="message ' . $class . '">' . htmlspecialchars($_GET['message']) . '</div>';
            }

            $upload_dir = "uploads/";

            // Logika hapus file
            if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['file'])) {
                $file_to_delete = $upload_dir . basename($_GET['file']);
                if (file_exists($file_to_delete)) {
                    if (unlink($file_to_delete)) {
                        // Redirect untuk membersihkan URL dan menampilkan pesan
                        header("Location: list_files.php?status=success&message=" . urlencode('File ' . htmlspecialchars($_GET['file']) . ' berhasil dihapus.'));
                        exit();
                    }
                }
            }

            $files = is_dir($upload_dir) ? array_diff(scandir($upload_dir), array('.', '..')) : array();
            
            // Urutkan file berdasarkan waktu modifikasi (terbaru dulu)
            if (!empty($files)) {
                usort($files, function($a, $b) use ($upload_dir) {
                    return filemtime($upload_dir . $b) - filemtime($upload_dir . $a);
                });
            }

            if (empty($files)) {
                echo "<div class='empty-message'>Galeri Anda masih kosong. Mulai unggah file sekarang!</div>";
            } else {
                echo '<div class="gallery-grid">';
                foreach ($files as $file) {
                    $file_path = $upload_dir . $file;
                    $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                    echo '<div class="gallery-card">';
                    
                    // --- KONTEN PRATINJAU ---
                    echo '<div class="card-preview">';
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo '<img src="' . htmlspecialchars($file_path) . '" alt="' . htmlspecialchars($file) . '" loading="lazy">';
                    } 
                    else if ($file_extension === 'pdf') {
                        echo '<iframe src="' . htmlspecialchars($file_path) . '" frameborder="0"></iframe>';
                    }
                    else {
                        $icon = '📄'; // Ikon default
                        if (in_array($file_extension, ['doc', 'docx'])) $icon = '📝';
                        if (in_array($file_extension, ['zip', 'rar'])) $icon = '🗄️';
                        echo '<div class="card-icon-fallback">' . $icon . '</div>';
                    }
                    echo '</div>';

                    // --- NAMA FILE ---
                    echo '<div class="card-filename">' . htmlspecialchars($file) . '</div>';

                    // --- TOMBOL AKSI ---
                    echo '<div class="card-actions">';
                    echo '<a href="' . htmlspecialchars($file_path) . '" download class="download-btn">Download</a>';
                    echo '<a href="?action=delete&file=' . urlencode($file) . '" class="delete-btn" onclick="return confirm(\'Anda yakin ingin menghapus file ini: ' . htmlspecialchars($file) . '?\')">Delete</a>';
                    echo '</div>';

                    echo '</div>'; // Akhir dari gallery-card
                }
                echo '</div>'; // Akhir dari gallery-grid
            }
            ?>
        </div>
    </div>
</body>
</html>