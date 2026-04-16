<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Quản trị - AI Smart Logistics</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }


        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }


        .animate-float-delayed { animation: float 4s ease-in-out 2s infinite; }


        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="antialiased text-gray-900 bg-slate-50">
    <div class="flex min-h-screen">

        <div class="hidden lg:flex lg:w-[40%] relative items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1580674684081-77625272a731?q=80&w=2069&auto=format&fit=crop')] bg-cover bg-center"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/95 via-blue-800/90 to-blue-600/80 mix-blend-multiply"></div>

            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-400 rounded-full mix-blend-screen filter blur-[80px] opacity-40 animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-cyan-300 rounded-full mix-blend-screen filter blur-[80px] opacity-30 animate-pulse" style="animation-delay: 1s;"></div>

            <div class="relative z-10 text-center px-8 xl:px-12 text-white flex flex-col items-center">
                <div class="animate-float p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20 mb-8 shadow-xl">
                    <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>

                <h1 class="text-4xl xl:text-5xl tracking-tight mb-6 leading-tight drop-shadow-lg" style="font-family: 'Playfair Display', serif; font-weight: 700;">Nền Tảng Giao Hàng <br> Tự Động Hóa</h1>
                <p class="text-base text-blue-50 mb-8 font-light italic leading-relaxed" style="font-family: 'Playfair Display', serif;">"Tối ưu chi phí logistics, quản lý đơn hàng thông minh bằng hệ thống điều phối AI."</p>

                <div class="flex gap-3 mt-2">
                    <span class="animate-float px-4 py-1.5 bg-white/10 rounded-full border border-white/20 text-xs font-semibold tracking-wider text-blue-100 backdrop-blur-md uppercase shadow-lg">Fast Delivery</span>
                    <span class="animate-float-delayed px-4 py-1.5 bg-white/10 rounded-full border border-white/20 text-xs font-semibold tracking-wider text-blue-100 backdrop-blur-md uppercase shadow-lg">AI Optimization</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-[60%] flex items-center justify-center p-8 sm:p-12 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-72 h-72 bg-blue-50 rounded-full filter blur-[50px] opacity-70"></div>

            <div class="animate-fade-in-up w-full max-w-md bg-white rounded-[2rem] p-10 relative z-10 shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] border border-gray-100 opacity-0">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Admin Điều Phối</h2>
                    <p class="text-gray-500 mt-2 text-sm font-medium">Vui lòng đăng nhập để truy cập hệ thống quản trị.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email quản trị</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="block w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 outline-none">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Mật khẩu</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition duration-150">Quên mật khẩu?</a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="block w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 outline-none">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <div class="flex items-center pt-1">
                        <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer transition">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">Ghi nhớ phiên đăng nhập</label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-[0_8px_20px_-6px_rgba(37,99,235,0.5)] text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-[0_12px_20px_-6px_rgba(37,99,235,0.6)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                            ĐĂNG NHẬP VÀO HỆ THỐNG
                        </button>
                    </div>

                    @if (Route::has('register'))
                    <div class="pt-5 text-center">
                        <p class="text-sm text-gray-500 font-medium">
                            Chưa có tài khoản quản trị?
                            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-800 transition duration-150">Đăng ký ngay</a>
                        </p>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</body>
</html>
