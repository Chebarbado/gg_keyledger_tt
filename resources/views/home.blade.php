@extends('layouts.marketplace')

@section('title', 'Главная')

@section('content')
    <x-marketplace.header />

    <main class="mx-auto max-w-[1200px] space-y-8 px-4 py-6">
        <section class="relative" data-banner-carousel>
            @php
                $bannerSlides = [
                    ['color' => '#2563eb', 'light' => false], // синий
                    ['color' => '#000000', 'light' => false], // чёрный
                    ['color' => '#ffffff', 'light' => true],  // белый
                    ['color' => '#dc2626', 'light' => false], // красный
                    ['color' => '#16a34a', 'light' => false], // зелёный
                    ['color' => '#eab308', 'light' => true],  // жёлтый
                ];
            @endphp

            <div class="relative h-[263px] w-full overflow-hidden rounded-2xl bg-white">
                {{-- маска ставится из JS: вырез 110px справа фиксированный при любой ширине --}}
                <div class="absolute inset-0" data-banner-mask>
                    @foreach ($bannerSlides as $index => $slide)
                        <div
                            data-banner-slide
                            data-banner-light="{{ $slide['light'] ? '1' : '0' }}"
                            @class(['absolute inset-0 h-full w-full', 'hidden' => $index !== 0])
                            style="background-color: {{ $slide['color'] }}"
                        ></div>
                    @endforeach
                </div>

                <div class="absolute right-0 top-0 z-20 flex h-12 w-[98px] items-center justify-center">
                    <div class="flex h-10 w-[90px] items-center justify-between rounded-full border border-[#e5e9f1] bg-[#f4f5f7] p-1">
                        <button type="button" data-banner-prev class="flex size-10 items-center justify-center rounded-full">
                            <img src="{{ asset('images/home/arrow-left.svg') }}" alt="Назад" class="size-[22px]" width="22" height="22">
                        </button>
                        <button type="button" data-banner-next class="flex size-10 items-center justify-center rounded-full">
                            <img src="{{ asset('images/home/arrow-right.svg') }}" alt="Вперёд" class="size-[22px]" width="22" height="22">
                        </button>
                    </div>
                </div>

                <div class="absolute bottom-4 z-10 flex gap-1" style="right: 16px;">
                    @foreach ($bannerSlides as $index => $slide)
                        <button
                            type="button"
                            data-banner-dot
                            @class([
                                'h-1 w-5 rounded-full',
                                'bg-white' => $index === 0,
                                'bg-white/45' => $index !== 0,
                            ])
                        ></button>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="space-y-4 overflow-visible rounded-2xl bg-white p-5 shadow-[0px_10px_17px_rgba(20,40,80,0.08)]">
            <div class="flex gap-4 overflow-x-auto px-0.5 pb-2 pt-3">
                @php
                    $services = [
                        ['name' => 'Steam', 'image' => 'steam.png', 'border' => '#1482b3'],
                        ['name' => 'Telegram', 'image' => 'telegram.png', 'border' => '#45baee'],
                        ['name' => 'Roblox', 'image' => 'roblox.png', 'border' => '#b8c5ff'],
                        ['name' => 'Brawl Stars', 'image' => 'brawl-stars.png', 'border' => '#e86eff'],
                        ['name' => 'PUBG Mob...', 'image' => 'pubg.png', 'border' => '#ffffff', 'active' => true],
                        ['name' => 'App Store', 'image' => 'app-store.png', 'border' => '#4acdff'],
                        ['name' => 'ChatGPT', 'image' => 'chatgpt.png', 'border' => '#38d4ad'],
                        ['name' => 'PlayStation', 'image' => 'playstation.png', 'border' => '#117fda'],
                        ['name' => 'TikTok', 'image' => 'tiktok.png', 'border' => '#454545'],
                        ['name' => 'Mobile Leg..', 'image' => 'mobile-legends.png', 'border' => 'rgba(255,255,255,0.45)'],
                    ];
                @endphp

                @foreach ($services as $service)
                    <button
                        type="button"
                        data-service-item
                        data-service-name="{{ $service['name'] }}"
                        data-service-image="{{ asset('images/home/' . $service['image']) }}"
                        data-service-border="{{ $service['border'] }}"
                        class="flex w-[76px] shrink-0 flex-col items-center gap-2 transition duration-200"
                    >
                        <span @class([
                            'flex size-[72px] items-center justify-center overflow-hidden rounded-2xl border-2 p-0.5 shadow-[0px_4px_8px_#d1d9e4] transition duration-200 will-change-transform',
                            'bg-black' => $service['active'] ?? false,
                        ]) style="border-color: {{ $service['border'] }}" data-service-icon>
                            <img src="{{ asset('images/home/' . $service['image']) }}" alt="{{ $service['name'] }}" class="size-full rounded-2xl object-cover" width="72" height="72">
                        </span>
                        <span class="text-base font-bold tracking-tight text-text">{{ $service['name'] }}</span>
                    </button>
                @endforeach

                <button type="button" data-service-item class="flex w-[76px] shrink-0 flex-col items-center gap-2 transition duration-200">
                    <span class="flex size-[72px] items-center justify-center rounded-2xl border-2 border-[#dfe5ef] bg-[#f2f4f7] transition duration-200 will-change-transform" data-service-icon>
                        <img src="{{ asset('images/home/more.svg') }}" alt="" class="size-7" width="28" height="28">
                    </span>
                    <span class="text-base font-bold tracking-tight text-muted">еще 841</span>
                </button>
            </div>

            <div class="h-px bg-[#e8eaed]"></div>

            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)_minmax(0,1.2fr)_200px] xl:items-center">
                <div class="flex items-center gap-3" data-topup-block>
                    <img
                        data-topup-image
                        src="{{ asset('images/home/steam.png') }}"
                        alt="Steam"
                        class="size-[72px] rounded-2xl border-2 border-[#1482b3] object-cover shadow-[0px_4px_8px_#d1d9e4]"
                        width="72"
                        height="72"
                    >
                    <div>
                        <div class="flex items-center gap-2">
                            <p data-topup-title class="text-base font-bold tracking-tight text-text">Пополнение Steam</p>
                            <span class="rounded-full bg-[#6eb83f] px-2 py-0.5 text-[11px] font-bold text-white">5%</span>
                        </div>
                        <label class="mt-1 inline-flex items-center gap-2 rounded-lg bg-[rgba(38,139,243,0.1)] px-3 py-1 text-xs font-bold text-black">
                            <span>Ввести промокод</span>
                            <input data-promo-input type="text" placeholder="WELCOME10" class="w-28 bg-transparent text-xs outline-none placeholder:text-[#8a94a6]">
                        </label>
                    </div>
                </div>

                <label class="flex h-16 max-w-[300px] items-center justify-between rounded-xl bg-page px-5">
                    <span class="flex min-w-0 flex-1 items-center gap-3">
                        <img src="{{ asset('images/home/profile.svg') }}" alt="" class="size-5 shrink-0" width="20" height="20">
                        <input data-topup-login type="text" placeholder="Логин Steam" class="w-full bg-transparent text-[15px] font-bold text-muted outline-none placeholder:text-muted">
                    </span>
                    <span class="flex size-5 shrink-0 items-center justify-center rounded-md bg-[#a0a8b5] text-xs font-bold italic text-white">i</span>
                </label>

                <div class="flex h-16 max-w-[380px] items-center justify-between rounded-xl bg-page px-4">
                    <div class="flex items-center gap-3">
                        <span data-topup-currency-sign class="text-lg font-bold text-text">$</span>
                        <div>
                            <p class="text-xs font-bold text-muted">Сумма</p>
                            <p data-topup-amount class="text-lg font-bold text-text">500$</p>
                        </div>
                    </div>
                    <div class="flex gap-1.5">
                        <button type="button" data-currency="$" class="flex size-9 items-center justify-center rounded-lg bg-black text-base font-bold text-white">$</button>
                        <button type="button" data-currency="₸" class="flex size-9 items-center justify-center rounded-lg bg-[#e8eaed] text-base font-bold text-muted">₸</button>
                        <button type="button" data-currency="₽" class="flex size-9 items-center justify-center rounded-lg bg-[#e8eaed] text-base font-bold text-muted">₽</button>
                    </div>
                </div>

                <button
                    type="button"
                    data-buy-button
                    data-sku="STEAM-TOPUP-500"
                    data-topup-pay
                    class="h-16 w-full rounded-xl bg-black text-base font-bold text-white transition hover:bg-[#222] xl:w-[200px]"
                >
                    Оплатить 500$
                </button>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <h2 class="text-xl font-bold text-heading">Популярные товары</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach (['Донат', 'Подписки', 'Предметы', 'Аккаунты', 'Ключи', 'Игровая валюта', 'Другое'] as $index => $filter)
                        <button type="button" @class([
                            'inline-flex h-[34px] items-center rounded-[10px] px-3.5 text-[13px] font-bold',
                            'bg-black text-white' => $index === 0,
                            'bg-page text-heading hover:bg-[#e8eaed]' => $index !== 0,
                        ])>{{ $filter }}</button>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach ($products as $product)
                    <x-marketplace.product-card
                        :sku="$product->sku"
                        :title="$product->name"
                        :price="$product->price"
                        :old-price="$product->price + 500"
                        :image="$product->image"
                    />
                @endforeach
            </div>
        </section>
    </main>
@endsection
