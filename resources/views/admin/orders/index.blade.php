<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Админка — заказы</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-page p-6 font-sans text-text">
    <main class="mx-auto max-w-5xl space-y-6">
        <h1 class="text-2xl font-bold text-heading">Оплачено, но не выдано</h1>

        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-page text-xs uppercase text-muted">
                    <tr>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-t border-[#eef1f6]">
                            <td class="px-4 py-3 font-semibold">{{ $order->public_id }}</td>
                            <td class="px-4 py-3">{{ $order->sku }}</td>
                            <td class="px-4 py-3">{{ $order->status }}</td>
                            <td class="px-4 py-3">{{ $order->issued_code ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if (in_array($order->status, ['out_of_stock', 'delivery_failed'], true))
                                    <button
                                        type="button"
                                        class="rounded-lg bg-black px-3 py-2 text-xs font-bold text-white"
                                        onclick="retryDelivery('{{ $order->public_id }}')"
                                    >
                                        Повторить выдачу
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-muted">Нет проблемных заказов</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <script>
        async function retryDelivery(orderId) {
            const response = await fetch(`/admin/orders/${orderId}/retry-delivery`, {
                method: 'POST',
                headers: {
                    'X-Admin-Token': '{{ config('marketplace.admin_token') }}',
                    'Accept': 'application/json',
                },
            });

            const payload = await response.json();
            alert(`Статус: ${payload.status}${payload.issued_code ? `\nКлюч: ${payload.issued_code}` : ''}`);
            window.location.reload();
        }
    </script>
</body>
</html>
