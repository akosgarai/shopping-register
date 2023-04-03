<?php

namespace App\Http\Controllers;

use App\Models\Basket;

use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
        $lastBasketsModel = $this->lastBasketsColumnChart(5);
        return view('home', compact('lastBasketsModel'));
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
            $columnChartModel->addColumn($baskets[$index]->date, $baskets[$index]->total, fake()->hexcolor(), ['tooltip' => $baskets[$index]->total . ' HUF']);
        }
        return $columnChartModel;
    }
}
