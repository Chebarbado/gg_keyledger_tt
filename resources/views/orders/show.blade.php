@extends('layouts.marketplace')

@section('title', 'Заказ '.$order->public_id)

@section('content')
    <x-marketplace.header />

    <main class="mx-auto max-w-[720px] space-y-6 px-4 py-10">
        <section class="rounded-2xl bg-white p-6 shadow-[0px_10px_17px_rgba(20,40,80,0.08)]">
            <h1 class="text-2xl font-bold text-heading">Статус заказа</h1>
            <p class="mt-2 text-sm text-muted">ID: <span class="font-semibold text-text">{{ $order->public_id }}</span></p>

            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-muted">Товар</dt>
                    <dd class="font-semibold text-text">{{ $order->product->name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-muted">SKU</dt>
                    <dd class="font-semibold text-text">{{ $order->sku }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-muted">Статус</dt>
                    <dd class="font-bold text-heading">{{ $order->status }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-muted">Сумма</dt>
                    <dd class="font-semibold text-text">{{ number_format($order->final_amount, 0, ',', ' ') }} {{ $order->currency }}</dd>
                </div>
                @if ($order->discount_amount > 0)
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted">Скидка</dt>
                        <dd class="font-semibold text-price">-{{ number_format($order->discount_amount, 0, ',', ' ') }} {{ $order->currency }}</dd>
                    </div>
                @endif
                @if ($order->issued_code)
                    <div class="rounded-xl bg-page p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-muted">Выданный ключ</p>
                        <p class="mt-2 break-all text-lg font-extrabold text-heading">{{ $order->issued_code }}</p>
                    </div>
                @endif
            </dl>

            @if ($order->status === 'created')
                <div class="mt-6">
                    <button
                        type="button"
                        data-pay-steam
                        data-order-id="{{ $order->public_id }}"
                        data-pay-result="paid"
                        class="rounded-xl bg-black px-5 py-3 text-sm font-bold text-white"
                    >
                        Оплатить
                    </button>
                </div>
            @endif

            <a href="{{ url('/') }}" class="mt-6 inline-block text-sm font-semibold text-heading underline">Вернуться на главную</a>
        </section>

        @if ($order->status === 'created')
            <div class="px-1">
                <button
                    type="button"
                    data-pay-steam
                    data-order-id="{{ $order->public_id }}"
                    data-pay-result="failed"
                    class="rounded-xl border border-[#e8eaed]/60 bg-white/40 px-4 py-2 text-xs font-medium text-muted/70 transition hover:bg-white/60 hover:text-muted"
                >
                    Оплата не прошла (тесты)
                </button>
            </div>
        @endif
    </main>
@endsection
