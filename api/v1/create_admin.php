<?php
// Include Medoo and the Ramsey UUID library
require '../../Database.php'; // Adjust the path as necessary

use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Guid\GuidInterface;
use Ramsey\Uuid\Rfc4122\FieldsInterface;

// Get the JSON data sent from the client
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if the required data is provided
if (isset($inputData['exampleInputEmail1'], $inputData['adminName'], $inputData['adminRole'], $inputData['adminDept'])) {
    try {
        // Check if the email already exists in the database
        $existingAdmin = $database->get('admins', ['admin_email_address'], ['admin_email_address' => $inputData['exampleInputEmail1']]);

        if ($existingAdmin) {
            // If the email already exists, return an error message
            echo json_encode([
                'status' => 'error',
                'message' => 'Email address already exists.',
            ]);
            exit; // Stop further processing
        }

        // Generate a UUID4 using Ramsey UUID library
        $uuid4 = Ramsey\Uuid\Guid\Guid::uuid4();
        $adminId = $uuid4->toString(); // Get the string representation of the UUID

        // Set options for Argon2id password hashing
        $options = [
            'memory_cost' => 1 << 17, // 128 MB
            'time_cost' => 4,         // 4 iterations
            'threads' => 2            // 2 threads
        ];

        // Generate first time password for the admin
        $firstTimePassword =  generateWordCaptcha(); // Outputs something like 'apple-Dog' or 'rainbow-Pen'

        // Hash the password using Argon2id
        $passwordHash = password_hash($firstTimePassword, PASSWORD_ARGON2ID, $options);

        // Prepare the data for insertion
        $dataToInsert = [
            'admin_email_address' => $inputData['exampleInputEmail1'],
            'admin_name' => $inputData['adminName'],
            'admin_id' => $adminId, // UUID4 id
            'admin_password' => $passwordHash, // Hashed password
            'encryption_password' => generateSecureEncryptionPassword(), // Secure encryption password
            'admin_first_time_password' => $firstTimePassword, // First time password
            'admin_role' => $inputData['adminRole'],
            'admin_dept' => $inputData['adminDept'],
            'is_first_time' => 1, // Set to 1 for first time login
            'created_at' => date('Y-m-d H:i:s'), // Store the current date and time
        ];

        // Insert the data into the 'admins' table
        $inserted = $database->insert('admins', $dataToInsert);

        // Return success or error message based on insertion result
        if ($inserted) {
            // TODO: send email to the admin with first time password and login link via phpmailer
            echo json_encode([
                'status' => 'success',
                'message' => 'Admin created successfully.',
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create admin. Insertion failed.',
            ]);
        }
    } catch (Exception $e) {
        // Handle the exception and return a response
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage(),
        ]);
    }
} else {
    // Return error if the necessary data is missing
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required data.',
    ]);
}

function generateWordCaptcha($length = 2)
{
    // List of words for the CAPTCHA
    $words = [
        'apple',
        'banana',
        'cherry',
        'dog',
        'elephant',
        'frog',
        'grape',
        'house',
        'ice',
        'jungle',
        'king',
        'lion',
        'monkey',
        'night',
        'orange',
        'pen',
        'queen',
        'rose',
        'sun',
        'tree',
        'umbrella',
        'violet',
        'water',
        'xylophone',
        'yellow',
        'zebra',
        'ant',
        'ball',
        'cat',
        'door',
        'egg',
        'fish',
        'goose',
        'hat',
        'island',
        'jacket',
        'kite',
        'lemon',
        'mouse',
        'notebook',
        'octopus',
        'pencil',
        'queen',
        'rainbow',
        'star',
        'tiger',
        'unicorn',
        'vulture',
        'whale',
        'xmas',
        'yacht',
        'zebra',
        'airplane',
        'bicycle',
        'cloud',
        'dolphin',
        'elephant',
        'flame',
        'guitar',
        'hurricane',
        'insect',
        'jellyfish',
        'kangaroo',
        'lighthouse',
        'mountain',
        'noodle',
        'octagon',
        'parrot',
        'quilt',
        'rocket',
        'shark',
        'turtle',
        'umbrella',
        'volcano',
        'waterfall',
        'x-ray',
        'yellowstone',
        'zoo',
        'acorn',
        'beach',
        'cactus',
        'dinosaur',
        'eagle',
        'firefly',
        'giraffe',
        'hippopotamus',
        'igloo',
        'jackal',
        'koala',
        'lighthouse',
        'mango',
        'noodle',
        'owl',
        'panda',
        'quicksand',
        'rosebud',
        'snowflake',
        'treehouse',
        'universe',
        'vulture',
        'windmill',
        'xenon',
        'yoga',
        'zeppelin'
    ];

    // Randomly select two words
    $word1 = $words[array_rand($words)];
    $word2 = $words[array_rand($words)];

    // Combine the words with a hyphen
    return $word1 . '-' . ucwords($word2);
}

function generateSecureEncryptionPassword($length = 16)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_=+[]{}|;:,.<>?';
    $password = '';
    $bytes = random_bytes($length);

    // Generate the password by selecting characters based on random bytes
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[ord($bytes[$i]) % strlen($characters)];
    }

    return $password;
}
