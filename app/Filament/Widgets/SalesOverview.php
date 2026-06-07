<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $paidOrders = Order::query()->whereIn('payment_status', ['paid', 'cod_pending']);
        $revenue = (clone $paidOrders)->sum('total');
        $ordersToday = Order::query()->whereDate('created_at', today())->count();
        $averageOrderValue = (clone $paidOrders)->avg('total') ?: 0;
        $topEmirate = Order::query()
            ->selectRaw('emirate, count(*) as orders_count')
            ->groupBy('emirate')
            ->orderByDesc('orders_count')
            ->value('emirate') ?: 'n/a';

        return [
            Stat::make('Total revenue', 'AED '.number_format((float) $revenue, 2))
                ->description('Paid and COD-pending order value')
                ->color('success'),
            Stat::make("Today's orders", (string) $ordersToday)
                ->description('Orders placed today')
                ->color('primary'),
            Stat::make('Pending orders', (string) Order::query()->whereIn('status', ['pending_payment', 'processing'])->count())
                ->description('Need payment or fulfilment action')
                ->color('warning'),
            Stat::make('Failed payments', (string) Payment::query()->where('status', 'failed')->count())
                ->description('Webhook-visible failed payments')
                ->color('danger'),
            Stat::make('Average order value', 'AED '.number_format((float) $averageOrderValue, 2))
                ->description('Across paid orders')
                ->color('info'),
            Stat::make('Low stock products', (string) Product::query()->where('stock_quantity', '<=', 5)->count())
                ->description('Top emirate: '.str_replace('_', ' ', $topEmirate))
                ->color('danger'),
        ];
    }
}
