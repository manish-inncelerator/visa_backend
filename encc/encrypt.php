<?php
$ALGORITHM = 'AES-128-CBC';
$IV = '12dasdq3g5b2434b'; // IV should be securely stored

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'c') {

    $file = isset($_FILES) && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK ? $_FILES['file'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($file === null) {
        $error .= 'Errors occurred while processing the file<br>';
    }

    if ($password === null || empty($password)) {
        $error .= 'Password is required for encryption<br>';
    }

    if ($error === '') {

        // Get file content and filename
        $contenuto = file_get_contents($file['tmp_name']);
        $filename = $file['name'];

        // Get the file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Encrypt the file content
        $encryptedContent = openssl_encrypt($contenuto, $ALGORITHM, $password, 0, $IV);

        if ($encryptedContent === false) {
            $error .= 'Error occurred while encrypting the file<br>';
        }

        if ($error === '') {

            // Base64 encode the file extension and prepend it to the encrypted content
            $base64Extension = base64_encode($extension);
            $encryptedFileContent = $base64Extension . "\n" . $encryptedContent;

            // Save the encrypted content to the uploads/ directory
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $encryptedFilename = $uploadDir . $filename . '.crypto';
            file_put_contents($encryptedFilename, $encryptedFileContent);

            // Save the encryption key in the keys/ directory
            $keyDir = 'keys/';
            if (!is_dir($keyDir)) {
                mkdir($keyDir, 0777, true);
            }
            $keyFilename = $keyDir . $filename . '.key';
            file_put_contents($keyFilename, $password);

            // Provide a success message or redirect
            echo "File encrypted successfully. Encrypted file saved in 'uploads/' and key saved in 'keys/' directory.";
            die;
        }
    }
}

// If there's any error, show it
if ($error !== '') {
    echo '<div style="color: red;">' . $error . '</div>';
}
