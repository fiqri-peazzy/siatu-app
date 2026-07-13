<div class="fixed inset-0 bg-[#f8f9fa] flex items-center justify-center p-4 sm:p-8 z-[60] overflow-y-auto">
    <div class="w-full max-w-5xl bg-white rounded-[2rem] flex overflow-hidden shadow-2xl mx-auto min-h-[600px] animate-fade-in-up relative my-auto">
        <!-- Left Side: Dark Blue Background -->
        <div class="w-1/2 bg-[#0a0f37] relative flex-col justify-center p-12 text-white hidden md:flex overflow-hidden">
            
            <!-- Abstract Shapes -->
            <!-- Top Left Circle -->
            <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-[#181d4b] opacity-80"></div>
            
            <!-- Top Right Semi-circle -->
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full border-[12px] border-[#181d4b] opacity-80"></div>
            
            <!-- Slanted Lines -->
            <div class="absolute top-24 right-20 text-[#181d4b] opacity-80 transform rotate-45 text-2xl tracking-widest font-bold">//////</div>
            <div class="absolute bottom-24 right-24 text-[#181d4b] opacity-80 transform rotate-45 text-2xl tracking-widest font-bold">//////</div>
            
            <!-- Small dots -->
            <div class="absolute top-32 right-12 w-1.5 h-1.5 rounded-full bg-white opacity-80"></div>
            <div class="absolute top-40 left-12 w-1.5 h-1.5 rounded-full bg-white opacity-80"></div>
            <div class="absolute bottom-40 left-32 w-1 h-1 rounded-full bg-white opacity-80"></div>
            
            <!-- Middle Circle -->
            <div class="absolute top-[40%] right-32 w-10 h-10 rounded-full border-[6px] border-[#181d4b] opacity-80"></div>
            <div class="absolute top-[45%] right-20 w-1 h-1 rounded-full bg-white opacity-80"></div>
            
            <!-- Bottom Left Circle -->
            <div class="absolute -bottom-12 -left-12 w-32 h-32 rounded-full border-[12px] border-[#181d4b] opacity-80"></div>
            <div class="absolute bottom-32 left-16 w-8 h-8 rounded-full border-[5px] border-[#181d4b] opacity-80"></div>
            
            <!-- Bottom Right Curve -->
            <div class="absolute -bottom-8 right-16 w-16 h-16 rounded-full border-[6px] border-[#181d4b] opacity-80"></div>
            <div class="absolute -left-2 top-1/2 w-8 h-8 rounded-full border-[4px] border-[#181d4b] opacity-80"></div>

            <div class="z-10 relative mt-4 flex flex-col justify-center items-start">
                <div class="w-28 h-28 bg-white/5 rounded-3xl p-3 backdrop-blur-md border border-white/10 mb-8 shadow-2xl relative group overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo SMAGO" class="w-full h-full object-contain drop-shadow-xl relative z-10">
                </div>
                <h1 class="text-[3rem] leading-none font-extrabold tracking-tight mb-2">SMAMGO</h1>
                <h2 class="text-xl font-medium text-blue-200 mb-2 tracking-wide">SITU-SMA Portal</h2>
                <div class="w-12 h-1 bg-indigo-500 rounded-full mb-6"></div>
                <p class="text-sm text-blue-100/70 font-light max-w-xs leading-relaxed">SMA Muhammadiyah Gorontalo<br/>Sistem Informasi Tata Usaha Terpadu</p>
            </div>
        </div>
        
        <!-- Right Side: White Login Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white relative">
            
            <!-- Top right dark circle on white -->
            <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full border-[1px] border-[#0a0f37] flex items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-[#0a0f37] ml-6 mt-6"></div>
            </div>
            
            <!-- Top left small icon on white -->
            <div class="absolute top-12 left-12 w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center">
                <div class="w-4 h-4 rounded-full bg-gray-200"></div>
            </div>
            
            <div class="text-center mb-12 relative z-10 mt-10">
                <h2 class="text-[2.5rem] font-medium text-[#0a0f37] tracking-wide relative inline-block">
                    Masuk
                    <!-- Small dot near Login -->
                    <div class="absolute bottom-2 -right-6 w-2.5 h-2.5 rounded-full bg-gray-300"></div>
                </h2>
            </div>

            <form wire:submit="login" class="space-y-6 relative z-10 w-full max-w-sm mx-auto">
                <div class="space-y-1.5">
                    <label for="email" class="block text-[13px] font-medium text-gray-500">Email / NIP</label>
                    <input wire:model="email" type="text" id="email" 
                        class="w-full bg-white border border-gray-200 focus:border-[#0a0f37] focus:ring-0 rounded-xl px-4 py-3 text-gray-800 transition-colors outline-none"
                        placeholder="">
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-[13px] font-medium text-gray-500">Kata Sandi</label>
                    <input wire:model="password" type="password" id="password" 
                        class="w-full bg-white border border-gray-200 focus:border-[#0a0f37] focus:ring-0 rounded-xl px-4 py-3 text-gray-800 transition-colors outline-none"
                        placeholder="">
                    @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input wire:model="remember" type="checkbox" class="peer sr-only">
                            <div class="w-4 h-4 rounded border border-gray-300 bg-white peer-checked:bg-[#0a0f37] peer-checked:border-[#0a0f37] transition-all duration-200"></div>
                            <svg class="absolute w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-[12px] font-medium text-gray-500 group-hover:text-gray-700 transition-colors">Ingat saya</span>
                    </label>
                    <a href="#" class="text-[11px] font-medium text-[#0a0f37] hover:underline">Lupa kata sandi?</a>
                </div>

                <div class="pt-6 pb-2 flex justify-center">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-[#0a0f37] text-white px-14 py-3 rounded-lg font-medium text-lg hover:bg-opacity-90 active:scale-95 transition-all w-auto flex items-center justify-center min-w-[160px]">
                        
                        <span wire:loading.remove wire:target="login">
                            Masuk
                        </span>

                        <span wire:loading wire:target="login">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-12 text-[11px] text-gray-400 font-medium relative z-10 mb-6">
                Belum memiliki akun? <a href="#" class="text-[#0a0f37] font-semibold hover:underline">Hubungi Admin</a>
            </div>
        </div>
    </div>
</div>
