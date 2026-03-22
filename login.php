<?php
session_start();
require 'db.php';

$error = "";
$success = "";
$step = 1; // 1 = login, 2 = enter email, 3 = create new password

// Check URL for step
if (isset($_GET['forgot'])) {
    $step = intval($_GET['forgot']);
}

// STEP 2: Check email
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_email'])) {
    $email = trim($_POST['reset_email']);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['reset_email'] = $email;
        header("Location: login.php?forgot=3");
        exit;
    } else {
        $error = "Email not found";
        $step = 2;
    }
}

// STEP 3: Set new password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_password'])) {
    if (isset($_SESSION['reset_email'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed, $_SESSION['reset_email']);
                
                if ($stmt->execute()) {
                    unset($_SESSION['reset_email']);
                    $success = "Password reset successful! You can now login.";
                    $step = 1;
                } else {
                    $error = "Failed to reset password";
                    $step = 3;
                }
            } else {
                $error = "Password must be at least 6 characters";
                $step = 3;
            }
        } else {
            $error = "Passwords do not match";
            $step = 3;
        }
    }
}

// Regular Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $password_valid = false;
        
        if (password_verify($password, $user['password'])) {
            $password_valid = true;
        } else if ($user['password'] === $password) {
            $password_valid = true;
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update_hash = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_hash->bind_param("si", $hashed, $user['id']);
            $update_hash->execute();
        }
        
        if ($password_valid) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
            $_SESSION['role'] = $user['role'];
            
            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            
            if ($user['role'] === 'admin') {
                header("Location: admindash.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Email not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $step === 1 ? 'Login' : 'Reset Password'; ?> | ReadQuest</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #0d1117; --panel: #161b27; --surface: #1e2535;
    --border: rgba(139, 180, 248, 0.08); --accent: #7c6af7; --accent2: #38d9c0;
    --text: #e2e8f8; --muted: #6b7a9e; --input-bg: #1a2133;
    --input-border: rgba(139, 180, 248, 0.12); --glow: rgba(124, 106, 247, 0.22);
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Nunito', sans-serif; background: var(--bg); min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; }
.brand-badge { display: flex; align-items: center; gap: 14px; text-decoration: none; margin-bottom: 40px; }
.brand-badge .book-logo { width: 52px; height: 52px; }
.brand-badge span { font-family: 'Bebas Neue', sans-serif; font-size: 2.2rem; letter-spacing: 5px; color: var(--text); text-shadow: 0 0 30px rgba(124,106,247,0.4); }
.form-box { width: 100%; max-width: 520px; animation: formIn 0.6s ease both; }
@keyframes formIn { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
.form-box::before { content: ''; display: block; height: 3px; background: linear-gradient(90deg, var(--accent), var(--accent2), transparent); border-radius: 2px 2px 0 0; }
.form-card { background: var(--panel); border: 1px solid var(--border); border-top: none; border-radius: 0 0 14px 14px; padding: 40px 48px 36px; }
.form-heading { margin-bottom: 28px; }
.form-heading h2 { font-family: 'Bebas Neue', sans-serif; font-size: 2rem; letter-spacing: 2px; color: var(--text); margin-bottom: 4px; }
.form-heading p { color: var(--muted); font-size: 0.88rem; }
.error-msg { background: rgba(232, 58, 58, 0.12); border: 1px solid rgba(232, 58, 58, 0.3); color: #ff7070; padding: 11px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 0.87rem; display: flex; align-items: center; gap: 8px; }
.success-msg { background: rgba(56, 217, 192, 0.12); border: 1px solid rgba(56, 217, 192, 0.3); color: #6ee7b7; padding: 11px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 0.87rem; display: flex; align-items: center; gap: 8px; }
.field { margin-bottom: 20px; }
.field label { display: block; font-size: 0.82rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 7px; }
.input-wrap { position: relative; }
.input-wrap i.icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 0.95rem; pointer-events: none; }
.input-wrap input { width: 100%; padding: 13px 42px; background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 8px; color: var(--text); font-family: 'Nunito', sans-serif; font-size: 0.95rem; outline: none; transition: all 0.25s; }
.input-wrap input:focus { border-color: var(--accent); background: #2e2e3e; box-shadow: 0 0 0 3px var(--glow); }
.toggle-pass { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); cursor: pointer; z-index: 2; }
.toggle-pass:hover { color: var(--accent); }
.forgot-row { text-align: right; margin: -8px 0 20px; }
.forgot-row a { color: var(--accent); font-size: 0.83rem; text-decoration: none; font-weight: 600; }
.forgot-row a:hover { color: var(--accent2); }
.btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #fff; border: none; border-radius: 8px; font-family: 'Bebas Neue', sans-serif; font-size: 1.15rem; letter-spacing: 2px; cursor: pointer; box-shadow: 0 4px 20px var(--glow); display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 8px; }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(124, 106, 247, 0.45); }
.btn-back { width: 100%; padding: 12px; background: transparent; color: var(--muted); border: 1px solid var(--border); border-radius: 8px; font-size: 0.9rem; cursor: pointer; margin-top: 15px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-back:hover { border-color: var(--accent); color: var(--accent); }
.divider { display: flex; align-items: center; gap: 14px; margin: 24px 0 20px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
.divider span { color: var(--muted); font-size: 0.78rem; text-transform: uppercase; }
.switch-link { text-align: center; color: var(--muted); font-size: 0.88rem; }
.switch-link a { color: var(--accent); font-weight: 700; text-decoration: none; }
.switch-link a:hover { color: var(--accent2); }
.step-indicator { display: flex; gap: 10px; justify-content: center; margin-bottom: 25px; }
.step-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--border); }
.step-dot.active { background: var(--accent); box-shadow: 0 0 10px var(--glow); }
@media (max-width: 600px) { .form-card { padding: 28px 22px 24px; } .brand-badge span { font-size: 1.7rem; } }
</style>
</head>
<body>

<a href="index.php" class="brand-badge">
    <svg class="book-logo" viewBox="0 0 52 52" fill="none"><circle cx="26" cy="26" r="24" stroke="url(#g1)" stroke-width="2.5"/><rect x="18" y="13" width="4" height="26" rx="1.5" fill="url(#g2)"/><rect x="22" y="13" width="13" height="26" rx="2" fill="url(#g3)"/><defs><linearGradient id="g1"><stop stop-color="#7c6af7"/><stop offset="1" stop-color="#38d9c0"/></linearGradient><linearGradient id="g2"><stop stop-color="#9d8fff"/><stop offset="1" stop-color="#5a4fd4"/></linearGradient><linearGradient id="g3"><stop stop-color="#2a2f4a"/><stop offset="1" stop-color="#1a1e35"/></linearGradient></defs></svg>
    <span>ReadQuest</span>
</a>

<div class="form-box"><div class="form-card">

<?php if ($step === 1): ?>
<!-- LOGIN -->
<div class="form-heading"><h2>Sign In</h2><p>Welcome back — continue your reading journey</p></div>
<?php if ($error): ?><div class="error-msg"><i class="fas fa-circle-exclamation"></i><span><?= htmlspecialchars($error) ?></span></div><?php endif; ?>
<?php if ($success): ?><div class="success-msg"><i class="fas fa-check-circle"></i><span><?= htmlspecialchars($success) ?></span></div><?php endif; ?>
<form method="POST">
<div class="field"><label for="email">Email</label><div class="input-wrap"><input type="email" name="email" id="email" placeholder="your@email.com" required><i class="fas fa-envelope icon"></i></div></div>
<div class="field"><label for="password">Password</label><div class="input-wrap"><input type="password" name="password" id="password" placeholder="••••••••" required><i class="fas fa-lock icon"></i><i class="fas fa-eye toggle-pass" onclick="togglePass('password', this)"></i></div></div>
<div class="forgot-row"><a href="login.php?forgot=2">Forgot Password?</a></div>
<button type="submit" class="btn-submit"><i class="fas fa-sign-in-alt"></i><span>Sign In</span></button>
</form>
<div class="divider"><span>or</span></div>
<div class="switch-link">Don't have an account? <a href="register.php">Create one now</a></div>

<?php elseif ($step === 2): ?>
<!-- STEP 2: Email -->
<div class="form-heading"><h2>Reset Password</h2><p>Step 1: Enter your email</p></div>
<div class="step-indicator"><div class="step-dot"></div><div class="step-dot active"></div><div class="step-dot"></div></div>
<?php if ($error): ?><div class="error-msg"><i class="fas fa-circle-exclamation"></i><span><?= htmlspecialchars($error) ?></span></div><?php endif; ?>
<form method="POST">
<div class="field"><label for="reset_email">Your Email</label><div class="input-wrap"><input type="email" name="reset_email" id="reset_email" placeholder="your@email.com" required><i class="fas fa-envelope icon"></i></div></div>
<button type="submit" class="btn-submit"><i class="fas fa-arrow-right"></i><span>Continue</span></button>
</form>
<a href="login.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Login</a>

<?php elseif ($step === 3): ?>
<!-- STEP 3: New Password -->
<div class="form-heading"><h2>Reset Password</h2><p>Step 2: Create new password</p></div>
<div class="step-indicator"><div class="step-dot"></div><div class="step-dot"></div><div class="step-dot active"></div></div>
<?php if ($error): ?><div class="error-msg"><i class="fas fa-circle-exclamation"></i><span><?= htmlspecialchars($error) ?></span></div><?php endif; ?>
<form method="POST">
<div class="field"><label for="new_password">New Password</label><div class="input-wrap"><input type="password" name="new_password" id="new_password" placeholder="At least 6 characters" required><i class="fas fa-lock icon"></i><i class="fas fa-eye toggle-pass" onclick="togglePass('new_password', this)"></i></div></div>
<div class="field"><label for="confirm_password">Confirm Password</label><div class="input-wrap"><input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter password" required><i class="fas fa-lock icon"></i><i class="fas fa-eye toggle-pass" onclick="togglePass('confirm_password', this)"></i></div></div>
<button type="submit" class="btn-submit"><i class="fas fa-check"></i><span>Reset Password</span></button>
</form>
<a href="login.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Login</a>
<?php endif; ?>

</div></div>

<script>
function togglePass(id, icon) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    icon.classList.toggle('fa-eye', isText);
    icon.classList.toggle('fa-eye-slash', !isText);
}
</script>
</body>
</html>