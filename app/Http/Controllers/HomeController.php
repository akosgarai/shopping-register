<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\QuantityUnit;

use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{

    public const LAST_BASKETS_NUMBER = 5;
    public const FREQUENT_SHOPS_NUMBER = 5;
    public const FREQUENT_ITEMS_NUMBER = 5;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $lastBasketsModel = $this->lastBasketsColumnChart(self::LAST_BASKETS_NUMBER);
        $lastItemsModel = $this->lastBasketItemsColumnChart();
        $frequentShopsModel = $this->frequentShopsColumnChart(self::FREQUENT_SHOPS_NUMBER);
        $frequentItemsPcsModel = $this->frequentItemsPcsColumnChart(self::FREQUENT_ITEMS_NUMBER);
        $frequentItemsPriceModel = $this->frequentItemsPcsMultiLineChart(self::FREQUENT_ITEMS_NUMBER);
        $basketPriceModel = $this->basketsMultiLineChart(3);
        return view('home', compact('lastBasketsModel', 'lastItemsModel', 'frequentShopsModel', 'frequentItemsPcsModel', 'frequentItemsPriceModel', 'basketPriceModel'));
    }

    private function lastBasketsColumnChart($number): ColumnChartModel
    {
        // Gather the last $number basket connected to the current user

        $baskets = Basket::where('user_id', auth()->user()->id)
            ->orderBy('date', 'desc')
            ->take($number)
            ->get();
        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Total'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        for ($index = count($baskets) - 1; $index >= 0; $index--) {
            // format the date string
            $formattedDate = date_format(date_create($baskets[$index]->date), 'Y-m-d H:i');
            $columnChartModel->addColumn($formattedDate, $baskets[$index]->total, fake()->hexcolor(), ['tooltip' => $baskets[$index]->total . ' HUF']);
        }
        return $columnChartModel;
    }

    private function lastBasketItemsColumnChart(): ColumnChartModel
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
                $tooltip = $basketItem->item->name . '<br>' . $basketItem->quantity . ' ' . $basketItem->quantityUnit->name . '<br>' . $basketItem->total . ' HUF';
                $columnChartModel->addColumn($nameShort, $basketItem->total, fake()->hexcolor(), ['tooltip' => $tooltip]);
            }
        }
        return $columnChartModel;
    }

    private function frequentShopsColumnChart($number): ColumnChartModel
    {
        // Gather the last $number basket connected to the current user

        $baskets = Basket::where('user_id', auth()->user()->id)
            ->selectRaw('shop_id, sum(total) as total, count(1) as baskets')
            ->groupBy('shop_id')
            ->orderBy('baskets', 'desc')
            ->with('shop')
            ->take($number)
            ->get();
        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Baskets'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        for ($index = count($baskets) - 1; $index >= 0; $index--) {
            $shopName = $baskets[$index]->shop->name;
            $columnChartModel->addColumn($shopName, $baskets[$index]->baskets, fake()->hexcolor());
        }
        return $columnChartModel;
    }

    private function frequentItemsPcsColumnChart($number): ColumnChartModel
    {
        $basketItems = $this->getFrequentPcsBasketItems($number);
        $columnChartModel = (new ColumnChartModel())
            ->setTitle(trans('Quantity'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->withDataLabels();
        for ($index = count($basketItems) - 1; $index >= 0; $index--) {
            $nameShort = (new Str())->limit($basketItems[$index]->item->name, 15);
            $tooltip = $basketItems[$index]->item->name . '<br>' . number_format($basketItems[$index]->quantity, 0, ',', ' ');
            $columnChartModel->addColumn($nameShort, $basketItems[$index]->quantity, fake()->hexcolor(), ['tooltip' => $tooltip]);
        }
        return $columnChartModel;
    }

    private function frequentItemsPcsMultiLineChart($number): LineChartModel
    {
        $frequentItemIds = $this->getFrequentPcsBasketItems($number)->pluck('item_id');

        $basketItems = BasketItem::join('baskets as b', 'b.id', '=', 'basket_items.basket_id')
            ->where('b.user_id', auth()->user()->id)
            ->whereIn('basket_items.item_id', $frequentItemIds)
            ->where('b.date', '>=', Carbon::now()->subMonths(3)->toDateString())
            ->with('item')
            ->selectRaw('basket_items.item_id, basket_items.unit_price, SUBSTRING(b.date, 1, 10) as date')
            ->orderBy('date', 'asc')
            ->get();
        $lineChartModel = (new LineChartModel())
            ->multiLine()
            ->setTitle(trans('Price Changes'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->setSmoothCurve()
            ->setDataLabelsEnabled(false);
        // Loop throught the dates of the last 3 months
        $date = Carbon::now()->subMonths(3);
        $now = Carbon::now();
        while ($date->lessThanOrEqualTo($now)) {
            // Add a point for each item
            foreach ($frequentItemIds as $itemId) {
                // if there is a price for the item on the current date, add it
                $basketItem = $basketItems->where('item_id', $itemId)->where('date', $date->format('Y-m-d'))->first();
                if ($basketItem != null) {
                    $nameShort = (new Str())->limit($basketItem->item->name, 15);
                    $lineChartModel->addSeriesPoint($nameShort, $date->format('Y-m-d'), $basketItem->unit_price);
                    continue;
                }
                // if there is no price for the item on the current date, add the price of the closest date
                $basketItem = $basketItems->where('item_id', $itemId)->where('date', '<', $date->format('Y-m-d'))->first();
                if ($basketItem != null) {
                    $nameShort = (new Str())->limit($basketItem->item->name, 15);
                    $lineChartModel->addSeriesPoint($nameShort, $date->format('Y-m-d'), $basketItem->unit_price);
                    continue;
                }
                // if there is no price for the item on the current date, add the price of the closest date
                $basketItem = $basketItems->where('item_id', $itemId)->where('date', '>', $date->format('Y-m-d'))->first();
                if ($basketItem != null) {
                    $nameShort = (new Str())->limit($basketItem->item->name, 15);
                    $lineChartModel->addSeriesPoint($nameShort, $date->format('Y-m-d'), $basketItem->unit_price);
                    continue;
                }
            }
            $date->addDay();
        }
        return $lineChartModel;
    }

    private function getFrequentPcsBasketItems($number)
    {
        // Gather the last $number basket connected to the current user
        $quantityUnitIdPcs = QuantityUnit::where('name', QuantityUnit::UNIT_PCS)->first()->id;

        return BasketItem::where('quantity_unit_id', $quantityUnitIdPcs)
            ->join('baskets as b', 'b.id', '=', 'basket_items.basket_id')
            ->where('b.user_id', auth()->user()->id)
            ->with('item')
            ->selectRaw('basket_items.item_id, sum(basket_items.quantity) as quantity')
            ->groupBy('basket_items.item_id')
            ->orderBy('quantity', 'desc')
            ->take($number)
            ->get();
    }

    private function basketsMultiLineChart($beforMonths): LineChartModel
    {
        $startDate = Carbon::now()->subMonths($beforMonths);
        $now = Carbon::now();

        $lineChartModel = (new LineChartModel())
            ->setTitle(trans('Daily Expenses'))
            ->setAnimated(true)
            ->withoutLegend()
            ->withGrid()
            ->setSmoothCurve()
            ->setDataLabelsEnabled(false);
        while ($startDate->lessThanOrEqualTo($now)) {
            // Get the number of baskets, total, number of distinct items for the current date
            $basket = Basket::where('user_id', auth()->user()->id)
                ->where('date', '>=', $startDate->format('Y-m-d'))
                ->where('date', '<', $startDate->addDay()->format('Y-m-d'))
                ->selectRaw('count(1) as baskets, sum(total) as total')
                ->first();
            // Add the points to the chart
            // If there is no basket for the current date, add 0
            $lineChartModel->addPoint($startDate->format('Y-m-d'), $basket->total ?? 0, ['tooltip' => $basket->total ?? 0 . ' Ft']);
            $startDate->addDay();
        }
        return $lineChartModel;
    }
}
