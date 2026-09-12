PHP 8.3 + Laravel 13, БД: SQLite, CSS,  Vite 8 + vanilla JS 

# Магазин цифровых товаров (KeyLedger)

Laravel + SQLite. Витрина → заказ → вебхук → выдача ключа.

## Быстрый старт (Docker)

Нужен только установленный Docker Desktop / Docker Engine.

```bash
docker compose up --build
```

Открыть: http://localhost:8000

Админка: http://localhost:8000/admin/orders?token=secret

Остановка:

```bash
docker compose down
```

Сброс БД (удалить volume и поднять заново):

```bash
docker compose down -v
docker compose up --build
```

Готовые значения в `docker-compose.yml`:

| Переменная | Значение |
|------------|----------|
| `APP_KEY` | `base64:YgBs7PrUgmEkbHc/BIOYdQl6rUw09srqJ8w0RMOF/1s=` |
| `ADMIN_TOKEN` | `secret` |
| `APP_URL` | `http://localhost:8000` |
| `RUN_SEED` | `true` (товары, ключи, промокоды) |

Порт снаружи можно сменить: `APP_PORT=8080 docker compose up --build`

---

## Локально без Docker

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

---

## API

| Метод | Путь | Назначение |
|--------|------|------------|
| `GET` | `/api/products` | Список товаров |
| `POST` | `/api/orders` | Создать заказ |
| `GET` | `/api/orders/{id}` | Статус JSON |
| `POST` | `/api/orders/{id}/pay` | Эмуляция оплаты → вебхук |
| `POST` | `/webhook/payment` | Вход от «платёжки» |
| `POST` | `/admin/orders/{id}/retry-delivery` | Ручная выдача |

Вебхуки пишутся в `payment_events` с unique `event_id`. Если вебхук пришёл раньше заказа — событие pending и догоняется при create.

---

## Проверка гонок

1. Создать заказ через UI, **не** оплачивать, взять `order_id` (например `ord_abc123xyz`).
2. Запустить:

```bash
# внутри контейнера:
docker compose exec app php artisan test:race-webhooks ord_abc123xyz --count=50

php artisan test:race-webhooks ord_abc123xyz --count=50 --event-id=evt_same
php artisan test:race-promo LIMIT3 --attempts=10
php artisan test
```

---

## Render

Инструкция: [RENDER.md](./RENDER.md)

Для проверки на сервере: 

Создать заказ: 

curl -s -X POST "http://194.87.26.51:8088/api/orders" ^
  -H "Content-Type: application/json" -H "Accept: application/json" ^
  -d "{\"sku\":\"STEAM-TOPUP-500\",\"idempotency_key\":\"demo-1\"}"

Вебхук:

curl -s -X POST "http://194.87.26.51:8088/webhook/payment" ^
  -H "Content-Type: application/json" -H "Accept: application/json" ^
  -d "{\"event_id\":\"evt_demo_001\",\"order_id\":\"ord_XXXX\",\"status\":\"paid\",\"amount\":500,\"currency\":\"RUB\"}"
