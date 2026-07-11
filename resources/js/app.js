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
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-md shadow-sm',
                    cancelButton: 'rounded-md shadow-sm text-gray-700'
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
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-md shadow-sm'
            }
        });
    });

    Livewire.on('swal:error', (data) => {
        let message = Array.isArray(data) ? data[0] : data;
        Swal.fire({
            title: 'Gagal!',
            text: message,
            icon: 'error',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-md shadow-sm bg-red-500'
            }
        });
    });
});
