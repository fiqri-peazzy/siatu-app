<div class="font-body"
     x-data="{
        izin: window.Notification ? Notification.permission : 'tidak_didukung',
        proses: false,
        async aktifkan() {
            if (! window.Notification) return;
            this.proses = true;
            this.izin = await Notification.requestPermission();
            if (this.izin === 'granted') await window.siatuSubscribePush?.().catch(() => {});
            this.proses = false;
        },
     }">
    <p class="text-[12px] font-medium text-navy-400">Profil</p>
    <h1 class="font-heading text-[1.35rem] font-semibold leading-tight text-navy-900">Notifikasi & pengingat</h1>

    {{-- Status perangkat --}}
    <section class="mt-5 rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
        <div class="flex items-center gap-3.5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                  :class="izin === 'granted' ? 'bg-ok-50 text-ok-500' : 'bg-warn-50 text-warn-500'"><x-ui.icon name="lonceng" /></span>
            <div class="min-w-0 flex-1">
                <p class="text-[15px] font-semibold text-navy-900">Notifikasi di perangkat ini</p>
                <p class="text-[13px]" :class="izin === 'granted' ? 'text-ok-500' : 'text-navy-500'"
                   x-text="{ granted: 'Aktif — pengingat muncul walau aplikasi ditutup', denied: 'Diblokir — izinkan dari pengaturan browser', default: 'Belum aktif', tidak_didukung: 'Browser ini tidak mendukung notifikasi' }[izin]"></p>
            </div>
        </div>
        <button type="button" x-show="izin === 'default'" @click="aktifkan()" :disabled="proses"
                class="mt-3 h-11 w-full rounded-2xl bg-navy-900 text-[14px] font-semibold text-white active:scale-[0.98] disabled:opacity-60">Aktifkan notifikasi</button>
        <p x-show="izin === 'denied'" x-cloak class="mt-3 rounded-xl bg-ivory-200 px-3 py-2.5 text-[12px] leading-relaxed text-navy-600">
            Ketuk ikon gembok di samping alamat situs → <b>Izin</b> / <b>Notifikasi</b> → <b>Izinkan</b>, lalu muat ulang halaman.
        </p>
    </section>

    {{-- Pengingat jurnal --}}
    <section class="mt-3 rounded-2xl bg-white ring-1 ring-ivory-300">
        <label class="flex cursor-pointer items-center gap-3.5 p-4">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-ivory-200 text-navy-700"><x-ui.icon name="jurnal" /></span>
            <span class="min-w-0 flex-1">
                <span class="block text-[15px] font-semibold text-navy-900">Pengingat jurnal harian</span>
                <span class="block text-[12px] text-navy-400">Pukul 14.00 WITA bila masih ada jurnal yang belum diisi</span>
            </span>
            <span class="relative inline-flex h-7 w-12 shrink-0">
                <input type="checkbox" wire:model.live="reminder_jurnal_aktif" wire:change="simpan" class="peer sr-only">
                <span class="absolute inset-0 rounded-full bg-ivory-400 transition peer-checked:bg-ok-500 peer-focus-visible:ring-2 peer-focus-visible:ring-gold-500"></span>
                <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
            </span>
        </label>
    </section>

    <p class="mt-4 px-1 text-[12px] leading-relaxed text-navy-400">Pengaturan pengingat tersimpan otomatis. Notifikasi dikirim lewat aplikasi (bukan WhatsApp).</p>
</div>
