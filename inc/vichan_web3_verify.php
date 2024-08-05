<?php 

// Define the backend service URL as a global variable
$backend_service_url = 'http://localhost:5000/verify-token'; // Replace with url of your https://github.com/web3NOSTR/TokenGateService-Lite instance 



/**
 * Check if the 'verifiedHolder' cookie is set.
 *
 * @return bool
 */
function isVerifiedHolder() {
    if (isset($_COOKIE['verifiedHolder']) && $_COOKIE['verifiedHolder'] === 'true') {
        return true;
    }
    return false;
}

/**
 * Verify the user token using the backend API.
 *
 * @param string $message The message to be verified.
 * @param string $signature The user's signature.
 * @return bool
 */
function verifyUserToken($message, $signature) {
    global $backend_service_url;

    // Sanitize the message and signature
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $signature = htmlspecialchars($signature, ENT_QUOTES, 'UTF-8');

    // Prepare data for the API request
    $data = json_encode([
        'message' => $message,
        'signature' => $signature
    ]);

    // Initialize cURL session
    $ch = curl_init($backend_service_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Execute the request
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $result = json_decode($response, true);

    // Check if the response is successful and the user holds the token
    if (isset($result['success']) && $result['success'] === true) {
        // Set a cookie for verified holder
        setcookie('verifiedHolder', 'true', time() + (86400 * 30), "/"); // 30 days
        return true;
    }

    // Verification failed
    return false;
}


?>

