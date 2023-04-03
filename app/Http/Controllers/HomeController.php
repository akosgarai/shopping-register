<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\QuantityUnit;

use Asantibanez\LivewireCharts\Models\ColumnChartModel;
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
        return view('home', compact('lastBasketsModel', 'lastItemsModel', 'frequentShopsModel', 'frequentItemsPcsModel'));
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
        // Gather the last $number basket connected to the current user
        $quantityUnitIdPcs = QuantityUnit::where('name', QuantityUnit::UNIT_PCS)->first()->id;

        $basketItems = BasketItem::where('quantity_unit_id', $quantityUnitIdPcs)
            ->join('baskets as b', 'b.id', '=', 'basket_items.basket_id')
            ->where('b.user_id', auth()->user()->id)
            ->with('item')
            ->selectRaw('basket_items.item_id, sum(basket_items.quantity) as quantity')
            ->groupBy('basket_items.item_id')
            ->orderBy('quantity', 'desc')
            ->take($number)
            ->get();
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
}
