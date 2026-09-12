@props([
    'sku',
    'title',
    'price',
    'oldPrice' => null,
    'image' => 'images/home/product.png',
    'stock' => 1,
])

@php
    $soldOut = (int) $stock < 1;
@endphp

<article
    class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-[0px_11.528px_24.703px_rgba(20,40,80,0.1)] transition duration-200 hover:-translate-y-1 hover:shadow-[0px_16px_28px_rgba(20,40,80,0.16)] hover:outline hover:outline-1 hover:outline-[#dfe5ef]"
    data-product-card
    data-sku="{{ $sku }}"
    data-stock="{{ (int) $stock }}"
    data-price="{{ (int) $price }}"
>
    <div class="h-[152px] overflow-hidden">
        <img src="{{ asset($image) }}" alt="" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]" width="227" height="152">
    </div>
    <div class="flex flex-col gap-3 p-3.5">
        <h3 class="min-h-[30px] text-[11px] font-extrabold leading-[14.5px] text-[#14181d]">{{ $title }}</h3>
        <div class="flex items-baseline gap-2">
            <span data-product-price class="text-xl font-bold text-price">{{ number_format($price, 0, ',', ' ') }} ₽</span>
            @if ($oldPrice)
                <span class="text-[11.5px] font-bold text-[#9ca3af] line-through">{{ number_format($oldPrice, 0, ',', ' ') }} ₽</span>
            @endif
        </div>
        <p data-product-stock class="text-[11px] font-semibold text-muted">
            {{ $soldOut ? 'Нет в наличии' : 'В наличии: '.(int) $stock }}
        </p>
        <button
            type="button"
            data-buy-button
            data-sku="{{ $sku }}"
            @disabled($soldOut)
            class="h-[43px] rounded-[11.5px] bg-black text-xs font-extrabold text-white transition hover:bg-[#222] disabled:cursor-not-allowed disabled:opacity-60"
        >
            {{ $soldOut ? 'Раскупили' : 'Купить' }}
        </button>
    </div>
</article>
