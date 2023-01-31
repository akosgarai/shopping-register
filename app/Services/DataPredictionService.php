<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Basket;

class DataPredictionService
{
    // the suggestion for the basket based on the id.
    public function getBasketSuggestions($userId, $basketId, $limit = null): Collection
    {
        $query =  Basket::selectRaw('baskets.*, levenshtein(baskets.receipt_id, ?) as distance', [$basketId])
            ->where('user_id', $userId)
            ->orderBy('distance');
        if ($limit) {
            $query->limit($limit);
        }
        $result = $query->get();
        $result->each(function ($item) use ($basketId) {
            $item->distance = (int) $item->distance;
            $length = max(strlen($basketId), strlen($item->receipt_id));
            $item->percentage = (int) (100 - ($item->distance / $length) * 100);
        });
        return $result;
    }

    // the suggestion for the company based on the tax number.
    public function getCompanySuggestions($taxNumber): Collection
    {
        return Company::selectRaw('companies.*, levenshtein(companies.tax_number, ?) as distance', [$this->formatTaxNumber($taxNumber)])
            ->orderBy('distance')
            ->get();
    }

    private function formatTaxNumber($taxNumber)
    {
        return preg_replace('/[^0-9]/', '', $taxNumber);
    }
}
