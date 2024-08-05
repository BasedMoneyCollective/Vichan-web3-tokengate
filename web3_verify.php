<?php
// Include the token verification library
require_once 'inc/vichan_web3_verify.php';

// Check the request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Include the HTML form from an external file
    include 'web3_verify_form.php';
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input from the request body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input.']);
        exit;
    }

    // Get the message and signature from the decoded JSON
    $message = isset($data['message']) ? $data['message'] : '';
    $signature = isset($data['signature']) ? $data['signature'] : '';

    // Check if the message and signature are provided
    if (empty($message) || empty($signature)) {
        echo json_encode(['success' => false, 'error' => 'Message and signature are required.']);
        exit;
    }

    // Verify the user token
    $isVerified = verifyUserToken($message, $signature);

    // Return the verification result
    if ($isVerified) {
        echo json_encode(['success' => true, 'message' => 'Token verified successfully.', 'holding' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Token verification failed.', 'holding' => false]);
    }
}
?>
