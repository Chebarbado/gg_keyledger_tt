<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тесты витрины — остатки и цены</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-page p-6 font-sans text-text">
    <main class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-heading">Тесты: остатки и цены</h1>
                <p class="mt-1 text-sm text-muted">
                    Меняешь значение → витрина подхватывает через polling (~1.5 с), без F5.
                </p>
            </div>
            <a href="{{ url('/') }}" class="text-sm font-semibold text-heading underline" target="_blank" rel="noopener">
                Открыть витрину
            </a>
        </div>

        <div class="rounded-xl border border-[#e8eaed] bg-white/80 px-4 py-3 text-sm text-muted">
            Быстрые сценарии:
            <button type="button" data-quick-sku="KEY-GTA5" data-quick-stock="1" class="ml-2 rounded-lg bg-[#eef1f6] px-2 py-1 text-xs font-bold text-heading">GTA5 stock=1</button>
            <button type="button" data-quick-sku="KEY-GTA5" data-quick-stock="0" class="rounded-lg bg-[#eef1f6] px-2 py-1 text-xs font-bold text-heading">GTA5 stock=0</button>
            <button type="button" data-quick-sku="KEY-CS2-PRIME" data-quick-price="999" class="rounded-lg bg-[#eef1f6] px-2 py-1 text-xs font-bold text-heading">CS2 price=999</button>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-page text-xs uppercase text-muted">
                    <tr>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Название</th>
                        <th class="px-4 py-3">Цена ₽</th>
                        <th class="px-4 py-3">Остаток</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-t border-[#eef1f6]" data-row data-sku="{{ $product->sku }}">
                            <td class="px-4 py-3 font-mono text-xs font-semibold">{{ $product->sku }}</td>
                            <td class="px-4 py-3">{{ $product->name }}</td>
                            <td class="px-4 py-3">
                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    data-price
                                    value="{{ $product->price }}"
                                    class="w-28 rounded-lg border border-[#e8eaed] px-2 py-1.5 text-sm font-semibold outline-none focus:border-black"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    data-stock
                                    value="{{ $product->stock }}"
                                    class="w-24 rounded-lg border border-[#e8eaed] px-2 py-1.5 text-sm font-semibold outline-none focus:border-black"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    type="button"
                                    data-save
                                    class="rounded-lg bg-black px-3 py-2 text-xs font-bold text-white disabled:opacity-50"
                                >
                                    Сохранить
                                </button>
                                <span data-status class="ml-2 text-xs text-muted"></span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const TOKEN = @json($adminToken);

        async function saveSku(sku, price, stock, statusEl, btn) {
            if (btn) btn.disabled = true;
            if (statusEl) statusEl.textContent = '…';
            try {
                const res = await fetch('/api/admin/products/' + encodeURIComponent(sku) + '?token=' + encodeURIComponent(TOKEN), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Admin-Token': TOKEN,
                    },
                    body: JSON.stringify({
                        price: Number(price),
                        stock: Number(stock),
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || ('HTTP ' + res.status));
                }
                if (statusEl) {
                    statusEl.textContent = 'ok · v' + (data.event?.version ?? '');
                    statusEl.className = 'ml-2 text-xs font-semibold text-green-700';
                }
                // подтянуть значения из ответа, если сервер что-то нормализовал
                const row = document.querySelector('[data-row][data-sku="' + sku + '"]');
                if (row && data.product) {
                    row.querySelector('[data-price]').value = data.product.price;
                    row.querySelector('[data-stock]').value = data.product.stock;
                }
            } catch (e) {
                if (statusEl) {
                    statusEl.textContent = e.message || 'ошибка';
                    statusEl.className = 'ml-2 text-xs font-semibold text-red-700';
                }
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        document.querySelectorAll('[data-row]').forEach((row) => {
            const btn = row.querySelector('[data-save]');
            const statusEl = row.querySelector('[data-status]');
            btn?.addEventListener('click', () => {
                saveSku(
                    row.dataset.sku,
                    row.querySelector('[data-price]').value,
                    row.querySelector('[data-stock]').value,
                    statusEl,
                    btn,
                );
            });
        });

        // пресеты для демо на звонке
        document.querySelectorAll('[data-quick-sku]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const sku = btn.dataset.quickSku;
                const row = document.querySelector('[data-row][data-sku="' + sku + '"]');
                if (!row) {
                    alert('Нет товара ' + sku);
                    return;
                }
                if (btn.dataset.quickPrice != null) {
                    row.querySelector('[data-price]').value = btn.dataset.quickPrice;
                }
                if (btn.dataset.quickStock != null) {
                    row.querySelector('[data-stock]').value = btn.dataset.quickStock;
                }
                row.querySelector('[data-save]')?.click();
            });
        });
    </script>
</body>
</html>
