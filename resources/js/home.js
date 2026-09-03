document.addEventListener('DOMContentLoaded', () => {
    // баннер
    const carousel = document.querySelector('[data-banner-carousel]');
    if (carousel) {
        const maskEl = carousel.querySelector('[data-banner-mask]');
        const slides = [...carousel.querySelectorAll('[data-banner-slide]')];
        const dots = [...carousel.querySelectorAll('[data-banner-dot]')];
        let i = 0;
        let timer;

        // Figma Subtract: из баннера вырезан rounded-rect 98×48 справа сверху
        // (node Rectangle 41234). Path как в banner-mask.svg, ширина = реальная.
        const applyBannerMask = () => {
            if (!maskEl) return;
            const w = Math.max(200, Math.round(maskEl.getBoundingClientRect().width));
            const h = 263;
            const d = [
                `M${w - 110} 0`,
                `C${w - 103.37} 0 ${w - 98} 5.37258 ${w - 98} 12`,
                `V26`,
                `C${w - 98} 38.1503 ${w - 88.15} 48 ${w - 76} 48`,
                `H${w - 12}`,
                `C${w - 5.37} 48 ${w} 53.3726 ${w} 60`,
                `V${h - 12}`,
                `C${w} ${h - 5.373} ${w - 5.373} ${h} ${w - 12} ${h}`,
                `H12`,
                `C5.37258 ${h} 0 ${h - 5.373} 0 ${h - 12}`,
                `V12`,
                `C0 5.37258 5.37258 0 12 0`,
                `H${w - 110}Z`,
            ].join('');
            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}" viewBox="0 0 ${w} ${h}"><path fill="white" d="${d}"/></svg>`;
            const url = `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
            maskEl.style.webkitMaskImage = url;
            maskEl.style.maskImage = url;
            maskEl.style.webkitMaskSize = '100% 100%';
            maskEl.style.maskSize = '100% 100%';
            maskEl.style.webkitMaskRepeat = 'no-repeat';
            maskEl.style.maskRepeat = 'no-repeat';
        };

        applyBannerMask();
        window.addEventListener('resize', applyBannerMask);

        const show = (n) => {
            i = (n + slides.length) % slides.length;
            slides.forEach((s, idx) => s.classList.toggle('hidden', idx !== i));

            // на светлых слайдах (белый/жёлтый) — тёмные точки
            const light = slides[i]?.dataset.bannerLight === '1';
            dots.forEach((d, idx) => {
                d.classList.toggle('bg-white', !light && idx === i);
                d.classList.toggle('bg-white/45', !light && idx !== i);
                d.classList.toggle('bg-black', light && idx === i);
                d.classList.toggle('bg-black/35', light && idx !== i);
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

    // каталог → меняем блок «Пополнение …»
    const topupImage = document.querySelector('[data-topup-image]');
    const topupTitle = document.querySelector('[data-topup-title]');
    const topupLogin = document.querySelector('[data-topup-login]');

    const selectService = (name, image, border) => {
        if (!name) return;
        const fullName = name.replace(/\.\.\.|…/g, '').trim();
        if (topupImage) {
            topupImage.src = image;
            topupImage.alt = fullName;
            topupImage.style.borderColor = border || '#1482b3';
        }
        if (topupTitle) {
            topupTitle.textContent = 'Пополнение ' + fullName;
        }
        if (topupLogin) {
            topupLogin.placeholder = 'Логин ' + fullName;
        }
    };

    const catBtn = document.querySelector('[data-catalog-toggle]');
    const catMenu = document.querySelector('[data-catalog-menu]');
    if (catBtn && catMenu) {
        const catButtons = [...catMenu.querySelectorAll('[data-catalog-cat]')];
        const catPanels = [...catMenu.querySelectorAll('[data-catalog-panel]')];
        const chevronActive = '/images/home/chevron-active.svg';
        const chevronIdle = '/images/home/chevron-sidebar.svg';

        const showCatalogCategory = (key) => {
            catButtons.forEach((btn) => {
                const active = btn.dataset.catalogCat === key;
                btn.classList.toggle('bg-white', active);
                btn.classList.toggle('text-[#16181d]', active);
                btn.classList.toggle('text-[#363636]', !active);
                const chevron = btn.querySelector('[data-catalog-cat-chevron]');
                if (chevron) {
                    chevron.src = active ? chevronActive : chevronIdle;
                }
            });
            catPanels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.catalogPanel !== key);
            });
        };

        catBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            catMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!catMenu.contains(e.target) && !catBtn.contains(e.target)) {
                catMenu.classList.add('hidden');
            }
        });

        catButtons.forEach((btn) => {
            btn.addEventListener('mouseenter', () => showCatalogCategory(btn.dataset.catalogCat));
            btn.addEventListener('click', () => showCatalogCategory(btn.dataset.catalogCat));
        });

        catMenu.querySelectorAll('[data-catalog-item]').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectService(btn.dataset.serviceName, btn.dataset.serviceImage, btn.dataset.serviceBorder);
                catMenu.classList.add('hidden');
            });
        });
    }

    // валюты — только UI (сумма + кнопка Оплатить)
    const topupCurrencySign = document.querySelector('[data-topup-currency-sign]');
    const topupAmount = document.querySelector('[data-topup-amount]');
    const topupPayBtn = document.querySelector('[data-topup-pay]');
    const topupAmountValue = 500;

    const setTopupCurrency = (symbol) => {
        if (topupCurrencySign) {
            topupCurrencySign.textContent = symbol;
        }
        if (topupAmount) {
            topupAmount.textContent = topupAmountValue + symbol;
        }
        if (topupPayBtn) {
            topupPayBtn.textContent = 'Оплатить ' + topupAmountValue + symbol;
        }
    };

    document.querySelectorAll('[data-currency]').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-currency]').forEach((b) => {
                b.classList.remove('bg-black', 'text-white');
                b.classList.add('bg-[#e8eaed]', 'text-muted');
            });
            btn.classList.add('bg-black', 'text-white');
            btn.classList.remove('bg-[#e8eaed]', 'text-muted');
            setTopupCurrency(btn.dataset.currency || btn.textContent.trim());
        });
    });

    // иконки сервисов + hover (scale только у иконки, чтобы не резало сверху)
    document.querySelectorAll('[data-service-item]').forEach((el) => {
        const icon = el.querySelector('[data-service-icon]');
        el.addEventListener('mouseenter', () => {
            icon?.classList.add('scale-110', 'relative', 'z-20');
        });
        el.addEventListener('mouseleave', () => {
            icon?.classList.remove('scale-110', 'relative', 'z-20');
        });
        el.addEventListener('click', () => {
            if (!el.dataset.serviceName) return;
            selectService(el.dataset.serviceName, el.dataset.serviceImage, el.dataset.serviceBorder);
        });
    });

    const promoInput = document.querySelector('[data-promo-input]');

    // UUID через randomUUID только на HTTPS/localhost; на http://IP падает → «Сеть недоступна»
    const newIdempotencyKey = (sku) => {
        let uuid;
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            try {
                uuid = crypto.randomUUID();
            } catch (_) {
                uuid = null;
            }
        }
        if (!uuid && typeof crypto !== 'undefined' && crypto.getRandomValues) {
            const bytes = crypto.getRandomValues(new Uint8Array(16));
            uuid = [...bytes].map((b) => b.toString(16).padStart(2, '0')).join('');
        }
        if (!uuid) {
            uuid = Date.now().toString(36) + Math.random().toString(36).slice(2);
        }
        return 'buy-' + sku + '-' + uuid;
    };

    // Купить / Оплатить (пополнение) — один флоу: создать заказ → страница статуса
    const createOrder = async (btn) => {
        if (btn.disabled) return;
        btn.disabled = true;
        try {
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    sku: btn.dataset.sku,
                    promo_code: promoInput?.value?.trim() || null,
                    idempotency_key: newIdempotencyKey(btn.dataset.sku),
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
    };

    document.querySelectorAll('[data-buy-button]').forEach((btn) => {
        btn.addEventListener('click', () => createOrder(btn));
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
