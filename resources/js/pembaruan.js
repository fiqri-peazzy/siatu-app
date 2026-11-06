/**
 * Pembaruan otomatis untuk PWA yang sudah terpasang (tanpa hapus/pasang ulang).
 *
 * Masalah: di HP aplikasi PWA jarang benar-benar ditutup, dan wire:navigate tidak memuat ulang
 * halaman — sehingga JS/CSS lama bisa terus dipakai walau server sudah diperbarui.
 *
 * Cara kerja:
 *  1. Versi build saat halaman dimuat dibaca dari <meta name="siatu-versi">.
 *  2. Versi server dicek (GET /versi) saat aplikasi kembali ke layar, saat fokus, dan tiap 10 menit.
 *  3. Bila berbeda:
 *     - kembali dari latar & aman (tidak ada isian belum disimpan, bukan layar kamera/form) → muat ulang diam-diam;
 *     - selain itu: navigasi berikutnya dijadikan muat penuh + tampil bar "Versi baru tersedia".
 */
const meta = document.querySelector('meta[name="siatu-versi"]');
const versiDimuat = meta?.content;

const INTERVAL = 10 * 60 * 1000;
const JEDA_LATAR = 30 * 1000; // dianggap "kembali ke aplikasi" bila tersembunyi ≥ 30 detik
const LAYAR_SENSITIF = /absen-sekarang|\/jurnal\/isi|\/create|\/edit|\/pengaturan/;

let adaPembaruan = false;
let adaIsian = false;
let tersembunyiSejak = null;
let terakhirCek = 0;

if (versiDimuat && versiDimuat !== 'dev') {
    // Isian form yang belum disimpan → jangan muat ulang otomatis
    document.addEventListener('input', (e) => {
        if (e.target.closest?.('input, textarea, select, [contenteditable]')) adaIsian = true;
    }, true);
    document.addEventListener('submit', () => { adaIsian = false; }, true);
    document.addEventListener('livewire:navigated', () => { adaIsian = false; });

    // Ada pembaruan → navigasi berikutnya jadi muat penuh (aset baru ikut terambil)
    document.addEventListener('livewire:navigate', (e) => {
        if (! adaPembaruan) return;
        e.preventDefault();
        window.location.href = e.detail?.url?.toString?.() ?? e.detail?.url ?? window.location.href;
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            tersembunyiSejak = Date.now();
            return;
        }
        const dariLatar = tersembunyiSejak && Date.now() - tersembunyiSejak >= JEDA_LATAR;
        tersembunyiSejak = null;
        cek({ dariLatar });
    });
    window.addEventListener('focus', () => cek({ dariLatar: false }));
    window.addEventListener('online', () => cek({ dariLatar: false, paksa: true }));
    setInterval(() => document.visibilityState === 'visible' && cek({ dariLatar: false }), INTERVAL);
}

function kameraAktif() {
    return [...document.querySelectorAll('video')].some((v) => v.srcObject && v.srcObject.active);
}

function amanDimuatUlang() {
    return ! adaIsian && ! kameraAktif() && ! LAYAR_SENSITIF.test(location.pathname);
}

async function cek({ dariLatar, paksa = false }) {
    // Batasi frekuensi (fokus & visibilitychange sering datang bersamaan)
    if (! paksa && Date.now() - terakhirCek < 15 * 1000) return;
    terakhirCek = Date.now();

    try {
        navigator.serviceWorker?.getRegistration?.().then((reg) => reg?.update()).catch(() => {});
        const res = await fetch('/versi', { cache: 'no-store', headers: { Accept: 'application/json' } });
        if (! res.ok) return;
        const { versi } = await res.json();
        if (! versi || versi === 'dev' || versi === versiDimuat) return;

        adaPembaruan = true;
        if (dariLatar && amanDimuatUlang()) {
            window.location.reload();
            return;
        }
        tampilkanBar();
    } catch (e) {
        // offline / server sibuk — coba lagi di kesempatan berikutnya
    }
}

function tampilkanBar() {
    if (document.getElementById('siatu-pembaruan')) return;
    const bar = document.createElement('div');
    bar.id = 'siatu-pembaruan';
    bar.setAttribute('role', 'status');
    bar.className = 'fixed inset-x-3 z-[10000] mx-auto flex max-w-md items-center gap-3 rounded-2xl bg-navy-900 px-4 py-3 text-white shadow-float ring-1 ring-white/10 bottom-[calc(env(safe-area-inset-bottom)+5.5rem)] lg:bottom-6';
    bar.innerHTML = `
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gold-400/15 text-gold-300" aria-hidden="true">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </span>
        <span class="min-w-0 flex-1">
            <span class="block text-[14px] font-semibold">Versi baru tersedia</span>
            <span class="block text-[12px] text-white/60">Perbaikan & fitur terbaru siap dipakai.</span>
        </span>
        <button type="button" class="h-9 shrink-0 rounded-xl bg-gold-400 px-3.5 text-[13px] font-semibold text-navy-950">Perbarui</button>
        <button type="button" class="-mr-1 flex h-9 w-8 shrink-0 items-center justify-center text-white/50" aria-label="Nanti saja">✕</button>`;
    const [perbarui, tutup] = bar.querySelectorAll('button');
    perbarui.addEventListener('click', () => {
        if (adaIsian && ! confirm('Isian yang belum disimpan akan hilang. Perbarui sekarang?')) return;
        window.location.reload();
    });
    tutup.addEventListener('click', () => bar.remove());
    document.body.appendChild(bar);
}
