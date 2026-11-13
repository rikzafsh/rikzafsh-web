/* Ruang Rikza — interaksi antarmuka (tanpa dependensi) */
(function () {
    'use strict';

    var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
    var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

    // ---- Menu navigasi mobile ----
    var navToggle = $('#navToggle');
    var navLinks = $('#navLinks');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            var open = navLinks.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // ---- Menu pengguna: tutup saat klik di luar atau tekan Esc ----
    document.addEventListener('click', function (e) {
        $$('.user-menu[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') $$('.user-menu[open]').forEach(function (d) { d.removeAttribute('open'); });
    });

    // ---- Alert: tombol tutup + hilang otomatis untuk pesan sukses ----
    $$('.alert__close').forEach(function (b) {
        b.addEventListener('click', function () { var a = b.closest('.alert'); if (a) a.remove(); });
    });
    $$('.alert--success[data-autohide]').forEach(function (a) {
        setTimeout(function () { a.style.transition = 'opacity .4s'; a.style.opacity = '0'; setTimeout(function () { a.remove(); }, 450); }, 6000);
    });

    // ---- Konfirmasi sebelum aksi berbahaya: <form data-confirm="Yakin?"> ----
    $$('form[data-confirm]').forEach(function (f) {
        f.addEventListener('submit', function (e) { if (!window.confirm(f.getAttribute('data-confirm'))) e.preventDefault(); });
    });

    // ---- Cegah kirim ganda: <form data-once> ----
    $$('form[data-once]').forEach(function (f) {
        f.addEventListener('submit', function () {
            setTimeout(function () { $$('button[type=submit]', f).forEach(function (b) { b.disabled = true; }); }, 0);
        });
    });

    // ---- Salin tautan: <button data-copy> ----
    $$('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = window.location.href.split('#')[0];
            var done = function () {
                var old = btn.getAttribute('title');
                btn.setAttribute('title', 'Tautan disalin!');
                btn.classList.add('is-copied');
                setTimeout(function () { btn.setAttribute('title', old || 'Salin tautan'); btn.classList.remove('is-copied'); }, 2000);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(done);
            } else {
                var t = document.createElement('textarea'); t.value = url; document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); done(); } catch (err) { /* abaikan */ }
                t.remove();
            }
        });
    });

    // ---- Tampilkan/sembunyikan kata sandi ----
    $$('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = $(btn.getAttribute('data-toggle-password'));
            if (!inp) return;
            var show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Sembunyikan' : 'Tampilkan';
        });
    });

    // ---- Form balasan komentar ----
    $$('[data-reply-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var box = document.getElementById('reply-form-' + btn.getAttribute('data-reply-toggle'));
            if (!box) return;
            var open = box.classList.toggle('is-open');
            if (open) { var ta = $('textarea', box); if (ta) ta.focus(); }
        });
    });
    $$('[data-reply-cancel]').forEach(function (btn) {
        btn.addEventListener('click', function () { var box = btn.closest('.reply-form'); if (box) box.classList.remove('is-open'); });
    });

    // ---- Penghitung karakter: <textarea maxlength data-counter="#id"> ----
    $$('[data-counter]').forEach(function (el) {
        var out = $(el.getAttribute('data-counter'));
        var max = parseInt(el.getAttribute('maxlength'), 10);
        if (!out || !max) return;
        var update = function () {
            out.textContent = el.value.length + ' / ' + max;
            out.classList.toggle('is-limit', el.value.length >= max * 0.95);
        };
        el.addEventListener('input', update);
        update();
    });

    // ---- Textarea tumbuh otomatis ----
    $$('.js-autosize').forEach(function (ta) {
        var fit = function () { ta.style.height = 'auto'; ta.style.height = Math.min(ta.scrollHeight + 2, 360) + 'px'; };
        ta.addEventListener('input', fit);
    });

    // ---- Pratinjau gambar: <input type=file data-preview="#img"> ----
    $$('input[type=file][data-preview]').forEach(function (inp) {
        inp.addEventListener('change', function () {
            var img = $(inp.getAttribute('data-preview'));
            if (!img || !inp.files || !inp.files[0]) return;
            img.src = URL.createObjectURL(inp.files[0]);
            img.hidden = false;
            var holder = img.closest('[data-preview-holder]');
            if (holder) holder.hidden = false;
        });
    });

    // ---- Peringatan perubahan belum disimpan: <form data-warn-unsaved> ----
    var dirty = false;
    var warnForm = $('form[data-warn-unsaved]');
    if (warnForm) {
        warnForm.addEventListener('input', function () { dirty = true; });
        warnForm.addEventListener('submit', function () { dirty = false; });
        window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
    }
    window.RR = { markDirty: function () { dirty = true; } };

    // ---- Bilah progres baca ----
    var bar = $('#readProgress');
    var body = $('#articleBody');
    if (bar && body) {
        var onScroll = function () {
            var r = body.getBoundingClientRect();
            var total = r.height - window.innerHeight * 0.6;
            var done = Math.min(Math.max(-r.top + window.innerHeight * 0.2, 0), Math.max(total, 1));
            bar.style.width = (total > 0 ? (done / total) * 100 : 100) + '%';
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ---- Tab dashboard (ingat tab terakhir) ----
    var tabButtons = $$('.tab-btn[data-tab]');
    if (tabButtons.length) {
        var show = function (id) {
            var found = false;
            tabButtons.forEach(function (b) {
                var on = b.getAttribute('data-tab') === id;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-selected', on ? 'true' : 'false');
                if (on) found = true;
            });
            if (!found) { id = tabButtons[0].getAttribute('data-tab'); tabButtons[0].classList.add('is-active'); }
            $$('.tab-panel').forEach(function (p) { p.classList.toggle('is-active', p.id === id); });
            try { localStorage.setItem('rr_dash_tab', id); } catch (err) { /* abaikan */ }
        };
        tabButtons.forEach(function (b) { b.addEventListener('click', function () { show(b.getAttribute('data-tab')); }); });
        var saved = null;
        try { saved = localStorage.getItem('rr_dash_tab'); } catch (err) { /* abaikan */ }
        show(saved || tabButtons[0].getAttribute('data-tab'));
    }
})();
