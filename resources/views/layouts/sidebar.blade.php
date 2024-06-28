<div class="flex-grow flex-col bg-white w-64 xl:fixed xl:top-0 xl:bottom-0 xl:left-0 xl:ml-0 xl:mr-0">
    <div id="app-brand" class="w-full h-16 mt-3 px-8">
        <a href="#" class="flex items-center">
            <img src="{{ asset('assets/logos/logo-management.png') }}" alt="" srcset="" class="object-cover">
        </a>
    </div>
    <ul id="menu-inner" class="flex flex-col flex-auto items-center justify-start m-0 p-0 pt-6 h-full relative overflow-hidden touch-auto py-1">
        <x-menu-item-single>
            <i class="menu-icon active-icon fa-solid fa-house"></i>
            <div class="text-slate-400">Dashboard</div>
        </x-menu-item-single>
    </ul>
</div>
