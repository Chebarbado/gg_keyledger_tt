Админка и ручная выдача /admin/orders?token=dev-admin-token

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

Или все тесты сразу (php artisan test) : PaymentWebhookTest, KeyDeliveryRaceTest, PromoCodeRaceTest, OutOfStockRecoveryTest
