<header class="relative sticky top-0 z-50 border-b border-[#f2f4f7] bg-white">
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
    </div>

    @php
        $catalogSidebar = [
            'games' => 'Игры и игровые сервисы',
            'values' => 'Игровые ценности',
            'mobile' => 'Мобильные игры',
            'social' => 'Сервисы и соцсети',
            'software' => 'Программы',
        ];

        $catalogPanels = [
            'games' => [
                [
                    'title' => 'Steam',
                    'links' => [
                        ['label' => 'Игры и DLC'],
                        ['label' => 'Пополнение баланса', 'service' => 'Steam', 'image' => 'steam.png', 'border' => '#1482b3'],
                        ['label' => 'Подарочные карты'],
                        ['label' => "Коллекционные\nкарточки"],
                        ['label' => 'Смена региона'],
                    ],
                ],
                [
                    'title' => 'PlayStation',
                    'links' => [
                        ['label' => 'Игры и DLC'],
                        ['label' => 'Пополнение баланса', 'service' => 'PlayStation', 'image' => 'playstation.png', 'border' => '#117fda'],
                        ['label' => 'Новые аккаунты'],
                        ['label' => 'PS Plus'],
                        ['label' => 'EA Play'],
                    ],
                ],
                [
                    'title' => 'Xbox',
                    'links' => [
                        ['label' => 'Игры и DLC'],
                        ['label' => 'Пополнение баланса'],
                        ['label' => 'Новые аккаунты'],
                        ['label' => 'Xbox Game Pass'],
                        ['label' => 'Услуги'],
                    ],
                ],
                [
                    'title' => 'Nintendo',
                    'links' => [
                        ['label' => 'Игры и DLC'],
                        ['label' => 'Подарочные карты'],
                        ['label' => 'Новые аккаунты'],
                        ['label' => 'NS Online'],
                    ],
                ],
                [
                    'title' => 'Battle.net',
                    'links' => [
                        ['label' => 'World of Warcraft'],
                        ['label' => 'Подарочные карты'],
                        ['label' => 'Прямое пополнение'],
                        ['label' => 'Новые аккаунты'],
                        ['label' => 'Смена региона'],
                    ],
                ],
                [
                    'title' => 'Подборки',
                    'links' => [
                        ['label' => 'Скидки 90%'],
                        ['label' => "Популярные\nиздатели"],
                        ['label' => 'Лучшие серии игр'],
                        ['label' => 'Steam Deck'],
                        ['label' => 'Bundle-наборы'],
                    ],
                ],
            ],
            'values' => [
                [
                    'title' => 'Валюта',
                    'links' => [
                        ['label' => 'V-Bucks'],
                        ['label' => 'Robux', 'service' => 'Roblox', 'image' => 'roblox.png', 'border' => '#b8c5ff'],
                        ['label' => 'Gems'],
                        ['label' => 'Золото'],
                    ],
                ],
                [
                    'title' => 'Предметы',
                    'links' => [
                        ['label' => 'Скины'],
                        ['label' => 'Ключи'],
                        ['label' => 'Кейсы'],
                        ['label' => 'Бусты'],
                    ],
                ],
            ],
            'mobile' => [
                [
                    'title' => 'PUBG Mobile',
                    'links' => [
                        ['label' => 'UC', 'service' => 'PUBG Mobile', 'image' => 'pubg.png', 'border' => '#ffffff'],
                        ['label' => 'Аккаунты'],
                        ['label' => 'Проходки'],
                    ],
                ],
                [
                    'title' => 'Brawl Stars',
                    'links' => [
                        ['label' => 'Гемы', 'service' => 'Brawl Stars', 'image' => 'brawl-stars.png', 'border' => '#e86eff'],
                        ['label' => 'Brawl Pass'],
                        ['label' => 'Аккаунты'],
                    ],
                ],
                [
                    'title' => 'Mobile Legends',
                    'links' => [
                        ['label' => 'Алмазы'],
                        ['label' => 'Аккаунты'],
                        ['label' => 'Скины'],
                    ],
                ],
            ],
            'social' => [
                [
                    'title' => 'Telegram',
                    'links' => [
                        ['label' => 'Premium', 'service' => 'Telegram', 'image' => 'telegram.png', 'border' => '#45baee'],
                        ['label' => 'Звёзды'],
                        ['label' => 'Подарки'],
                    ],
                ],
                [
                    'title' => 'TikTok',
                    'links' => [
                        ['label' => 'Монеты', 'service' => 'TikTok', 'image' => 'tiktok.png', 'border' => '#454545'],
                        ['label' => 'Подписчики'],
                    ],
                ],
                [
                    'title' => 'ChatGPT',
                    'links' => [
                        ['label' => 'Plus', 'service' => 'ChatGPT', 'image' => 'chatgpt.png', 'border' => '#38d4ad'],
                        ['label' => 'Аккаунты'],
                    ],
                ],
            ],
            'software' => [
                [
                    'title' => 'App Store',
                    'links' => [
                        ['label' => 'Подарочные карты', 'service' => 'App Store', 'image' => 'app-store.png', 'border' => '#4acdff'],
                        ['label' => 'Подписки'],
                    ],
                ],
                [
                    'title' => 'Программы',
                    'links' => [
                        ['label' => 'Антивирусы'],
                        ['label' => 'Офис'],
                        ['label' => 'VPN'],
                    ],
                ],
            ],
        ];
    @endphp

    <div
        data-catalog-menu
        class="absolute left-0 right-0 z-50 hidden border-t border-[#f1f2f5] bg-white shadow-[0_16px_40px_rgba(20,40,80,0.12)]"
    >
        <div class="mx-auto flex min-h-[500px] max-w-[1200px]">
            <aside class="w-[280px] shrink-0 border-r border-[#f1f2f5] bg-[#f4f5f7] py-4 sm:w-[320px]">
                <nav class="flex flex-col gap-1" data-catalog-sidebar>
                    @foreach ($catalogSidebar as $key => $label)
                        <button
                            type="button"
                            data-catalog-cat="{{ $key }}"
                            @class([
                                'flex w-full items-center justify-between px-6 py-3 text-left text-sm font-medium transition',
                                'bg-white text-[#16181d]' => $key === 'games',
                                'text-[#363636] hover:bg-white/70' => $key !== 'games',
                            ])
                        >
                            <span>{{ $label }}</span>
                            <img
                                src="{{ asset('images/home/' . ($key === 'games' ? 'chevron-active.svg' : 'chevron-sidebar.svg')) }}"
                                alt=""
                                class="size-[18px]"
                                width="18"
                                height="18"
                                data-catalog-cat-chevron
                            >
                        </button>
                    @endforeach
                </nav>
            </aside>

            <div class="min-w-0 flex-1 overflow-auto bg-white px-6 py-8 sm:px-8">
                @foreach ($catalogPanels as $key => $columns)
                    <div
                        data-catalog-panel="{{ $key }}"
                        @class([
                            'grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5',
                            'hidden' => $key !== 'games',
                        ])
                    >
                        @foreach ($columns as $column)
                            <div class="min-w-0">
                                <div class="mb-4 flex items-center gap-1">
                                    <h3 class="text-base font-bold leading-6 text-[#16181d]">{{ $column['title'] }}</h3>
                                    <img src="{{ asset('images/home/chevron-heading.svg') }}" alt="" class="size-[18px]" width="18" height="18">
                                </div>
                                <ul class="space-y-3">
                                    @foreach ($column['links'] as $link)
                                        <li>
                                            <button
                                                type="button"
                                                @if (! empty($link['service']))
                                                    data-catalog-item
                                                    data-service-name="{{ $link['service'] }}"
                                                    data-service-image="{{ asset('images/home/' . $link['image']) }}"
                                                    data-service-border="{{ $link['border'] }}"
                                                @endif
                                                class="block w-full text-left text-[13.5px] font-medium leading-5 text-[#363636] hover:text-black"
                                            >
                                                {!! nl2br(e($link['label'])) !!}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</header>
