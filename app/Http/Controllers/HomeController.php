<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

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
    public function index(DashboardService $dashboardService)
    {
        return view('home', [
            'lastBasketsModel' => $dashboardService->lastBasketsColumnChart(),
            'lastItemsModel' => $dashboardService->lastBasketItemsColumnChart(),
            'frequentShopsModel' => $dashboardService->frequentShopsColumnChart(),
            'frequentItemsModel' => $dashboardService->frequentItemsColumnChart(),
            'frequentItemsPriceModel' => $dashboardService->frequentItemsMultiLineChart(),
            'basketPriceModel' => $dashboardService->basketsDailyMultiLineChart(),
            'basketMonthPriceModel' => $dashboardService->basketsMonthlyMultiLineChart(),
            'expensesByShopsModel' => $dashboardService->expensesByShopsPieChart(),
        ]);
    }
}
