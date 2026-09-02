document.addEventListener('DOMContentLoaded', () => {
    // баннер
    const carousel = document.querySelector('[data-banner-carousel]');
    if (carousel) {
        const slides = [...carousel.querySelectorAll('[data-banner-slide]')];
        const dots = [...carousel.querySelectorAll('[data-banner-dot]')];
        let i = 0;
        let timer;

        const show = (n) => {
            i = (n + slides.length) % slides.length;
            slides.forEach((s, idx) => s.classList.toggle('hidden', idx !== i));
            dots.forEach((d, idx) => {
                d.classList.toggle('bg-white', idx === i);
                d.classList.toggle('bg-white/45', idx !== i);
            });
        };
        const loop = () => {
            clearInterval(timer);
            timer = setInterval(() => show(i + 1), 5000); // 5 сек как в макете? вроде ок
        };

        carousel.querySelector('[data-banner-prev]')?.addEventListener('click', () => { show(i - 1); loop(); });
        carousel.querySelector('[data-banner-next]')?.addEventListener('click', () => { show(i + 1); loop(); });
        dots.forEach((d, idx) => d.addEventListener('click', () => { show(idx); loop(); }));
        show(0);
        loop();
    }

    // каталог
    const catBtn = document.querySelector('[data-catalog-toggle]');
    const catMenu = document.querySelector('[data-catalog-menu]');
    if (catBtn && catMenu) {
        catBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            catMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!catMenu.contains(e.target) && !catBtn.contains(e.target)) {
                catMenu.classList.add('hidden');
            }
        });
    }

    // валюты — только UI
    document.querySelectorAll('[data-currency]').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-currency]').forEach((b) => {
                b.classList.remove('bg-black', 'text-white');
                b.classList.add('bg-[#e8eaed]', 'text-muted');
            });
            btn.classList.add('bg-black', 'text-white');
            btn.classList.remove('bg-[#e8eaed]', 'text-muted');
        });
    });

    // hover сервисов
    document.querySelectorAll('[data-service-item]').forEach((el) => {
        el.addEventListener('mouseenter', () => el.classList.add('scale-105'));
        el.addEventListener('mouseleave', () => el.classList.remove('scale-105'));
    });

    const promoInput = document.querySelector('[data-promo-input]');

    // без disabled — заказ улетает дважды
    document.querySelectorAll('[data-buy-button]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            if (btn.disabled) return;
            btn.disabled = true;
            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        sku: btn.dataset.sku,
                        promo_code: promoInput?.value?.trim() || null,
                        idempotency_key: 'buy-' + btn.dataset.sku + '-' + crypto.randomUUID(),
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    alert(data.message || 'Ошибка создания заказа');
                    return;
                }
                location.href = '/orders/' + data.order_id;
            } catch (e) {
                alert('Сеть недоступна');
            } finally {
                btn.disabled = false;
            }
        });
    });

    document.querySelectorAll('[data-pay-steam]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            try {
                const res = await fetch('/api/orders/' + btn.dataset.orderId + '/pay', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ result: btn.dataset.payResult }),
                });
                const data = await res.json();
                if (!res.ok) {
                    alert(data.message || 'Ошибка оплаты');
                    return;
                }
                location.reload();
            } finally {
                btn.disabled = false;
            }
        });
    });
});
