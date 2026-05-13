<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="register-container">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card register-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="register-title"><i>Create Account</i></h2>
                            <p class="text-muted"><i>Join PHY-FA-CAT today</i></p>
                        </div>

                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('register') ?>" method="post">
                            <div class="form-group mb-3">
                                <label for="username" class="form-label"><i>Username</i></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" class="form-control" name="username" placeholder="Choose a username" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label"><i>Email</i></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="form-label"><i>Password</i></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="password" placeholder="Create a password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye-slash" id="togglePassword1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="confirm_password" class="form-label"><i>Confirm Password</i></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm your password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                        <i class="bi bi-eye-slash" id="togglePassword2"></i>
                                    </button>
                                </div>
                            </div>

                            <script>
                                function togglePassword(inputName) {
                                    const passwordInput = document.querySelector(`input[name="${inputName}"]`);
                                    const toggleIcon = inputName === 'password' ?
                                        document.querySelector('#togglePassword1') :
                                        document.querySelector('#togglePassword2');

                                    if (passwordInput.type === 'password') {
                                        passwordInput.type = 'text';
                                        toggleIcon.classList.remove('bi-eye-slash');
                                        toggleIcon.classList.add('bi-eye');
                                    } else {
                                        passwordInput.type = 'password';
                                        toggleIcon.classList.remove('bi-eye');
                                        toggleIcon.classList.add('bi-eye-slash');
                                    }
                                }
                            </script>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i>Create Account</i> <i class="bi bi-arrow-right-circle ms-1"></i>
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0"><i>Already have an account?</i> <a href="<?= base_url('login') ?>" class="login-link"><i>Sign In</i></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .register-container {
        background: linear-gradient(276deg, #17376E -2.09%, #481F64 75.22%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .register-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }

    .register-title {
        color: #481F64;
        font-weight: 600;
    }

    .form-label {
        color: #666;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: #481F64;
    }

    .form-control {
        border-left: none;
        padding-left: 0;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }

    .btn-outline-secondary {
        border-color: #ced4da;
        color: #481F64;
        background-color: #f8f9fa;
        border-left: none;
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus {
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #17376E;
        box-shadow: none;
    }

    .input-group>.btn-outline-secondary {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }

    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    .alert-danger ul {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }

    .btn-primary {
        background-color: #481F64;
        border-color: #481F64;
        padding: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #17376E;
        border-color: #17376E;
        transform: translateY(-2px);
    }

    .login-link {
        color: #481F64;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .login-link:hover {
        color: #17376E;
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .register-container {
            padding: 20px;
        }

        .register-card {
            margin: 10px;
        }
    }
</style>
<?= $this->endSection() ?>