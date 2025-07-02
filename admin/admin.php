<?php
// /admin/admin.php - Admin Login with Bootstrap and Modal Registration
session_start();
include('../includes/config.php');  // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to check if the username exists
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // If the password is correct, start a session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];

        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        // Display error if login fails
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --white-color: hsl(0, 0.00%, 98.00%);
            --black-color: hsl(0, 0% , 0%);
            --body-font: "Poppins", sans-serif; 
            --h1-font-size: 1.75rem;
            --normal-font-size: 1rem;
            --small-font-size: .813rem;
            --font-medium: 500; 
        }

        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body,
        input,
        button {
            font-size: var(--normal-font-size);
            font-family: var(--body-font);
        }

        body {
            color: var(--white-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('../assets/DBreakers Background.png') no-repeat center center/cover;
        }

        input,
        button {
            border: none;
            outline: none;
        }

        a {
            text-decoration: none;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .login {
            position: relative;
            height: 100vh;
            display: grid;
            align-items: center;
        }

        .login__img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .login__form {
            position: relative;
            background-color: hsla(0, 0%, 10%, 0.1);
            border: 2px solid var(--white-color);
            margin-inline: 1.5rem;
            padding: 2.5rem 1.5rem;
            border-radius: 1rem;
            backdrop-filter: blur(8px);
            width: 100%;
            max-width: 432px;
        }

        .login__title {
            text-align: center;
            font-size: var(--h1-font-size);
            font-weight: var(--font-medium);
            margin-bottom: 2rem;
            color: var(--white-color);
        }

        .alert-danger {
            background-color: hsla(354, 70%, 54%, 0.1);
            border: 1px solid hsla(354, 70%, 54%, 0.4);
            color: var(--white-color);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            font-size: var(--small-font-size);
        }

        .login__content {
            display: grid;
            row-gap: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .login__box {
            display: grid;
            grid-template-columns: max-content 1fr;
            align-items: center;
            column-gap: 0.75rem;
            border-bottom: 2px solid var(--white-color);
            padding-right: 0.5rem;
        }

        .login__icon, .login__eye {
            font-size: 1.25rem;
            color: var(--white-color);
        }

        .login__input {
            width: 100%;
            padding: 0.8rem 0;
            background: none;
            color: var(--white-color);
            position: relative;
            z-index: 1;
        }

        .login__box-input {
            position: relative;
        }

        .login__label {
            position: absolute;
            left: 0;
            top: 13px;
            font-weight: var(--font-medium);
            color: var(--white-color);
            transition: all 0.3s ease;
            z-index: 0;
        }

        .login__eye {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
        }

        .login__box:nth-child(2) input {
            padding-right: 1.8rem;
        }

        .login__check {
            margin-bottom: 1.5rem;
        }

        .login__check-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login__check-input {
            width: 16px;
            height: 16px;
            accent-color: var(--white-color);
        }

        .login__check-label {
            font-size: var(--small-font-size);
            color: var(--white-color);
        }

        .login__button {
            width: 100%;
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: var(--white-color);
            color: var(--black-color);
            font-weight: var(--font-medium);
            cursor: pointer;
            margin-bottom: 2rem;
            transition: background-color 0.3s ease;
        }

        .login__button:hover {
            background-color: #f0f0f0;
        }

        .login__register {
            text-align: center;
            color: var(--white-color);
        }

        .login__register a {
            color: var(--white-color);
            font-weight: var(--font-medium);
            text-decoration: none;
        }

        .login__register a:hover {
            text-decoration: underline;
        }

        /* Fix for floating labels */
        .login__input:focus + .login__label,
        .login__input:not(:placeholder-shown) + .login__label {
            top: -12px;
            font-size: var(--small-font-size);
        }

        /* for medium devices */
        @media screen and (min-width: 576px) {
            .login__form {
                padding: 4rem 3rem 3.5rem;
                border-radius: 1.5rem;
            }

            .login__title {
                font-size: 2rem;
            }
        }

        /* Registration Modal Styling */
        .modal-content {
            background-color: hsla(0, 0%, 10%, 0.1);
            border: 2px solid var(--white-color);
            border-radius: 1rem;
            backdrop-filter: blur(8px);
            color: var(--white-color);
        }

        .modal-header {
            border-bottom: none;
            padding: 2rem 2rem 1rem;
        }

        .modal-body {
            padding: 1rem 2rem 2rem;
        }

        .modal-title {
            color: var(--white-color);
            font-size: var(--h1-font-size);
            font-weight: var(--font-medium);
            width: 100%;
            text-align: center;
        }

        .modal .close {
            color: var(--white-color);
            opacity: 1;
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
        }

        .modal .form-group {
            position: relative;
            margin-bottom: 1.75rem;
        }

        .modal .form-control {
            background: none;
            border: none;
            border-bottom: 2px solid var(--white-color);
            border-radius: 0;
            padding: 0.8rem 0;
            color: var(--white-color);
            font-size: var(--normal-font-size);
        }

        .modal .form-control:focus {
            box-shadow: none;
            border-color: var(--white-color);
        }

        .modal label {
            color: var(--white-color);
            position: absolute;
            top: 0.8rem;
            left: 0;
            font-size: var(--normal-font-size);
            transition: all 0.3s ease;
        }

        .modal .form-control:focus ~ label,
        .modal .form-control:not(:placeholder-shown) ~ label {
            top: -0.5rem;
            font-size: var(--small-font-size);
            color: var(--white-color);
        }

        .modal .btn-primary {
            background-color: var(--white-color);
            border: none;
            color: var(--black-color);
            padding: 1rem;
            font-weight: var(--font-medium);
            border-radius: 0.5rem;
            width: 100%;
            margin-top: 1rem;
        }

        .modal .btn-primary:hover {
            background-color: #f0f0f0;
        }

        .modal-backdrop.show {
            opacity: 0.7;
        }
    </style>
</head>
<body>

<div class="login">
    <form method="POST" action="admin.php" class="login__form">
        <h2 class="login__title">Admin Login</h2>
        
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="login__content">
            <div class="login__box">
                <i class="login__icon fas fa-user"></i>
                <div class="login__box-input">
                    <input type="text" id="username" name="username" class="login__input" placeholder=" " required>
                    <label for="username" class="login__label">Username</label>
                </div>
            </div>

            <div class="login__box">
                <i class="login__icon fas fa-lock"></i>
                <div class="login__box-input">
                    <input type="password" id="password" name="password" class="login__input" placeholder=" " required>
                    <label for="password" class="login__label">Password</label>
                    <i class="login__eye fas fa-eye"></i>
                </div>
            </div>
        </div>

        <div class="login__check">
            <div class="login__check-group">
                <input type="checkbox" id="remember" class="login__check-input">
                <label for="remember" class="login__check-label">Remember me</label>
            </div>
        </div>

        <button type="submit" class="login__button">Login</button>

        <div class="login__register">
            <span>Don't have an account? <a href="#" data-toggle="modal" data-target="#registerModal">Register here</a></span>
        </div>
    </form>
</div>

<!-- Modal for Registration -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Admin Registration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="register.php">
                    <div class="form-group">
                        <input type="text" id="reg_username" name="username" class="form-control" placeholder=" " required>
                        <label for="reg_username">Username</label>
                    </div>

                    <div class="form-group">
                        <input type="password" id="reg_password" name="password" class="form-control" placeholder=" " required>
                        <label for="reg_password">Password</label>
                    </div>

                    <div class="form-group">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder=" " required>
                        <label for="confirm_password">Confirm Password</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Password visibility toggle
    const showHiddenInput = (inputOverlay, inputPass, inputIcon) => {
        const input = document.getElementById(inputPass),
              iconEye = document.querySelector(inputIcon)
        
        iconEye.addEventListener('click', () => {
            // Change password to text
            if(input.type === 'password') {
                input.type = 'text'
                iconEye.classList.add('fa-eye-slash')
                iconEye.classList.remove('fa-eye')
            } else { // Change text to password
                input.type = 'password'
                iconEye.classList.remove('fa-eye-slash')
                iconEye.classList.add('fa-eye')
            }
        })
    }

    showHiddenInput('.login__box-input', 'password', '.login__eye')

    // Remember me functionality
    const rememberCheckbox = document.getElementById('remember');
    const usernameInput = document.getElementById('username');
    
    // Check if there are stored credentials
    if(localStorage.getItem('rememberedUsername')) {
        usernameInput.value = localStorage.getItem('rememberedUsername');
        rememberCheckbox.checked = true;
    }

    // Store credentials if remember me is checked
    document.querySelector('.login__form').addEventListener('submit', (e) => {
        if(rememberCheckbox.checked) {
            localStorage.setItem('rememberedUsername', usernameInput.value);
        } else {
            localStorage.removeItem('rememberedUsername');
        }
    });

    // Fix for label animation
    document.querySelectorAll('.login__input').forEach(input => {
        // Initial state check
        if(input.value !== '') {
            const label = input.nextElementSibling;
            label.style.top = '-12px';
            label.style.fontSize = 'var(--small-font-size)';
        }

        // Input event for real-time updates
        input.addEventListener('input', () => {
            const label = input.nextElementSibling;
            if(input.value !== '') {
                label.style.top = '-12px';
                label.style.fontSize = 'var(--small-font-size)';
            } else {
                label.style.top = '13px';
                label.style.fontSize = 'var(--normal-font-size)';
            }
        });

        // Focus event for better UX
        input.addEventListener('focus', () => {
            const label = input.nextElementSibling;
            label.style.top = '-12px';
            label.style.fontSize = 'var(--small-font-size)';
        });

        // Blur event to revert if empty
        input.addEventListener('blur', () => {
            const label = input.nextElementSibling;
            if(input.value === '') {
                label.style.top = '13px';
                label.style.fontSize = 'var(--normal-font-size)';
            }
        });
    });

    // Registration form floating labels
    document.querySelectorAll('.modal .form-control').forEach(input => {
        // Initial state check
        if(input.value !== '') {
            const label = input.nextElementSibling;
            label.style.top = '-0.5rem';
            label.style.fontSize = 'var(--small-font-size)';
        }

        // Input event for real-time updates
        input.addEventListener('input', () => {
            const label = input.nextElementSibling;
            if(input.value !== '') {
                label.style.top = '-0.5rem';
                label.style.fontSize = 'var(--small-font-size)';
            } else {
                label.style.top = '0.8rem';
                label.style.fontSize = 'var(--normal-font-size)';
            }
        });

        // Focus event for better UX
        input.addEventListener('focus', () => {
            const label = input.nextElementSibling;
            label.style.top = '-0.5rem';
            label.style.fontSize = 'var(--small-font-size)';
        });

        // Blur event to revert if empty
        input.addEventListener('blur', () => {
            const label = input.nextElementSibling;
            if(input.value === '') {
                label.style.top = '0.8rem';
                label.style.fontSize = 'var(--normal-font-size)';
            }
        });
    });
</script>

</body>
</html>