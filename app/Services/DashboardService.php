<?php

namespace App\Services;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\QuantityUnit;

use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardService
{
    public const LAST_BASKETS_NUMBER = 5;
    public const FREQUENT_SHOPS_NUMBER = 5;
    public const FREQUENT_ITEMS_NUMBER = 5;
    public const BASKET_DAILY_MONTHS_NUMBER = 3;
    public const BASKET_MONTHLY_MONTHS_NUMBER = 12;

    private $itemColors = [];
    private $shopColors = [];

    public function lastBasketsColumnChart(): ColumnChartModel
    {
        // Gather maximum LAST_BASKETS_NUMBER basket connected to the current user
        $baskets = Basket::where('user_id', auth()->user()->id)
            ->orderBy('date', 'desc')
            ->take(self::LAST_BASKETS_NUMBER)
            ->get();
        $basketNumber = count($baskets);

        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Total'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        $basketColor = fake()->hexcolor();
        for ($index = $basketNumber -1 ; $index >= 0; $index--) {
            // format the date string
            $formattedDate = date_format(date_create($baskets[$index]->date), 'Y-m-d H:i');
            $columnChartModel->addColumn($formattedDate, $baskets[$index]->total, $basketColor, ['tooltip' => $this->monetaryUnitFormat($baskets[$index]->total)]);
        }
        return $columnChartModel;
    }

    public function lastBasketItemsColumnChart(): ColumnChartModel
    {
        $basket = Basket::where('user_id', auth()->user()->id)
            ->orderBy('date', 'desc')
            ->first();
        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Total'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        if ($basket != null) {
            $basketItems = BasketItem::where('basket_id', $basket->id)
                ->selectRaw('item_id, sum(quantity) as quantity, sum(price) as total, quantity_unit_id')
                ->with('item', 'quantityUnit')
                ->groupBy('item_id', 'quantity_unit_id')
                ->get();
            foreach ($basketItems as $basketItem) {
                $nameShort = (new Str())->limit($basketItem->item->name, 15);
                $quantity = $basketItem->quantity;
                if ($basketItem->quantityUnit->name == QuantityUnit::UNIT_PCS) {
                    $quantity = number_format($basketItem->quantity, 0, ',', ' ');
                }
                $tooltip = $basketItem->item->name . '<br>' . $basketItem->quantity . ' ' . $basketItem->quantityUnit->name . '<br>' . $this->monetaryUnitFormat($basketItem->total);
                $columnChartModel->addColumn($nameShort, $basketItem->total, $this->itemColor($basketItem->item_id), ['tooltip' => $tooltip]);
            }
        }
        return $columnChartModel;
    }

    public function expensesByShopsPieChart(): PieChartModel
    {
        // Gather the last $number basket connected to the current user

        $baskets = Basket::where('user_id', auth()->user()->id)
            ->selectRaw('shop_id, sum(total) as total, count(1) as baskets')
            ->groupBy('shop_id')
            ->orderBy('total', 'asc')
            ->with('shop')
            ->get();
        $numberOfShops = count($baskets);
        $pieChartModel = (new PieChartModel())
            ->setTitle(trans('Expanses By Shops'))
            ->setAnimated(true)
            ->withDataLabels();
        for ($index = 0; $index < $numberOfShops; $index++) {
            $shopName = $baskets[$index]->shop->name;
            $pieChartModel->addSlice($shopName, (float)$baskets[$index]->total, $this->shopColor($baskets[$index]->shop_id), ['tooltip' => $this->monetaryUnitFormat($baskets[$index]->total)]);
        }
        return $pieChartModel;
    }

    public function frequentShopsColumnChart(): ColumnChartModel
    {
        // Gather the FREQUENT_SHOPS_NUMBER basket connected to the current user

        $baskets = Basket::where('user_id', auth()->user()->id)
            ->selectRaw('shop_id, sum(total) as total, count(1) as baskets')
            ->groupBy('shop_id')
            ->orderBy('baskets', 'desc')
            ->with('shop')
            ->take(self::FREQUENT_SHOPS_NUMBER)
            ->get();
        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Baskets'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        for ($index = count($baskets) - 1; $index >= 0; $index--) {
            $shopName = $baskets[$index]->shop->name;
            $columnChartModel->addColumn($shopName, $baskets[$index]->baskets, $this->shopColor($baskets[$index]->shop_id));
        }
        return $columnChartModel;
    }

    public function basketsDailyMultiLineChart(): LineChartModel
    {
        $startDate = $this->now()->subMonths(self::BASKET_DAILY_MONTHS_NUMBER);
        $now = $this->now();

        $lineChartModel = (new LineChartModel())
            ->setTitle(trans('Daily Expenses'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->setSmoothCurve()
            ->setDataLabelsEnabled(false);
        while ($startDate->lessThanOrEqualTo($now)) {
            // Get the number of baskets, total, number of distinct items for the current date
            $fromDate = $startDate->format('Y-m-d');
            $toDate = $startDate->addDay()->format('Y-m-d');
            $basket = Basket::where('user_id', auth()->user()->id)
                ->where('date', '>=', $fromDate)
                ->where('date', '<', $toDate)
                ->selectRaw('count(1) as baskets, sum(total) as total')
                ->first();
            // Add the points to the chart
            // If there is no basket for the current date, add 0
            $lineChartModel->addPoint($fromDate, $basket->total ?? 0, ['tooltip' => $this->monetaryUnitFormat($basket->total ?? 0)]);
        }
        return $lineChartModel;
    }

    public function basketsMonthlyMultiLineChart(): LineChartModel
    {
        $now = $this->now();
        $startDate = $this->now()->subMonths(self::BASKET_MONTHLY_MONTHS_NUMBER);

        $lineChartModel = (new LineChartModel())
            ->setTitle(trans('Monthly Expenses'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->setSmoothCurve()
            ->setDataLabelsEnabled(false);
        while ($startDate->lessThanOrEqualTo($now)) {
            // Get the number of baskets, total, number of distinct items for the current date
            $fromDate = $startDate->format('Y-m-d');
            $toDate = $startDate->addMonth()->format('Y-m-d');
            $basket = Basket::where('user_id', auth()->user()->id)
                ->where('date', '>=', $fromDate)
                ->where('date', '<', $toDate)
                ->selectRaw('count(1) as baskets, sum(total) as total')
                ->first();
            // Add the points to the chart
            // If there is no basket for the current date, add 0
            $lineChartModel->addPoint($fromDate, $basket->total ?? 0, ['tooltip' => $this->monetaryUnitFormat($basket->total ?? 0)]);
        }
        return $lineChartModel;
    }

    public function frequentItemsColumnChart(): ColumnChartModel
    {
        $basketItems = $this->getFrequentBasketItems();
        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Quantity'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        for ($index = count($basketItems) - 1; $index >= 0; $index--) {
            $nameShort = (new Str())->limit($basketItems[$index]->item->name, 15);
            $tooltip = $basketItems[$index]->item->name . '<br>' . $this->numberFormat($basketItems[$index]->quantity);
            $columnChartModel->addColumn($nameShort, $basketItems[$index]->quantity, $this->itemColor($basketItems[$index]->item_id), ['tooltip' => $tooltip]);
        }
        return $columnChartModel;
    }

    public function frequentItemsMultiLineChart(): LineChartModel
    {
        $frequentItemIds = $this->getFrequentBasketItems()->pluck('item_id');

        $basketItems = BasketItem::join('baskets as b', 'b.id', '=', 'basket_items.basket_id')
            ->where('b.user_id', auth()->user()->id)
            ->whereIn('basket_items.item_id', $frequentItemIds)
            ->where('b.date', '>=', $this->now()->subMonths(self::BASKET_DAILY_MONTHS_NUMBER)->toDateString())
            ->with('item')
            ->selectRaw('basket_items.item_id, basket_items.unit_price, SUBSTRING(b.date, 1, 10) as date')
            ->orderBy('date', 'asc')
            ->get();
        // Define the colors array. It will be used to color the lines
        $colors = [];
        foreach ($frequentItemIds as $itemId) {
            if ($basketItems->where('item_id', $itemId)->count() > 0) {
                $colors[] = $this->itemColor($itemId);
            }
        }
        $lineChartModel = (new LineChartModel())
            ->multiLine()
            ->setTitle(trans('Price Changes'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->setSmoothCurve()
            ->setColors($colors)
            ->setDataLabelsEnabled(false);
        // Loop throught the dates of the last BASKET_DAILY_MONTHS_NUMBER months
        $date = $this->now()->subMonths(self::BASKET_DAILY_MONTHS_NUMBER);
        $now = $this->now();
        while ($date->lessThanOrEqualTo($now)) {
            // Add a point for each item
            foreach ($frequentItemIds as $itemId) {
                // if there is a price for the item on the current date, add it
                $formattedDate = $date->format('Y-m-d');
                $basketItem = $basketItems->where('item_id', $itemId)->where('date', $formattedDate)->first();
                if ($basketItem != null) {
                    $lineChartModel->addSeriesPoint($basketItem->item->name, $formattedDate, $basketItem->unit_price, ['tooltip' => $this->monetaryUnitFormat($basketItem->unit_price)]);
                    continue;
                }
                // if there is no price for the item on the current date, add the price of the closest date
                $basketItem = $basketItems->where('item_id', $itemId)->where('date', '<', $formattedDate)->first();
                if ($basketItem != null) {
                    $lineChartModel->addSeriesPoint($basketItem->item->name, $formattedDate, $basketItem->unit_price, ['tooltip' => $this->monetaryUnitFormat($basketItem->unit_price)]);
                    continue;
                }
                // if there is no price for the item on the current date, add the price of the closest date
                $basketItem = $basketItems->where('item_id', $itemId)->where('date', '>', $formattedDate)->first();
                if ($basketItem != null) {
                    $lineChartModel->addSeriesPoint($basketItem->item->name, $formattedDate, $basketItem->unit_price, ['tooltip' => $this->monetaryUnitFormat($basketItem->unit_price)]);
                    continue;
                }
            }
            $date->addDay();
        }
        return $lineChartModel;
    }

    private function shopColor($shopId)
    {
        if (!array_key_exists($shopId, $this->shopColors)) {
            $this->shopColors[$shopId] = fake()->hexcolor();
        }
        return $this->shopColors[$shopId];
    }

    private function itemColor($itemId)
    {
        if (!array_key_exists($itemId, $this->itemColors)) {
            $this->itemColors[$itemId] = fake()->hexcolor();
        }
        return $this->itemColors[$itemId];
    }

    private function now()
    {
        return (new Carbon())->now();
    }

    private function numberFormat($number)
    {
        return number_format($number, 0, ',', ' ');
    }

    private function monetaryUnitFormat($number)
    {
        return $this->numberFormat($number) . ' HUF';
    }

    private function getFrequentBasketItems()
    {
        // Gather the last $number basket connected to the current user
        $quantityUnitIdPcs = QuantityUnit::where('name', QuantityUnit::UNIT_PCS)->first()->id;

        return BasketItem::join('baskets as b', 'b.id', '=', 'basket_items.basket_id')
            ->where('b.user_id', auth()->user()->id)
            ->with('item')
            ->selectRaw('basket_items.item_id, CASE WHEN basket_items.quantity_unit_id = '.$quantityUnitIdPcs.' THEN sum(basket_items.quantity) ELSE count(1) END as quantity')
            ->groupBy('basket_items.item_id', 'basket_items.quantity_unit_id')
            ->orderBy('quantity', 'desc')
            ->take(self::FREQUENT_ITEMS_NUMBER)
            ->get();
    }
}
