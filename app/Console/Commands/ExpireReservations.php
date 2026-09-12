<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire';

    protected $description = 'Снимает просроченные брони и возвращает товар на склад';

    public function handle(OrderService $orders): int
    {
        $n = $orders->expireReservations();
        $this->info("Expired reservations: {$n}");

        return self::SUCCESS;
    }
}
