<div class="w-full max-w-md">
    <div class="bg-surface-container-lowest p-10 rounded-xl tonal-shadow">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold tracking-tight text-primary">SITU-SMA</h1>
            <p class="text-sm text-on-surface-variant mt-2">Selamat datang kembali di portal administrasi sekolah.</p>
        </div>

        <form wire:submit="login" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-semibold text-on-surface mb-2">Email Sekolah</label>
                <input wire:model="email" type="email" id="email" 
                    class="w-full bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-md transition-all duration-200 p-3"
                    placeholder="nama@sekolah.sch.id">
                @error('email') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-on-surface mb-2">Kata Sandi</label>
                <input wire:model="password" type="password" id="password" 
                    class="w-full bg-surface-container-low border-0 border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-md transition-all duration-200 p-3"
                    placeholder="••••••••">
                @error('password') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary">
                    <span class="text-sm text-on-surface-variant">Ingat saya</span>
                </label>
                <a href="#" class="text-sm font-medium text-primary hover:underline">Lupa sandi?</a>
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 signature-gradient text-on-primary font-bold rounded-md shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all duration-200 mt-4">
                Masuk ke Sistem
            </button>
        </form>

        <div class="mt-8 pt-8 border-t border-outline-variant/30 text-center">
            <p class="text-xs text-on-surface-variant uppercase tracking-widest">Digitalisasi Masa Depan Pendidikan</p>
        </div>
    </div>
</div>
