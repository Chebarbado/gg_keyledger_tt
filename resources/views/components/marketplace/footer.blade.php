<footer class="rounded-[18px] bg-white px-6 pb-6 pt-6 shadow-[0px_20px_25px_rgba(20,30,60,0.08)]">
    <nav class="flex flex-wrap justify-center gap-x-7 gap-y-2 border-b border-[#eef1f6] pb-6">
        @foreach (['Стать продавцом', 'Бонусы', 'Поддержка', 'Гарантии', 'Отзывы'] as $link)
            <a href="#" class="text-[15px] font-bold text-[#374151] hover:text-black">{{ $link }}</a>
        @endforeach
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-4 py-5">
        <div class="flex items-center gap-3.5">
            <a href="#" class="size-[34px]"><img src="{{ asset('images/home/vk.svg') }}" alt="VK" class="size-full" width="34" height="34"></a>
            <a href="#" class="size-[34px]"><img src="{{ asset('images/home/tg-social.svg') }}" alt="Telegram" class="size-full" width="34" height="34"></a>
            <a href="#" class="size-[34px]"><img src="{{ asset('images/home/tiktok-social.svg') }}" alt="TikTok" class="size-full" width="34" height="34"></a>
            <a href="#" class="flex size-[34px] items-center justify-center rounded-[10px] bg-[#f3f5f9]">
                <img src="{{ asset('images/home/youtube.svg') }}" alt="YouTube" class="size-[18px]" width="18" height="18">
            </a>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="flex h-7 min-w-[49px] items-center justify-center rounded-lg border border-[#e7eaf0] bg-white px-3 text-sm font-black italic tracking-tight text-[#1a1f71] shadow-sm">VISA</span>
            <span class="flex h-7 min-w-[49px] items-center justify-center rounded-lg border border-[#e7eaf0] bg-white px-3 text-[12.9px] font-extrabold tracking-tight text-[#0f9d58] shadow-sm">мир</span>
            <span class="flex h-7 items-center gap-0.5 rounded-lg border border-[#e7eaf0] bg-white px-3 shadow-sm">
                <span class="size-[17px] rounded-full bg-[#eb001b]"></span>
                <span class="size-[17px] rounded-full bg-[#f79e1b] mix-blend-multiply"></span>
            </span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 border-t border-[#eef1f6] pt-5">
        @foreach (['Политика конфиденциальности', 'Соглашение', 'Договор-оферта'] as $link)
            <a href="#" class="text-sm font-semibold text-[#6b7486] hover:text-[#374151]">{{ $link }}</a>
        @endforeach
    </div>
</footer>
