<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple database connection
$host = 'localhost';
$dbname = 'pr_inventory';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';
$success = '';

if ($_POST) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    if ($user && $pass) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM fict_users WHERE fu_username = ? AND fu_password = ?");
            $stmt->execute([$user, $pass]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $_SESSION['user']['login'] = true;
                $_SESSION['user']['data'] = $userData;
                $success = "Login successful! User found: " . $userData['fu_username'];
            } else {
                $error = "Invalid username or password";
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please enter both username and password";
    }
}

// Get locations
$locations = [];
try {
    $stmt = $pdo->query("SELECT * FROM fict_location ORDER BY flo_address");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error .= " Location error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Login Test</title>
</head>
<body>
    <h1>Login Test</h1>
    
    <?php if ($error): ?>
        <div style="color: red; padding: 10px; border: 1px solid red; margin: 10px 0;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div style="color: green; padding: 10px; border: 1px solid green; margin: 10px 0;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <p>
            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </p>
        <p>
            <label>Location:</label><br>
            <select name="location">
                <option value="">Select location...</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?php echo htmlspecialchars($loc['flo_code']); ?>">
                        <?php echo htmlspecialchars($loc['flo_address']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <button type="submit">Login</button>
        </p>
    </form>
    
    <h3>Debug Info:</h3>
    <p>Available users in database:</p>
    <?php
    try {
        $stmt = $pdo->query("SELECT fu_code, fu_username FROM fict_users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($users as $u) {
            echo "<li>" . htmlspecialchars($u['fu_username']) . " (ID: " . htmlspecialchars($u['fu_code']) . ")</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "Error getting users: " . $e->getMessage();
    }
    ?>
    
    <p>Available locations:</p>
    <ul>
        <?php foreach ($locations as $loc): ?>
            <li><?php echo htmlspecialchars($loc['flo_address']); ?> (<?php echo htmlspecialchars($loc['flo_code']); ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>