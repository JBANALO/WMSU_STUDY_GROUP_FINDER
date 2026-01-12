<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Google Sign-In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #8B0000;
            margin-bottom: 20px;
        }
        .log {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 12px;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid #8B0000;
            padding-left: 10px;
        }
        .success { border-left-color: green; }
        .error { border-left-color: red; }
        .info { border-left-color: blue; }
        #googleSignInDiv {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Google Sign-In Diagnostic Tool</h1>
        <p>This page will help diagnose Google Sign-In issues.</p>
        
        <div id="googleSignInDiv"></div>
        
        <div class="log" id="logContainer">
            <strong>Activity Log:</strong>
        </div>
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        const logContainer = document.getElementById('logContainer');
        
        function addLog(message, type = 'info') {
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            const timestamp = new Date().toLocaleTimeString();
            entry.textContent = `[${timestamp}] ${message}`;
            logContainer.appendChild(entry);
            logContainer.scrollTop = logContainer.scrollHeight;
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
        
        addLog('Page loaded - waiting for Google library...', 'info');
        
        function handleCredentialResponse(response) {
            addLog('✅ Google credential received!', 'success');
            
            if (!response || !response.credential) {
                addLog('❌ ERROR: No credential in response', 'error');
                return;
            }
            
            addLog('Sending credential to backend...', 'info');
            
            fetch('handlers/google_signin_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    credential: response.credential
                })
            })
            .then(res => {
                addLog(`Backend response status: ${res.status}`, res.ok ? 'success' : 'error');
                if (!res.ok) {
                    throw new Error('HTTP error ' + res.status);
                }
                return res.json();
            })
            .then(data => {
                addLog('Backend response: ' + JSON.stringify(data), 'info');
                
                if (data.success) {
                    addLog('✅ Login successful!', 'success');
                    addLog('Redirecting to dashboard...', 'info');
                    setTimeout(() => {
                        window.location.href = 'index.php?page=dashboard';
                    }, 1000);
                } else {
                    addLog('❌ Login failed: ' + (data.message || 'Unknown error'), 'error');
                    alert(data.message || 'Login failed. Please try again.');
                }
            })
            .catch(error => {
                addLog('❌ ERROR: ' + error.message, 'error');
                alert('An error occurred during sign-in: ' + error.message);
            });
        }
        
        window.onload = function() {
            addLog('Initializing Google Sign-In...', 'info');
            
            if (typeof google === 'undefined') {
                addLog('❌ ERROR: Google library not loaded!', 'error');
                addLog('Please check your internet connection', 'error');
                return;
            }
            
            addLog('✅ Google library loaded successfully', 'success');
            
            try {
                const clientId = "174568861864-ed5p6jgvvbuc6gjbnrkvv5ki8h9vfkng.apps.googleusercontent.com";
                addLog(`Using Client ID: ${clientId}`, 'info');
                
                google.accounts.id.initialize({
                    client_id: clientId,
                    callback: handleCredentialResponse,
                    auto_select: false,
                    cancel_on_tap_outside: true
                });
                
                addLog('✅ Google Sign-In initialized', 'success');
                
                google.accounts.id.renderButton(
                    document.getElementById("googleSignInDiv"),
                    { 
                        type: "standard",
                        theme: "outline", 
                        size: "large",
                        text: "signin_with",
                        shape: "rectangular",
                        logo_alignment: "left",
                        width: 350
                    }
                );
                
                addLog('✅ Google Sign-In button rendered', 'success');
                addLog('Ready! Click the button to test sign-in.', 'success');
                
            } catch (error) {
                addLog('❌ ERROR initializing: ' + error.message, 'error');
                console.error('Full error:', error);
            }
        };
        
        // Detect if script fails to load
        setTimeout(() => {
            if (typeof google === 'undefined') {
                addLog('❌ TIMEOUT: Google library failed to load after 5 seconds', 'error');
                addLog('Check if https://accounts.google.com/gsi/client is accessible', 'error');
            }
        }, 5000);
    </script>
</body>
</html>
