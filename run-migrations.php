<?php
/**
 * Helper script to run database migrations
 * Use this if you don't have SSH access to run "php artisan migrate"
 * 
 * IMPORTANT SECURITY:
 * 1. Only use this for initial setup
 * 2. DELETE this file immediately after use
 * 3. Never leave it on production server
 * 
 * Usage:
 * 1. Make sure .env is configured with correct DB credentials
 * 2. Upload this file to your hosting root directory
 * 3. Open it in browser: https://your-domain.com/run-migrations.php
 * 4. Click "Run Migrations" button
 * 5. DELETE this file immediately after successful migration!
 */

// Security check - change this password before using!
define('MIGRATION_PASSWORD', 'change_this_password_123');

$password_correct = false;
$output = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === MIGRATION_PASSWORD) {
        $password_correct = true;
        
        // Change to Laravel root directory
        chdir(__DIR__);
        
        // Check if artisan file exists
        if (!file_exists('artisan')) {
            $error = 'Error: artisan file not found. Make sure this script is in the Laravel root directory.';
        } else {
            // Run migrations
            ob_start();
            
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                
                switch ($action) {
                    case 'migrate':
                        echo "Running migrations...\n\n";
                        passthru('php artisan migrate --force 2>&1', $return_var);
                        break;
                        
                    case 'seed':
                        echo "Running seeders...\n\n";
                        passthru('php artisan db:seed --force 2>&1', $return_var);
                        break;
                        
                    case 'migrate_fresh':
                        echo "WARNING: This will drop all tables and recreate them!\n\n";
                        passthru('php artisan migrate:fresh --force 2>&1', $return_var);
                        break;
                        
                    case 'migrate_seed':
                        echo "Running migrations and seeders...\n\n";
                        passthru('php artisan migrate --force 2>&1', $return_var);
                        echo "\n\nRunning seeders...\n\n";
                        passthru('php artisan db:seed --force 2>&1', $return_var);
                        break;
                }
            }
            
            $output = ob_get_clean();
            
            if ($return_var !== 0) {
                $error = 'Command returned error code: ' . $return_var;
            }
        }
    } else {
        $error = 'Incorrect password!';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run Database Migrations</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
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
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        
        .warning strong {
            color: #856404;
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .warning ul {
            margin-left: 20px;
            color: #856404;
        }
        
        .warning li {
            margin-bottom: 5px;
        }
        
        .danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        
        .danger strong {
            color: #721c24;
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .danger p {
            color: #721c24;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.2s;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .btn-danger:hover {
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
        }
        
        .output {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 500px;
            overflow-y: auto;
            margin-top: 20px;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            margin-top: 20px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            margin-top: 20px;
        }
        
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
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
        <h1>🗄️ Run Database Migrations</h1>
        <p class="subtitle">Helper script for Anesi Kassa Laravel deployment</p>
        
        <div class="danger">
            <strong>⚠️ CRITICAL SECURITY WARNING</strong>
            <p>
                This file is a security risk! You MUST:<br>
                1. Change the password in the script before first use<br>
                2. DELETE this file immediately after running migrations<br>
                3. Never leave this file on production server
            </p>
        </div>
        
        <?php if (!$password_correct): ?>
        <div class="warning">
            <strong>⚠️ Before You Begin:</strong>
            <ul>
                <li>Make sure your <code>.env</code> file is configured with correct database credentials</li>
                <li>Make sure <code>APP_KEY</code> is set in <code>.env</code></li>
                <li>Backup your database if it contains important data</li>
                <li>Change MIGRATION_PASSWORD in this file before using!</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="password">Enter Migration Password:</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter the password set in this script">
            </div>
            
            <div class="actions">
                <button type="submit" name="action" value="migrate" class="btn">
                    ▶️ Run Migrations Only
                </button>
                
                <button type="submit" name="action" value="migrate_seed" class="btn">
                    ▶️ Run Migrations + Seeders
                </button>
                
                <button type="submit" name="action" value="seed" class="btn">
                    🌱 Run Seeders Only
                </button>
                
                <button type="submit" name="action" value="migrate_fresh" class="btn btn-danger"
                        onclick="return confirm('⚠️ WARNING: This will DROP ALL TABLES and recreate them! All data will be lost! Are you sure?')">
                    🗑️ Fresh Migration (Dangerous!)
                </button>
            </div>
        </form>
        
        <?php if ($error && !$password_correct): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <?php if ($error): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($output): ?>
        <div class="success">
            <strong>✓ Command executed successfully!</strong>
        </div>
        
        <div class="output"><?php echo htmlspecialchars($output); ?></div>
        
        <div class="danger" style="margin-top: 30px;">
            <strong>🗑️ DELETE THIS FILE NOW!</strong>
            <p>
                Migrations completed. For security, you MUST delete this file immediately:<br>
                <code>run-migrations.php</code>
            </p>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="?" class="btn">◀️ Back</a>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>
