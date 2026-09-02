<header class="sticky top-0 z-50 border-b border-[#f2f4f7] bg-white">
    <div class="relative mx-auto max-w-[1200px] px-4">
        <div class="flex h-20 items-center gap-4">
            <button type="button" data-catalog-toggle class="flex h-11 shrink-0 items-center gap-2 rounded-[10px] bg-black px-5 text-sm font-semibold text-white">
                <img src="{{ asset('images/home/catalog.svg') }}" alt="" class="size-5" width="20" height="20">
                Каталог
            </button>

            <div class="relative flex min-w-0 flex-1 items-center">
                <div class="absolute inset-0 rounded-[10px] bg-black"></div>
                <div class="relative m-1 flex h-10 flex-1 items-center rounded-lg bg-white pr-1">
                    <input
                        type="search"
                        placeholder="Игра, приложение или услуга..."
                        class="min-w-0 flex-1 bg-transparent px-4 text-xs font-semibold text-[#76829b] outline-none placeholder:text-[#76829b]"
                    >
                    <button type="button" class="mr-1 flex size-8 items-center justify-center rounded-md bg-[#eff1f5]">
                        <img src="{{ asset('images/home/favorite.svg') }}" alt="Избранное" class="h-[13px] w-3.5" width="14" height="13">
                    </button>
                    <button type="button" class="flex size-10 items-center justify-center rounded-lg bg-black">
                        <img src="{{ asset('images/home/search.svg') }}" alt="Поиск" class="size-5" width="20" height="20">
                    </button>
                </div>
            </div>

            <button type="button" class="flex size-11 shrink-0 items-center justify-center rounded-[10px] bg-[#f2f4f7]">
                <img src="{{ asset('images/home/profile.svg') }}" alt="Профиль" class="size-5" width="20" height="20">
            </button>
        </div>

        <div data-catalog-menu class="absolute left-4 top-[calc(100%-8px)] z-50 hidden w-72 rounded-xl border border-[#e8eaed] bg-white p-4 shadow-lg">
            <p class="mb-3 text-sm font-bold text-heading">Каталог</p>
            <ul class="space-y-2 text-sm font-semibold text-text">
                <li><a href="#" class="block rounded-lg px-3 py-2 hover:bg-page">Донат</a></li>
                <li><a href="#" class="block rounded-lg px-3 py-2 hover:bg-page">Подписки</a></li>
                <li><a href="#" class="block rounded-lg px-3 py-2 hover:bg-page">Предметы</a></li>
                <li><a href="#" class="block rounded-lg px-3 py-2 hover:bg-page">Аккаунты</a></li>
                <li><a href="#" class="block rounded-lg px-3 py-2 hover:bg-page">Ключи</a></li>
                <li><a href="#" class="block rounded-lg px-3 py-2 hover:bg-page">Игровая валюта</a></li>
            </ul>
        </div>
    </div>
</header>
