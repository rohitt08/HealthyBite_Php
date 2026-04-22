<?php
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include_once 'includes/db.php';

$error_login = '';
$error_register = '';
$success_register = '';
$active_tab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $active_tab = 'register';
    $name = "";
    if (isset($_POST['reg_name'])) { $name = trim($_POST['reg_name']); }
    
    $email = "";
    if (isset($_POST['reg_email'])) { $email = strtolower(trim($_POST['reg_email'])); }
    
    $password = isset($_POST['reg_password']) ? $_POST['reg_password'] : '';
    $confirm  = isset($_POST['reg_confirm']) ? $_POST['reg_confirm'] : '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error_register = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_register = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error_register = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error_register = 'Passwords do not match.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error_register = 'An account with this email already exists. Please log in.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $name, $email, $hashed);
            if ($insert->execute()) {
                $success_register = 'Account created successfully! You can now log in.';
                $active_tab = 'login';
            } else {
                $error_register = 'Registration failed. Please try again.';
            }
            $insert->close();
        }
        $check->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $active_tab = 'login';
    $email = "";
    if (isset($_POST['email'])) { $email = strtolower(trim($_POST['email'])); }
    
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $error_login = 'Please fill in both fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($uid, $uname, $uhash);
        $stmt->fetch();

        if ($stmt->num_rows === 0 || !password_verify($password, $uhash)) {
            $error_login = 'Invalid email or password. Please try again.';
        } else {
            $_SESSION['user_id']   = $uid;
            $_SESSION['user_name'] = $uname;
            $_SESSION['user_email']= $email;
            session_regenerate_id(true);
            header('Location: index.php');
            exit;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrivo | Login & Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <meta name="description" content="Login or create your Nutrivo account to start ordering fresh, healthy meals.">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #0f1923 0%, #1a2e1a 60%, #0f2d1a 100%);
        }

        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand a {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-family: 'Merienda', cursive;
            font-size: 1.8rem;
            font-weight: 700;
            color: #4ade80;
            text-decoration: none;
        }

        .auth-brand img {
            height: 42px;
            width: 42px;
            object-fit: contain;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(74, 222, 128, 0.15);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
        }

        /* Tab Switcher */
        .tab-switcher {
            display: flex;
            background: rgba(0,0,0,0.3);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 2rem;
        }

        .tab-btn {
            flex: 1;
            padding: 0.65rem 1rem;
            border: none;
            background: transparent;
            color: #8899a6;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 9px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: #0f1923;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(74, 222, 128, 0.3);
        }

        /* Forms */
        .auth-form { display: none; }
        .auth-form.active { display: block; }

        .auth-form h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #f0fdf4;
            margin-bottom: 0.4rem;
        }

        .auth-form p.sub {
            color: #8899a6;
            font-size: 0.875rem;
            margin-bottom: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #a3c9a8;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(74, 222, 128, 0.15);
            border-radius: 10px;
            color: #f0fdf4;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
            box-sizing: border-box;
        }

        .form-group input::placeholder { color: #4a5568; }

        .form-group input:focus {
            outline: none;
            border-color: #4ade80;
            background: rgba(74, 222, 128, 0.07);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.12);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.35);
            color: #86efac;
        }

        .auth-submit {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #4ade80, #22c55e);
            border: none;
            border-radius: 12px;
            color: #0f1923;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            margin-top: 0.5rem;
            box-shadow: 0 4px 20px rgba(74, 222, 128, 0.3);
            letter-spacing: 0.02em;
        }

        .auth-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(74, 222, 128, 0.45);
        }

        .auth-submit:active { transform: translateY(0); }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.8rem 0;
            font-size: 0.82rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #8899a6;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] { accent-color: #4ade80; }
        .forgot-password { color: #4ade80; text-decoration: none; }
        .forgot-password:hover { text-decoration: underline; }

        .switch-prompt {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #8899a6;
        }

        .switch-prompt a {
            color: #4ade80;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .switch-prompt a:hover { text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.2rem 0;
            color: #4a5568;
            font-size: 0.8rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        @media (max-width: 500px) {
            .auth-card { padding: 1.8rem 1.2rem; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-container">

        <div class="auth-brand">
            <a href="index.php">
                <img src="assets/logo.png" alt="Nutrivo Logo"> Nutrivo
            </a>
        </div>

        <div class="auth-card">
            <div class="tab-switcher" role="tablist">
                <button class="tab-btn <?php echo $active_tab === 'login' ? 'active' : ''; ?>" 
                        id="tab-login" role="tab" onclick="switchTab('login')">Sign In</button>
                <button class="tab-btn <?php echo $active_tab === 'register' ? 'active' : ''; ?>" 
                        id="tab-register" role="tab" onclick="switchTab('register')">Create Account</button>
            </div>

            <div class="auth-form <?php echo $active_tab === 'login' ? 'active' : ''; ?>" id="form-login">
                <h2>Welcome back 👋</h2>
                <p class="sub">Sign in to access your account and orders.</p>

                <?php if ($error_login): ?>
                    <div class="alert alert-error">⚠ <?php echo htmlspecialchars($error_login); ?></div>
                <?php endif; ?>

                <?php if ($success_register): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_register); ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="login-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                    <button type="submit" class="auth-submit">Sign In →</button>
                </form>

                <div class="switch-prompt">
                    New to Nutrivo? <a onclick="switchTab('register')">Create a free account</a>
                </div>
            </div>

            <div class="auth-form <?php echo $active_tab === 'register' ? 'active' : ''; ?>" id="form-register">
                <h2>Join Nutrivo 🌱</h2>
                <p class="sub">Create your account and start eating healthy today.</p>

                <?php if ($error_register): ?>
                    <div class="alert alert-error">⚠ <?php echo htmlspecialchars($error_register); ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="reg_name">Full Name</label>
                        <input type="text" id="reg_name" name="reg_name" required 
                               value="<?php echo htmlspecialchars($_POST['reg_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="reg_email">Email Address</label>
                        <input type="email" id="reg_email" name="reg_email" required 
                               value="<?php echo htmlspecialchars($_POST['reg_email'] ?? ''); ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="reg_password">Password</label>
                            <input type="password" id="reg_password" name="reg_password" required>
                        </div>
                        <div class="form-group">
                            <label for="reg_confirm">Confirm Password</label>
                            <input type="password" id="reg_confirm" name="reg_confirm" required>
                        </div>
                    </div>
                    <button type="submit" class="auth-submit">Create Account →</button>
                </form>

                <div class="switch-prompt">
                    Already have an account? <a onclick="switchTab('login')">Sign in here</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('form-' + tab).classList.add('active');
    }

    const activeTab = "<?php echo $active_tab; ?>";
    switchTab(activeTab);
</script>
</body>
</html>
