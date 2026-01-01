<?php
/**
 * Helper script to generate Laravel APP_KEY
 * Use this if you don't have SSH access to run "php artisan key:generate"
 * 
 * Usage:
 * 1. Upload this file to your hosting
 * 2. Open it in browser: https://your-domain.com/generate-key.php
 * 3. Copy the generated key
 * 4. Paste it in .env file as APP_KEY=base64:...
 * 5. DELETE this file for security!
 */

function generateRandomKey($length = 32)
{
    $bytes = random_bytes($length);
    return 'base64:' . base64_encode($bytes);
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Laravel APP_KEY</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .key-box {
            background: #f8f9fa;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #2c3e50;
        }
        
        .steps {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .steps h3 {
            color: #1976d2;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .steps ol {
            margin-left: 20px;
            color: #333;
        }
        
        .steps li {
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .warning strong {
            color: #856404;
            display: block;
            margin-bottom: 5px;
        }
        
        .warning p {
            color: #856404;
            margin: 0;
            font-size: 14px;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .copy-btn {
            width: 100%;
            margin-top: 10px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            display: none;
        }
        
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Generate Laravel APP_KEY</h1>
        <p class="subtitle">Helper script for Anesi Kassa Laravel deployment</p>
        
        <div class="key-box" id="keyBox">
            <?php echo generateRandomKey(); ?>
        </div>
        
        <button class="btn copy-btn" onclick="copyKey()">📋 Copy Key to Clipboard</button>
        <div class="success" id="successMessage">✓ Key copied to clipboard!</div>
        
        <div class="steps">
            <h3>📝 Next Steps:</h3>
            <ol>
                <li>Copy the key above (click the button)</li>
                <li>Open your <code>.env</code> file on the server</li>
                <li>Find the line <code>APP_KEY=</code></li>
                <li>Paste the copied key after the equals sign</li>
                <li>Save the <code>.env</code> file</li>
                <li><strong>DELETE this generate-key.php file immediately for security!</strong></li>
            </ol>
        </div>
        
        <div class="warning">
            <strong>⚠️ IMPORTANT SECURITY WARNING</strong>
            <p>Delete this file immediately after copying the key! This file should not remain on your production server.</p>
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <button class="btn" onclick="location.reload()">🔄 Generate New Key</button>
        </div>
    </div>
    
    <script>
        function copyKey() {
            const keyBox = document.getElementById('keyBox');
            const text = keyBox.textContent.trim();
            
            navigator.clipboard.writeText(text).then(function() {
                const successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';
                
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000);
            }, function(err) {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                const successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';
                
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000);
            });
        }
    </script>
</body>
</html>
