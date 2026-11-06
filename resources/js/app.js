import './bootstrap';
import './deteksi-wajah';
import './absensi-guru';
import './absensi-tendik';
import './pembaruan';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
window.Swal = Swal;

/*
 * Alert & konfirmasi aplikasi (design system v2, mobile-first).
 *  - success / info / warning → toast di atas, hilang sendiri (tidak memblokir)
 *  - error / confirm          → dialog dengan tombol besar
 * Pesan selalu di-escape (aman dari HTML/XSS).
 */
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const ikon = {
    success: '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>',
    error: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    warning: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007"/>',
    info: '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008"/>',
};
const warnaIkon = { success: 'bg-ok-50 text-ok-500', error: 'bg-bad-50 text-bad-500', warning: 'bg-warn-50 text-warn-500', info: 'bg-info-50 text-info-500' };
const svg = (jenis, ukuran = 'h-5 w-5') => `<svg class="${ukuran}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">${ikon[jenis]}</svg>`;

const toast = (jenis, pesan) => Swal.fire({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: jenis === 'warning' ? 4200 : 2800,
    timerProgressBar: true,
    html: `<div class="flex items-start gap-3 text-left font-body">
              <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full ${warnaIkon[jenis]}">${svg(jenis, 'h-4 w-4')}</span>
              <p class="text-[14px] font-medium leading-snug text-navy-900">${esc(pesan)}</p>
           </div>`,
    customClass: {
        popup: '!w-[min(92vw,26rem)] !rounded-2xl !bg-ivory-50 !p-3.5 !shadow-float ring-1 ring-ivory-300 !mt-[max(env(safe-area-inset-top),0.75rem)]',
        htmlContainer: '!m-0 !p-0',
        timerProgressBar: '!bg-gold-400',
    },
    didOpen: (el) => { el.addEventListener('mouseenter', Swal.stopTimer); el.addEventListener('mouseleave', Swal.resumeTimer); },
});

const dialog = (jenis, judul, pesan, opsi = {}) => Swal.fire({
    html: `<div class="flex flex-col items-center pt-2 font-body">
              <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl ${warnaIkon[jenis]}">${svg(jenis, 'h-7 w-7')}</span>
              <h3 class="font-heading text-lg font-semibold text-navy-900">${esc(judul)}</h3>
              <p class="mt-1.5 px-1 text-center text-[14px] leading-relaxed text-navy-500">${esc(pesan)}</p>
           </div>`,
    buttonsStyling: false,
    reverseButtons: true,
    customClass: {
        popup: '!w-[min(92vw,22rem)] !rounded-3xl !bg-ivory-50 !p-6 !shadow-float',
        htmlContainer: '!m-0 !p-0',
        actions: '!mt-6 !grid !w-full !grid-cols-1 !gap-2.5 !m-0' + (opsi.showCancelButton ? ' sm:!grid-cols-2' : ''),
        confirmButton: `!m-0 h-12 w-full rounded-2xl text-[15px] font-semibold text-white active:scale-[0.98] ${jenis === 'error' ? 'bg-bad-500' : 'bg-navy-900 hover:bg-navy-800'}`,
        cancelButton: '!m-0 h-12 w-full rounded-2xl text-[15px] font-semibold text-navy-700 ring-1 ring-ivory-400',
    },
    ...opsi,
});

window.showAppAlert = {
    success: (pesan) => toast('success', pesan || 'Berhasil disimpan.'),
    info: (pesan) => toast('info', pesan),
    warning: (pesan) => toast('warning', pesan),
    error: (pesan) => dialog('error', 'Terjadi kesalahan', pesan || 'Silakan coba lagi.', { confirmButtonText: 'Mengerti' }),
    confirm: (pesan, judul = 'Konfirmasi') => dialog('warning', judul, pesan, {
        showCancelButton: true, confirmButtonText: 'Ya, lanjutkan', cancelButtonText: 'Batal', focusCancel: true,
    }),
};

document.addEventListener('livewire:initialized', () => {
    // Custom SweetAlert Confirm Directive
    Livewire.directive('swal-confirm', ({ el, directive, component, cleanup }) => {
        let content = directive.expression;
        let onClick = e => {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            window.showAppAlert.confirm(content).then((result) => {
                if (result.isConfirmed) {
                    el.removeEventListener('click', onClick, true);
                    el.click();
                    el.addEventListener('click', onClick, true);
                }
            });
        };
        el.addEventListener('click', onClick, true);
        cleanup(() => {
            el.removeEventListener('click', onClick, true);
        });
    });

    // Listen for Livewire events for SweetAlert
    Livewire.on('swal:success', (data) => {
        let message = Array.isArray(data) ? data[0] : data;
        window.showAppAlert.success(message);
    });

    Livewire.on('swal:error', (data) => {
        let message = Array.isArray(data) ? data[0] : data;
        window.showAppAlert.error(message);
    });

    ['warning', 'info'].forEach((jenis) => Livewire.on(`swal:${jenis}`, (data) => {
        window.showAppAlert[jenis](Array.isArray(data) ? data[0] : data);
    }));
});
