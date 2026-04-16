<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AI Smart Logistics') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            a { text-decoration: none !important; }
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 10px; }
            .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.3); }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="flex h-screen overflow-hidden">

            <aside class="w-64 bg-[#1a233a] text-white flex flex-col shrink-0 z-20 shadow-xl transition-all duration-300">
                <div class="h-16 flex items-center px-6 border-b border-white/5 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 w-full hover:no-underline">
                        <x-application-logo />
                        <span class="text-base font-bold tracking-wider text-white uppercase drop-shadow-md">AI Logistics</span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">

                    <a href="{{ request()->routeIs('dashboard') ? 'javascript:void(0)' : route('dashboard') }}"
                       class="flex items-center px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white cursor-default' : 'text-gray-300 hover:bg-white/5 hover:text-white' }} rounded-lg font-medium transition-all group">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Tổng quan
                    </a>

                    @php
                        // Cập nhật để nhận diện 4 trang trong nhóm Đơn hàng (đã thay dashboard thành pending)
                        $isOrderActive = request()->routeIs('pending') ||
                                         request()->routeIs('processing') ||
                                         request()->routeIs('status') ||
                                         request()->routeIs('history');
                    @endphp

                    <div class="space-y-1">
                        <button type="button" onclick="toggleSidebarMenu('menu-donhang', 'icon-donhang')" class="w-full flex items-center justify-between px-3 py-2.5 {{ $isOrderActive ? 'text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }} rounded-lg font-medium transition-all focus:outline-none group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ $isOrderActive ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Đơn hàng
                            </div>
                            <svg id="icon-donhang" class="w-4 h-4 {{ $isOrderActive ? 'text-white rotate-180' : 'text-gray-500 group-hover:text-white' }} transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div id="menu-donhang" class="{{ $isOrderActive ? '' : 'hidden' }} pl-11 space-y-1 py-1 transition-all">

                            <a href="{{ request()->routeIs('pending') ? 'javascript:void(0)' : route('pending') }}"
                               class="block py-2 text-sm {{ request()->routeIs('pending') ? 'text-blue-400 font-bold cursor-default' : 'text-gray-400 hover:text-white' }} transition-colors">
                               Chờ xác nhận
                            </a>

                            <a href="{{ request()->routeIs('processing') ? 'javascript:void(0)' : route('processing') }}"
                               class="block py-2 text-sm {{ request()->routeIs('processing') ? 'text-blue-400 font-bold cursor-default' : 'text-gray-400 hover:text-white' }} transition-colors">
                               Đang xử lý
                            </a>

                            <a href="{{ request()->routeIs('status') ? 'javascript:void(0)' : route('status') }}"
                               class="block py-2 text-sm {{ request()->routeIs('status') ? 'text-blue-400 font-bold cursor-default' : 'text-gray-400 hover:text-white' }} transition-colors">
                               Trạng thái
                            </a>

                            <a href="{{ request()->routeIs('history') ? 'javascript:void(0)' : route('history') }}"
                               class="block py-2 text-sm {{ request()->routeIs('history') ? 'text-blue-400 font-bold cursor-default' : 'text-gray-400 hover:text-white' }} transition-colors">
                               Lịch sử đơn hàng
                            </a>

                        </div>
                    </div>
                </nav>

                <div class="p-4 border-t border-white/5 shrink-0 bg-[#151c2f]">
                    <div class="flex items-center px-3 gap-3">
                         <span class="flex h-2.5 w-2.5 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500 shadow-[0_0_8px_#4ade80]"></span>
                        </span>
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">AI System Active</span>
                    </div>
                </div>
            </aside>

            <div class="flex-1 flex flex-col min-w-0 bg-white">
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0 shadow-sm z-10">
                    <div class="flex-1 flex items-center">
                        <div class="relative w-full max-w-lg">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" placeholder="Tìm kiếm mã đơn, SĐT... (Ctrl + K)" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <div class="ml-4 flex items-center gap-6">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="text-[10px] text-blue-600 font-bold uppercase tracking-widest">Admin Control</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50">
                    <div class="p-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            function toggleSidebarMenu(menuId, iconId) {
                const menu = document.getElementById(menuId);
                const icon = document.getElementById(iconId);
                if (menu.classList.contains('hidden')) {
                    menu.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    menu.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            }
        </script>
    </body>
</html>
