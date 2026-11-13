<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php $this->setVar('title', 'Masuk | Ruang Rikza'); ?>

<div class="auth-wrap">
    <div class="card card--pad auth-card">
        <div class="text-center" style="margin-bottom: 22px;">
            <span class="nav__logo" style="margin: 0 auto 12px; width: 48px; height: 48px; font-size: 1.4rem;" aria-hidden="true">R</span>
            <h1 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 4px;">Selamat datang kembali</h1>
            <p class="text-muted mb-0">Masuk untuk menulis, berkomentar, dan menyimpan artikel.</p>
        </div>

        <form action="<?= url_to('login') ?>" method="post" data-once>
            <?= csrf_field() ?>

            <div class="field">
                <label class="label" for="email">Email</label>
                <input class="input" type="email" id="email" name="email" inputmode="email" autocomplete="email" placeholder="nama@email.com" value="<?= old('email') ?>" required autofocus>
            </div>

            <div class="field">
                <label class="label" for="password">Kata sandi</label>
                <div style="position: relative;">
                    <input class="input" type="password" id="password" name="password" autocomplete="current-password" placeholder="Kata sandi" required style="padding-right: 74px;">
                    <button type="button" class="mini-btn" data-toggle-password="#password" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%);">Tampilkan</button>
                </div>
            </div>

            <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                <label class="flex items-center gap-8" style="margin-bottom: 20px; cursor: pointer; font-size: .92rem;">
                    <input type="checkbox" name="remember" value="1" <?= old('remember') ? 'checked' : '' ?>> Ingat saya di perangkat ini
                </label>
            <?php endif ?>

            <button type="submit" class="btn btn--primary btn--block btn--lg">Masuk</button>
        </form>

        <p class="text-center text-muted" style="margin: 20px 0 0; font-size: .92rem;">
            <?php if (setting('Auth.allowMagicLinkLogins')): ?>
                <a href="<?= url_to('magic-link') ?>">Lupa kata sandi?</a><br>
            <?php endif ?>
            <?php if (setting('Auth.allowRegistration')): ?>
                Belum punya akun? <a href="<?= url_to('register') ?>"><strong>Daftar</strong></a>
            <?php endif ?>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
