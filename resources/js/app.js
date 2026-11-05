import './bootstrap';
import './deteksi-wajah';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
window.Swal = Swal;

window.showAppAlert = {
    success: (message) => {
        return Swal.fire({
            html: `
                <div class="flex flex-col items-center pt-4">
                    <div class="w-20 h-20 bg-success-teal/10 rounded-full flex items-center justify-center mb-5 border-4 border-success-teal/20">
                        <svg class="w-10 h-10 text-success-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-ink-900 mb-2">Berhasil!</h3>
                    <p class="text-sm text-slate-500 font-medium text-center px-4">${message}</p>
                </div>
            `,
            buttonsStyling: false,
            showConfirmButton: true,
            confirmButtonText: 'Tutup',
            customClass: {
                popup: 'rounded-[2rem] bg-white shadow-[0_20px_60px_rgba(18,26,47,0.15)] border border-line-200 !max-w-[320px] !w-full',
                confirmButton: '!w-full !block bg-navy-deep text-white font-bold py-3.5 rounded-xl hover:bg-navy-mid transition-colors active:scale-95',
                actions: '!w-full !px-8 !pb-8 !m-0 !mt-6 !flex !flex-col',
                htmlContainer: '!m-0 !p-0'
            }
        });
    },
    error: (message) => {
        return Swal.fire({
            html: `
                <div class="flex flex-col items-center pt-4">
                    <div class="w-20 h-20 bg-danger-coral/10 rounded-full flex items-center justify-center mb-5 border-4 border-danger-coral/20">
                        <svg class="w-10 h-10 text-danger-coral" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-ink-900 mb-2">Terjadi Kesalahan</h3>
                    <p class="text-sm text-slate-500 font-medium text-center px-4">${message}</p>
                </div>
            `,
            buttonsStyling: false,
            showConfirmButton: true,
            confirmButtonText: 'Tutup',
            customClass: {
                popup: 'rounded-[2rem] bg-white shadow-[0_20px_60px_rgba(18,26,47,0.15)] border border-line-200 !max-w-[320px] !w-full',
                confirmButton: '!w-full !block bg-danger-coral text-white font-bold py-3.5 rounded-xl hover:opacity-90 transition-opacity active:scale-95',
                actions: '!w-full !px-8 !pb-8 !m-0 !mt-6 !flex !flex-col',
                htmlContainer: '!m-0 !p-0'
            }
        });
    },
    confirm: (message) => {
        return Swal.fire({
            html: `
                <div class="flex flex-col items-center pt-4">
                    <div class="w-20 h-20 bg-warning-amber/10 rounded-full flex items-center justify-center mb-5 border-4 border-warning-amber/20">
                        <svg class="w-10 h-10 text-warning-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-ink-900 mb-2">Konfirmasi</h3>
                    <p class="text-sm text-slate-500 font-medium text-center px-4">${message}</p>
                </div>
            `,
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem] bg-white shadow-[0_20px_60px_rgba(18,26,47,0.15)] border border-line-200 !max-w-[340px] !w-full',
                confirmButton: '!w-full !block bg-navy-deep text-white font-bold py-3.5 rounded-xl hover:bg-navy-mid transition-colors active:scale-95 mb-3',
                cancelButton: '!w-full !block bg-slate-100 text-slate-600 font-bold py-3.5 rounded-xl hover:bg-slate-200 transition-colors active:scale-95 m-0',
                actions: '!w-full !flex !flex-col !px-8 !pb-8 !m-0 !mt-6',
                htmlContainer: '!m-0 !p-0'
            }
        });
    }
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
});
