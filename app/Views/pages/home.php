<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="auth-gateway">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9">
                <div class="gateway-card">
                    <div class="row g-0 align-items-stretch">
                        <div class="col-md-6 gateway-panel gateway-panel-brand">
                            <div class="gateway-copy">
                                <div class="gateway-badge">PHY-CAT-CBT</div>
                                <h1 class="gateway-title">Masuk untuk mulai menggunakan platform.</h1>
                                <p class="gateway-text">Akses akun Anda atau buat akun baru untuk melanjutkan ke halaman ujian, hasil, dan rekap data.</p>
                            </div>
                        </div>
                        <div class="col-md-6 gateway-panel gateway-panel-action">
                            <div class="gateway-actions">
                                <h2 class="gateway-actions-title">Akses Akun</h2>
                                <p class="gateway-actions-text">Pilih tindakan yang ingin dilakukan.</p>
                                <div class="gateway-buttons">
                                    <a href="<?= base_url('login') ?>" class="btn btn-gateway-primary">Sign In</a>
                                    <a href="<?= base_url('register') ?>" class="btn btn-gateway-secondary">Sign Up</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-gateway {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background:
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.20), transparent 30%),
        radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.16), transparent 34%),
        linear-gradient(180deg, #edf4ff 0%, #f7fbff 100%);
    padding: 2rem 0;
}

.gateway-card {
    overflow: hidden;
    border: 1px solid #d7e3f4;
    background: #ffffff;
    box-shadow: 0 22px 50px rgba(37, 99, 235, 0.10);
}

.gateway-panel {
    min-height: 420px;
    display: flex;
    align-items: center;
}

.gateway-panel-brand {
    background: linear-gradient(160deg, #1d4ed8 0%, #2563eb 52%, #1e40af 100%);
    color: #ffffff;
}

.gateway-panel-action {
    background: #ffffff;
}

.gateway-copy,
.gateway-actions {
    width: 100%;
    padding: 2.5rem;
}

.gateway-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.45rem 0.8rem;
    margin-bottom: 1rem;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.22);
}

.gateway-title {
    margin-bottom: 1rem;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
}

.gateway-text,
.gateway-actions-text {
    margin-bottom: 0;
    font-size: 1rem;
    line-height: 1.7;
}

.gateway-actions-title {
    margin-bottom: 0.5rem;
    font-size: 1.55rem;
    font-weight: 700;
    color: #1e3a8a;
}

.gateway-actions-text {
    margin-bottom: 1.75rem;
    color: #64748b;
}

.gateway-buttons {
    display: grid;
    gap: 0.9rem;
}

.gateway-buttons .btn {
    min-height: 50px;
    border-radius: 0;
    font-size: 1rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-gateway-primary {
    background: #2563eb;
    border: 1px solid #2563eb;
    color: #ffffff;
}

.btn-gateway-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
}

.btn-gateway-secondary {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
}

.btn-gateway-secondary:hover {
    background: #dbeafe;
    border-color: #93c5fd;
    color: #1e40af;
}

@media (max-width: 767.98px) {
    .auth-gateway {
        padding: 1rem 0;
    }

    .gateway-panel {
        min-height: auto;
    }

    .gateway-copy,
    .gateway-actions {
        padding: 1.5rem;
    }

    .gateway-title {
        font-size: 1.6rem;
    }
}
</style>

<?= $this->endSection() ?>
