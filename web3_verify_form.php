<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Verification</title>
    <script src="https://cdn.jsdelivr.net/npm/web3/dist/web3.min.js"></script>
    <style>
        body {
            background-color: #f0f8ff; /* Light blue background */
            font-family: 'Arial', sans-serif;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #98fb98; /* Light green, frog-themed */
            padding: 20px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            font-size: 2em;
            margin: 0;
            color: #006400; /* Dark green text */
        }
        .content {
            padding: 20px;
        }
        #connect-wallet, #verify-token {
            background-color: #32cd32; /* Frog green */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
            transition: background-color 0.3s;
        }
        #connect-wallet:hover, #verify-token:hover {
            background-color: #228b22; /* Darker green on hover */
        }
        #status {
            margin: 20px 0;
            font-weight: bold;
        }
        .link {
            margin: 20px 0;
        }
        .link a {
            color: #006400; /* Dark green */
            text-decoration: none;
            font-weight: bold;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .frog-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1; /* Light overlay for background */
            z-index: -1;
        }
    </style>
</head>
<body>
    <header>
        <h1>Frog Themed Image Board</h1>
        <p>Only verified frog holders can access the members area</p>
    </header>
    <div class="content">
        <button id="connect-wallet">Connect Wallet</button>
        <button id="verify-token" disabled>Verify Token Ownership</button>
        <div id="status"></div>
        <div class="link"><a href="/membersOnly">Members area</a></div>
    </div>
    <img src="frog-background.jpg" alt="Frog Background" class="frog-background">

    <script>
        let web3;
        let userAddress;

        document.getElementById('connect-wallet').onclick = async () => {
            if (window.ethereum) {
                try {
                    web3 = new Web3(window.ethereum);
                    await window.ethereum.request({ method: 'eth_requestAccounts' });
                    userAddress = (await web3.eth.getAccounts())[0];
                    document.getElementById('verify-token').disabled = false;
                    document.getElementById('status').innerText = 'Wallet connected: ' + userAddress;
                } catch (error) {
                    console.error(error);
                    document.getElementById('status').innerText = 'Error connecting wallet';
                }
            } else {
                document.getElementById('status').innerText = 'MetaMask not detected';
            }
        };

        document.getElementById('verify-token').onclick = async () => {
            if (web3 && userAddress) {
                try {
                    let message = "I am the owner of this address.";
                    const nonce = Math.floor(Math.random() * 1000000); // Generate a random nonce
                    const messageWithNonce = `${message} ${nonce}`; // Combine message and nonce

                    message = messageWithNonce;

                    const signature = await web3.eth.personal.sign(messageWithNonce, userAddress, "");

                    // Send the message and signature to the server for verification
                    const response = await fetch('web3_verify.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ message, signature })
                    });

                    const result = await response.json();
                    if (result.success) {
                        if (result.holding) {
                            document.getElementById('status').innerText = 'You hold the token.';
                        } else {
                            document.getElementById('status').innerText = 'You do not hold the token.';
                        }
                    } else {
                        document.getElementById('status').innerText = 'Error: ' + result.error;
                    }
                } catch (error) {
                    console.error(error);
                    document.getElementById('status').innerText = 'Error verifying token ownership';
                }
            }
        };
    </script>
</body>
</html>
