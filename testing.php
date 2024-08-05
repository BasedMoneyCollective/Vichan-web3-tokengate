<?php
// Include the token verification library
require_once 'inc/vichan_web3_verify.php';

// Check the request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Display the form
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Token Verification</title>
    </head>
    <body>
        <h1>Token Verification</h1>
        <form action="testing.php" method="post">
            <label for="message">Message:</label><br>
            <input type="text" id="message" name="message" required><br><br>
            
            <label for="signature">Signature:</label><br>
            <input type="text" id="signature" name="signature" required><br><br>
            
            <input type="submit" value="Verify">
        </form>
    </body>
    </html>';
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the message and signature from POST parameters
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    $signature = isset($_POST['signature']) ? $_POST['signature'] : '';

    // Check if the POST parameters are provided
    if (empty($message) || empty($signature)) {
        echo json_encode(['success' => false, 'error' => 'Message and signature are required.']);
        exit;
    }

    // Verify the user token
    $isVerified = verifyUserToken($message, $signature);

    if ($isVerified) {
        echo json_encode(['success' => true, 'message' => 'Token verified successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Token verification failed.']);
    }
}
?>
