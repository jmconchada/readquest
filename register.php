<?php
require_once __DIR__ . '/db.php';

if (!isset($conn)) {
    die("Database connection not found! Check db.php path.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $contact          = trim($_POST['contact_number']);
    $area             = trim($_POST['area']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms            = isset($_POST['terms']);

    if (!$terms) {
        $error = "You must agree to the Terms and Conditions";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $password_hash   = password_hash($password, PASSWORD_DEFAULT);
        $default_profile = 'assets/images/profile_picture.png';

        $stmt = $conn->prepare("
            INSERT INTO users (name, email, contact_number, area, password, profile_picture, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("ssssss", $name, $email, $contact, $area, $password_hash, $default_profile);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Email already exists!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | ReadQuest</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --bg:           #0d1117;
    --panel:        #161b27;
    --surface:      #1e2535;
    --border:       rgba(139, 180, 248, 0.08);
    --accent:       #7c6af7;
    --accent2:      #38d9c0;
    --accent-green: #38d9c0;
    --text:         #e2e8f8;
    --muted:        #6b7a9e;
    --input-bg:     #1a2133;
    --input-border: rgba(139, 180, 248, 0.12);
    --glow:         rgba(124, 106, 247, 0.22);
    --glow-green:   rgba(56, 217, 192, 0.2);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Nunito', sans-serif;
    background: var(--bg);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow-x: hidden;
    padding: 40px 20px;
}

/* ── Top brand — centered ── */
.brand-badge {
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    margin-bottom: 40px;
}

.brand-badge .book-logo {
    width: 52px;
    height: 52px;
    flex-shrink: 0;
}

.brand-badge span {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2.2rem;
    letter-spacing: 5px;
    color: var(--text);
    text-shadow: 0 0 30px rgba(124,106,247,0.4);
}

@keyframes formIn {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.form-box {
    width: 100%;
    max-width: 560px;
    animation: formIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.form-box::before {
    content: '';
    display: block;
    height: 3px;
    background: linear-gradient(90deg, var(--accent), var(--accent2), transparent);
    border-radius: 2px 2px 0 0;
}

.form-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-top: none;
    border-radius: 0 0 14px 14px;
    padding: 32px 36px 28px;
}

.form-heading {
    margin-bottom: 24px;
}

.form-heading h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    letter-spacing: 2px;
    color: var(--text);
    margin-bottom: 4px;
}

.form-heading p {
    color: var(--muted);
    font-size: 0.88rem;
}

/* Required note */
.required-note {
    font-size: 0.78rem;
    color: var(--muted);
    text-align: right;
    margin-bottom: 18px;
}

.required-note span { color: var(--accent); }

/* Two-column grid for optional fields */
.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* Error */
.error-msg {
    background: rgba(232, 58, 58, 0.12);
    border: 1px solid rgba(232, 58, 58, 0.3);
    color: #ff7070;
    padding: 11px 14px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 0.87rem;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: shake 0.4s ease;
}

@keyframes shake {
    0%,100% { transform: translateX(0); }
    25%      { transform: translateX(-6px); }
    75%      { transform: translateX(6px); }
}

/* Fields */
.field { margin-bottom: 16px; }

.field label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}

.field label .req { color: var(--accent); margin-left: 2px; }
.field label .opt { color: #4a4a5e; font-weight: 400; font-size: 0.72rem; }

.input-wrap { position: relative; }

.input-wrap i.icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 0.88rem;
    pointer-events: none;
    transition: color 0.2s;
}

.input-wrap input {
    width: 100%;
    padding: 12px 38px;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: 8px;
    color: var(--text);
    font-family: 'Nunito', sans-serif;
    font-size: 0.92rem;
    outline: none;
    transition: all 0.25s;
}

.input-wrap input::placeholder { color: #3a3a4e; }

.input-wrap input:focus {
    border-color: var(--accent);
    background: #2e2e3e;
    box-shadow: 0 0 0 3px var(--glow);
}

.input-wrap:focus-within i.icon { color: var(--accent); }

.toggle-pass {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    cursor: pointer;
    font-size: 0.9rem;
    transition: color 0.2s;
    z-index: 2;
}

.toggle-pass:hover { color: var(--accent); }

/* Password strength */
.strength-bar {
    margin-top: 6px;
    height: 3px;
    border-radius: 2px;
    background: var(--surface);
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    border-radius: 2px;
    transition: all 0.3s;
    width: 0%;
}

.strength-label {
    font-size: 0.74rem;
    margin-top: 4px;
    color: var(--muted);
    min-height: 16px;
}

/* Terms */
.terms-wrap {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 16px 0 20px;
    padding: 12px 14px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: border-color 0.25s, background 0.25s;
}

.terms-wrap.checked {
    border-color: rgba(58, 205, 122, 0.35);
    background: rgba(58, 205, 122, 0.05);
}

.terms-wrap input[type="checkbox"] {
    margin-top: 2px;
    width: 17px;
    height: 17px;
    accent-color: var(--accent-green);
    cursor: pointer;
    flex-shrink: 0;
}

.terms-wrap label {
    font-size: 0.83rem;
    color: var(--muted);
    line-height: 1.5;
    cursor: pointer;
    text-transform: none;
    letter-spacing: 0;
    font-weight: 400;
}

.terms-wrap label a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
}

.terms-wrap label a:hover { color: var(--accent2); }

/* Submit */
.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--accent-green), #2db86a);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.15rem;
    letter-spacing: 2px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px var(--glow-green);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(56, 217, 192, 0.4);
}

.btn-submit:active { transform: translateY(0); }

/* Divider */
.divider {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 22px 0 18px;
}

.divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

.divider span {
    color: var(--muted);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.switch-link {
    text-align: center;
    color: var(--muted);
    font-size: 0.88rem;
}

.switch-link a {
    color: var(--accent);
    font-weight: 700;
    text-decoration: none;
    transition: color 0.2s;
}

.switch-link a:hover { color: var(--accent2); }

/* ── Responsive ── */
@media (max-width: 600px) {
    .form-card { padding: 26px 20px 22px; }
    .field-row { grid-template-columns: 1fr; }
    .brand-badge span { font-size: 1.7rem; }
}
</style>
</head>
<body>

<!-- Top brand with inline book SVG -->
<a href="index.php" class="brand-badge">
    <svg class="book-logo" viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="26" cy="26" r="24" stroke="url(#ringGrad2)" stroke-width="2.5" opacity="0.9"/>
        <rect x="18" y="13" width="4" height="26" rx="1.5" fill="url(#spineGrad2)"/>
        <rect x="22" y="13" width="13" height="26" rx="2" fill="url(#coverGrad2)"/>
        <rect x="21" y="14" width="1.5" height="24" fill="#c8bfff" opacity="0.25"/>
        <line x1="25" y1="20" x2="32" y2="20" stroke="#a89ff0" stroke-width="1.2" stroke-linecap="round" opacity="0.7"/>
        <line x1="25" y1="24" x2="32" y2="24" stroke="#a89ff0" stroke-width="1.2" stroke-linecap="round" opacity="0.7"/>
        <line x1="25" y1="28" x2="32" y2="28" stroke="#a89ff0" stroke-width="1.2" stroke-linecap="round" opacity="0.7"/>
        <line x1="25" y1="32" x2="30" y2="32" stroke="#a89ff0" stroke-width="1.2" stroke-linecap="round" opacity="0.5"/>
        <circle cx="34" cy="16" r="1.5" fill="#38d9c0" opacity="0.9"/>
        <defs>
            <linearGradient id="ringGrad2" x1="0" y1="0" x2="52" y2="52" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#7c6af7"/>
                <stop offset="100%" stop-color="#38d9c0"/>
            </linearGradient>
            <linearGradient id="spineGrad2" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%" stop-color="#9d8fff"/>
                <stop offset="100%" stop-color="#5a4fd4"/>
            </linearGradient>
            <linearGradient id="coverGrad2" x1="0" y1="0" x2="1" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%" stop-color="#2a2f4a"/>
                <stop offset="100%" stop-color="#1a1e35"/>
            </linearGradient>
        </defs>
    </svg>
    <span>ReadQuest</span>
</a>

<!-- Centered form -->
<div class="form-box">
        <div class="form-card">
            <div class="form-heading">
                <h2>Create Account</h2>
                <p>Join thousands of readers on ReadQuest</p>
            </div>

            <div class="required-note"><span>*</span> Required fields</div>

            <?php if ($error): ?>
            <div class="error-msg">
                <i class="fas fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return handleSubmit(this)" id="registerForm">

                <div class="field">
                    <label for="name">Full Name <span class="req">*</span></label>
                    <div class="input-wrap">
                        <input type="text" name="name" id="name"
                               placeholder="Your full name" required autocomplete="name">
                        <i class="fas fa-user icon"></i>
                    </div>
                </div>

                <div class="field">
                    <label for="email">Email Address <span class="req">*</span></label>
                    <div class="input-wrap">
                        <input type="email" name="email" id="email"
                               placeholder="your@email.com" required autocomplete="email">
                        <i class="fas fa-envelope icon"></i>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="contact_number">Contact <span class="opt">(optional)</span></label>
                        <div class="input-wrap">
                            <input type="text" name="contact_number" id="contact_number"
                                   placeholder="+63 xxx xxx" autocomplete="tel">
                            <i class="fas fa-phone icon"></i>
                        </div>
                    </div>

                    <div class="field">
                        <label for="area">Location <span class="opt">(optional)</span></label>
                        <div class="input-wrap">
                            <input type="text" name="area" id="area"
                                   placeholder="City / Area" autocomplete="address-level2">
                            <i class="fas fa-map-marker-alt icon"></i>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password <span class="req">*</span></label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="password"
                               placeholder="••••••••" required autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <i class="fas fa-lock icon"></i>
                        <i class="fas fa-eye toggle-pass" onclick="togglePass('password', this)"></i>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                    <div class="strength-label" id="strengthLabel"></div>
                </div>

                <div class="field">
                    <label for="confirm_password">Confirm Password <span class="req">*</span></label>
                    <div class="input-wrap">
                        <input type="password" name="confirm_password" id="confirm_password"
                               placeholder="••••••••" required autocomplete="new-password">
                        <i class="fas fa-lock icon"></i>
                        <i class="fas fa-eye toggle-pass" onclick="togglePass('confirm_password', this)"></i>
                    </div>
                </div>

                <div class="terms-wrap" id="termsWrap">
                    <input type="checkbox" name="terms" id="terms" required
                           onchange="toggleTerms(this)">
                    <label for="terms">
                        I agree to the
                        <a href="terms.php" target="_blank">Terms &amp; Conditions</a>
                        and <a href="privacy.php" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn-submit" id="registerBtn">
                    <i class="fas fa-user-plus"></i>
                    <span>Create Account</span>
                </button>
            </form>

            <div class="divider"><span>or</span></div>

            <div class="switch-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>
</div><!-- /form-box -->

<script>
function togglePass(id, icon) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    icon.classList.toggle('fa-eye', isText);
    icon.classList.toggle('fa-eye-slash', !isText);
}

function toggleTerms(cb) {
    const wrap = document.getElementById('termsWrap');
    wrap.classList.toggle('checked', cb.checked);
}

function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (!val) { fill.style.width = '0%'; label.textContent = ''; return; }

    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;

    const levels = [
        { w: '20%', color: '#e74c3c', text: 'Too short' },
        { w: '40%', color: '#e67e22', text: 'Weak' },
        { w: '60%', color: '#f1c40f', text: 'Fair' },
        { w: '80%', color: '#2ecc71', text: 'Strong' },
        { w: '100%', color: '#27ae60', text: 'Very strong!' },
    ];
    const lvl = levels[Math.min(score, 4)];
    fill.style.width = lvl.w;
    fill.style.background = lvl.color;
    label.style.color = lvl.color;
    label.textContent = lvl.text;
}

function handleSubmit(form) {
    const pass    = form.password.value;
    const confirm = form.confirm_password.value;
    const terms   = form.terms.checked;

    if (!terms) {
        alert('You must agree to the Terms and Conditions.');
        return false;
    }
    if (pass.length < 6) {
        alert('Password must be at least 6 characters.');
        return false;
    }
    if (pass !== confirm) {
        alert('Passwords do not match.');
        return false;
    }

    const btn = document.getElementById('registerBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Creating account...</span>';
    btn.style.opacity = '0.75';
    btn.style.pointerEvents = 'none';
    return true;
}
</script>
</body>
</html>