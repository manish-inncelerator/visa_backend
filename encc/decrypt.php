<?php
$ALGORITHM = 'AES-128-CBC';
$IV = '12dasdq3g5b2434b'; // IV should be securely stored

$error = '';

// Check if the file parameter exists in the URL
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $encryptedFile = urldecode($_GET['file']); // Decode the file name from the URL

    // Validate if the file exists in the uploads directory
    $uploadDir = 'uploads/';
    $filePath = $uploadDir . $encryptedFile;

    if (!file_exists($filePath)) {
        $error .= 'The requested file does not exist.<br>';
    }
} else {
    $error .= 'No file specified for decryption.<br>';
}

// Handle the file decryption
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && $error === '') {
    $password = $_POST['password'];

    if ($password === null || empty($password)) {
        $error .= 'Password is required for decryption.<br>';
    }

    if ($error === '') {
        // Get the encrypted file content
        $contenuto = file_get_contents($filePath);

        // Extract the original extension from the encrypted content
        $extensionStart = strpos($contenuto, "\n");
        if ($extensionStart === false) {
            $error .= 'Invalid encrypted file format. Missing extension data.<br>';
        }

        if ($error === '') {
            $originalExtension = base64_decode(substr($contenuto, 0, $extensionStart));
            $contenuto = substr($contenuto, $extensionStart + 1); // Remove the extension part from content

            // Decrypt the file content
            $decryptedContent = openssl_decrypt($contenuto, $ALGORITHM, $password, 0, $IV);

            if ($decryptedContent === false) {
                $error .= 'Decryption failed. This could be due to an incorrect password or corrupted data.<br>';
            }

            if ($error === '') {
                // Set the MIME type based on the file extension
                $mimeType = 'application/octet-stream'; // Default to binary stream
                if ($originalExtension === 'pdf') {
                    $mimeType = 'application/pdf';
                } elseif ($originalExtension === 'jpg' || $originalExtension === 'jpeg') {
                    $mimeType = 'image/jpeg';
                } elseif ($originalExtension === 'png') {
                    $mimeType = 'image/png';
                } elseif ($originalExtension === 'html') {
                    $mimeType = 'text/html';
                }

                // Serve the decrypted file in the browser
                header("Content-Type: $mimeType");
                header("Content-Disposition: inline; filename=\"decrypted_file.$originalExtension\"");
                echo $decryptedContent;
                exit;
            }
        }
    }
}

// Handle encrypted file download if requested
if (isset($_GET['download_encrypted']) && $_GET['download_encrypted'] == 1 && isset($_GET['file'])) {
    $encryptedFile = urldecode($_GET['file']);
    $filePath = 'uploads/' . $encryptedFile;

    if (file_exists($filePath)) {
        // Read the encrypted file content
        $encryptedContent = file_get_contents($filePath);

        // Force the download of the encrypted file
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($filePath) . "\";");
        header("Content-Length: " . strlen($encryptedContent));
        echo $encryptedContent;
        exit;
    } else {
        echo 'The requested file does not exist.';
        exit;
    }
}

// If there's any error, show it
if ($error !== '') {
    echo '<div style="color: red;">' . $error . '</div>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decrypt File</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script>
        // Prevent right-click and Ctrl+S to force the download of the encrypted file only after decryption
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
            alert("Saving is disabled. The encrypted file will be downloaded instead.");
            window.location.href = "?download_encrypted=1&file=<?php echo urlencode($encryptedFile); ?>"; // Redirect to encrypted file download
        });

        document.addEventListener("keydown", function(e) {
            if (e.ctrlKey && (e.key === "s" || e.key === "S")) {
                e.preventDefault();
                alert("Saving is disabled. The encrypted file will be downloaded instead.");
                window.location.href = "?download_encrypted=1&file=<?php echo urlencode($encryptedFile); ?>"; // Redirect to encrypted file download
            }
        });
    </script>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Decrypt File</h1>

        <?php if ($error === ''): ?>
            <p>Decrypting file: <strong><?php echo htmlspecialchars($encryptedFile); ?></strong></p>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Decrypt</button>
            </div>
        </form>
    </div>
</body>

</html>