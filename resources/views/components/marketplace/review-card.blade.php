@props([
    'author' => 'Bizidin',
    'time' => 'Сегодня в 11:48',
    'text' => 'Отзывчивый и приятный продавец, помог не только с товаром но и с другим вопросом. Рекомендую!',
    'product' => '🌸 FunTime | Полностью готовый сервер под ключ ⚡',
    'price' => '139₽',
])

<article class="flex flex-col gap-4 rounded-3xl border border-[#f2f4f6] bg-white p-5 shadow-[0px_8px_13px_rgba(15,23,42,0.06)]">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/home/avatar.png') }}" alt="" class="size-12 rounded-full object-cover" width="48" height="48">
            <div>
                <p class="text-base font-extrabold text-heading">{{ $author }}</p>
                <div class="flex items-center gap-2">
                    <span class="text-[15px] tracking-wide text-black">★★★★★</span>
                    <span class="text-sm font-bold text-heading">5.0</span>
                </div>
            </div>
        </div>
        <time class="text-[13px] font-medium text-[#9ca3af]">{{ $time }}</time>
    </div>

    <p class="rounded-2xl bg-[#f1f2f4] p-4 text-sm font-medium leading-[21px] text-[#6b7280]">{{ $text }}</p>

    <div class="flex items-center gap-3">
        <img src="{{ asset('images/home/product.png') }}" alt="" class="h-[52px] w-16 rounded-lg object-cover" width="64" height="52">
        <p class="min-w-0 flex-1 text-sm font-medium leading-tight text-heading">{{ $product }}</p>
        <span class="rounded-lg bg-[#f1f2f4] px-3 py-1.5 text-sm font-bold text-heading">{{ $price }}</span>
    </div>
</article>
