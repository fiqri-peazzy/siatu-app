<div class="w-full max-w-md w-full animate-fade-in-up">
    
    <div class="flex flex-col items-center text-center mb-12">
        <div class="flex items-center justify-center w-28 h-28 rounded-[2rem] bg-gradient-to-tr from-indigo-500/10 to-violet-500/10 p-1 mb-5 ring-1 ring-white/10 shadow-[0_0_40px_rgba(79,70,229,0.15)]">
            <div class="w-full h-full rounded-[calc(2rem-4px)] glass-panel flex items-center justify-center p-3">
                <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="SITU-SMA Logo" class="w-full h-full object-contain drop-shadow-lg">
            </div>
        </div>
        <span class="rounded-full px-4 py-1.5 text-[10px] uppercase tracking-[0.25em] font-semibold text-indigo-400 bg-indigo-500/10 ring-1 ring-indigo-500/20 mb-4 inline-block">SITU-SMA Portal</span>
        <h1 class="text-4xl font-extrabold tracking-tight text-white mb-3">SMAMGO</h1>
        <p class="text-sm text-slate-400 font-medium px-4">SMA Muhammadiyah Gorontalo<br/>Sistem Informasi Tata Usaha Terpadu</p>
    </div>

    <!-- Outer Shell (Double Bezel) -->
    <div class="bg-white/5 rounded-[2.5rem] p-2 ring-1 ring-white/10 shadow-2xl backdrop-blur-md">
        
        <!-- Inner Core -->
        <div class="glass-panel rounded-[calc(2.5rem-8px)] p-8 sm:p-10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)] relative overflow-hidden">
            
            <!-- Subtle internal glow -->
            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            
            <form wire:submit="login" class="space-y-6 relative z-10">
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-widest pl-1">Email / NIP</label>
                    <div class="relative group">
                        <input wire:model="email" type="email" id="email" 
                            class="w-full bg-black/40 border border-white/10 focus:border-indigo-500/50 focus:ring focus:ring-indigo-500/20 rounded-2xl p-4 text-white placeholder-slate-500 transition-all duration-300 outline-none"
                            placeholder="nama@sekolah.sch.id">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                    </div>
                    @error('email') <span class="text-xs font-medium text-rose-400 mt-2 block pl-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-widest pl-1">Kata Sandi</label>
                    <div class="relative group">
                        <input wire:model="password" type="password" id="password" 
                            class="w-full bg-black/40 border border-white/10 focus:border-indigo-500/50 focus:ring focus:ring-indigo-500/20 rounded-2xl p-4 text-white placeholder-slate-500 transition-all duration-300 outline-none"
                            placeholder="••••••••">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-indigo-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                    </div>
                    @error('password') <span class="text-xs font-medium text-rose-400 mt-2 block pl-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input wire:model="remember" type="checkbox" class="peer sr-only">
                            <div class="w-5 h-5 rounded-md border border-white/20 bg-black/40 peer-checked:bg-indigo-500 peer-checked:border-indigo-500 transition-all duration-200"></div>
                            <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-400 group-hover:text-slate-300 transition-colors">Ingat Sesi Ini</span>
                    </label>
                    <a href="#" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Lupa Sandi?</a>
                </div>

                <div class="pt-6">
                    <button type="submit" wire:loading.attr="disabled"
                        class="group relative w-full flex items-center justify-between rounded-full bg-white text-black p-2 pr-6 hover:bg-slate-100 active:scale-[0.98] transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)]">
                        
                        <span wire:loading.remove wire:target="login" class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-black/5 flex items-center justify-center mr-4 group-hover:translate-x-1 group-hover:-translate-y-[1px] group-hover:scale-105 transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                            <span class="font-bold text-sm uppercase tracking-wide">Otorisasi Masuk</span>
                        </span>

                        <span wire:loading wire:target="login" class="flex items-center w-full justify-center h-12">
                            <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
    
    <div class="mt-12 text-center">
        <p class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-medium">Internal System &copy; {{ date('Y') }}</p>
    </div>

</div>
