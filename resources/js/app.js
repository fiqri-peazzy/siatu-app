import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
window.Swal = Swal;

document.addEventListener('livewire:initialized', () => {
    // Override default wire:confirm
    Livewire.directive('confirm', ({ el, directive, component, cleanup }) => {
        let content = directive.expression;
        
        let onClick = e => {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            Swal.fire({
                title: 'Konfirmasi',
                text: content,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Tailwind Indigo 600
                cancelButtonColor: '#d1d5db',  // Tailwind Gray 300
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[2rem] p-6 bg-white shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-slate-100',
                    title: 'text-2xl font-extrabold text-slate-800 mb-2',
                    htmlContainer: 'text-sm font-medium text-slate-500 mb-6',
                    confirmButton: 'w-full bg-slate-900 text-white rounded-full py-3.5 font-bold text-sm shadow-[0_8px_20px_rgb(0,0,0,0.15)] active:scale-95 transition-all mb-3',
                    cancelButton: 'w-full bg-slate-100 text-slate-600 rounded-full py-3.5 font-bold text-sm hover:bg-slate-200 active:scale-95 transition-all',
                    actions: 'w-full flex-col'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove the directive to avoid infinite loop when triggering click manually
                    el.removeAttribute('wire:confirm');
                    el.click();
                    // Put it back
                    el.setAttribute('wire:confirm', content);
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
        Swal.fire({
            title: 'Berhasil!',
            text: message,
            icon: 'success',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-[2rem] p-6 bg-white shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-slate-100',
                title: 'text-2xl font-extrabold text-slate-800 mb-2',
                htmlContainer: 'text-sm font-medium text-slate-500 mb-6',
                confirmButton: 'w-full bg-indigo-500 text-white rounded-full py-3.5 font-bold text-sm shadow-[0_8px_20px_rgb(99,102,241,0.3)] active:scale-95 transition-all',
                icon: 'border-0'
            }
        });
    });

    Livewire.on('swal:error', (data) => {
        let message = Array.isArray(data) ? data[0] : data;
        Swal.fire({
            title: 'Gagal!',
            text: message,
            icon: 'error',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-[2rem] p-6 bg-white shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-slate-100',
                title: 'text-2xl font-extrabold text-slate-800 mb-2',
                htmlContainer: 'text-sm font-medium text-slate-500 mb-6',
                confirmButton: 'w-full bg-rose-500 text-white rounded-full py-3.5 font-bold text-sm shadow-[0_8px_20px_rgb(244,63,94,0.3)] active:scale-95 transition-all',
                icon: 'border-0'
            }
        });
    });
});
