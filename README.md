PHP 8.3 + Laravel 13, БД: SQLite, CSS,  Vite 8 + vanilla JS 

Админка /admin/orders?token=dev-admin-token

.env.example / дефолт конфига
dev-admin-token

docker-compose.yml и демо на :8088
secret

**Демо:** http://194.87.26.51:8088/  
**Админка заказов:** http://194.87.26.51:8088/admin/orders?token=secret  
**Крутилка stock/price:** http://194.87.26.51:8088/tests?token=secret  

Старт на Docker Desktop / Docker Engine:
```bash
docker compose up --build
```

Локально без Docker:
```bash
composer install
cp .env.example .env   # Windows: copy .env.example .env
php artisan key:generate
# при желании: ADMIN_TOKEN=secret и RESERVATION_TTL=120 в .env
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```


Методы API:
GET /api/products Список товаров

POST /api/orders Создать заказ

GET /api/orders/{id} Статус JSON

POST /api/orders/{id}/pay Эмуляция оплаты, вебхук 

POST /webhook/payment Вход от «платёжки» 

_____________________
`POST /admin/orders/{id}/retry-delivery`  Ручная выдача 

Вебхуки пишутся в `payment_events` с unique `event_id`, поэтому дубли безопасны; если вебхук пришёл раньше заказа — событие лежит pending и догоняется при create.

1. Создать заказ через UI (или API), не оплачивать, взять `order_id` со страницы статуса (например `ord_abc123xyz`).
2. Запустить 50 вебхуков с одним `event_id`:

php artisan test:race-webhooks ord_abc123xyz --count=50

Идемпотентность:
php artisan test:race-webhooks ord_abc123xyz --count=50 --event-id=evt_same

Промокоды:
php artisan test:race-promo LIMIT3 --attempts=10

Или все тесты сразу (php artisan test) : PaymentWebhookTest, KeyDeliveryRaceTest, PromoCodeRaceTest, OutOfStockRecoveryTest, ReservationTest, LastUnitRaceTest, CatalogRealtimeTest

Для проверки на сервере: 

Создать заказ: 

curl -s -X POST "http://194.87.26.51:8088/api/orders" ^
  -H "Content-Type: application/json" -H "Accept: application/json" ^
  -d "{\"sku\":\"STEAM-TOPUP-500\",\"idempotency_key\":\"demo-1\"}"

Вебхук:

curl -s -X POST "http://194.87.26.51:8088/webhook/payment" ^
  -H "Content-Type: application/json" -H "Accept: application/json" ^
  -d "{\"event_id\":\"evt_demo_001\",\"order_id\":\"ord_XXXX\",\"status\":\"paid\",\"amount\":500,\"currency\":\"RUB\"}"

  (На Linux/macOS `^` -> `\`.)

  1. Живое обновление витрины
## 1.Открыть две вкладки витрины: http://194.87.26.51:8088/
2.В третьей вкладке: http://194.87.26.51:8088/tests?token=secret
3.У ключа поставить stock = 0 → Сохранить.
4.Через ~1.5–2 с. на обеих вкладках витрины кнопка станет «Раскупили» (без обновления).
5.Для цены: изменить price у любого SKU → Сохранить — на карточках обновится сумма.

  2. Покупка последней единицы наперегонки
1.На /tests?token=secret у ключа поставить stock = 1 → Сохранить.
2.Две вкладки витрины — найти карточку.
3.Почти одновременно нажать Купить в обоих окнах.
Результат:
один → страница заказа со статусом reserved и таймером;
второй → алерт вроде «Товар только что раскупили» и возврат на витрину.

