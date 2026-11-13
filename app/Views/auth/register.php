<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php $this->setVar('title', 'Daftar | Ruang Rikza'); ?>

<div class="auth-wrap">
    <div class="card card--pad auth-card">
        <div class="text-center" style="margin-bottom: 22px;">
            <span class="nav__logo" style="margin: 0 auto 12px; width: 48px; height: 48px; font-size: 1.4rem;" aria-hidden="true">R</span>
            <h1 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 4px;">Buat akun baru</h1>
            <p class="text-muted mb-0">Gratis dan hanya butuh satu menit.</p>
        </div>

        <form action="<?= url_to('register') ?>" method="post" data-once>
            <?= csrf_field() ?>

            <div class="field">
                <label class="label" for="email">Email</label>
                <input class="input" type="email" id="email" name="email" inputmode="email" autocomplete="email" placeholder="nama@email.com" value="<?= old('email') ?>" required autofocus>
            </div>

            <div class="field">
                <label class="label" for="username">Nama pengguna</label>
                <input class="input" type="text" id="username" name="username" autocomplete="username" placeholder="mis. rikza_f" value="<?= old('username') ?>" required>
                <span class="hint">Dipakai di alamat profil publikmu.</span>
            </div>

            <div class="field">
                <label class="label" for="password">Kata sandi</label>
                <div style="position: relative;">
                    <input class="input" type="password" id="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" required style="padding-right: 74px;">
                    <button type="button" class="mini-btn" data-toggle-password="#password" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%);">Tampilkan</button>
                </div>
            </div>

            <div class="field">
                <label class="label" for="password_confirm">Ulangi kata sandi</label>
                <input class="input" type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" placeholder="Ketik ulang kata sandi" required>
            </div>

            <button type="submit" class="btn btn--primary btn--block btn--lg">Daftar</button>
        </form>

        <p class="text-center text-muted" style="margin: 20px 0 0; font-size: .92rem;">
            Sudah punya akun? <a href="<?= url_to('login') ?>"><strong>Masuk</strong></a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
