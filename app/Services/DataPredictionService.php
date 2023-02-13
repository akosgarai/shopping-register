<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

use App\Models\Address;
use App\Models\Basket;
use App\Models\Company;
use App\Models\Item;
use App\Models\Shop;

class DataPredictionService
{
    // the suggestion for the basket based on the id.
    public function getBasketSuggestions($userId, $basketId, $limit = null): Collection
    {
        $query =  Basket::selectRaw('baskets.*, levenshtein(baskets.receipt_id, ?) as distance', [$basketId])
            ->where('user_id', $userId)
            ->with('shop', 'shop.address', 'shop.company', 'shop.company.address', 'basketItems.item')
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
    public function getCompanySuggestions($taxNumber, $limit = null): Collection
    {
        $formattedTaxNumber = $this->formatTaxNumber($taxNumber);
        $query = Company::selectRaw('companies.*, levenshtein(companies.tax_number, ?) as distance', [$formattedTaxNumber])
            ->with('address')
            ->orderBy('distance');
        if ($limit) {
            $query->limit($limit);
        }
        $result = $query->get();
        $result->each(function ($item) use ($formattedTaxNumber) {
            $item->distance = (int) $item->distance;
            $length = max(strlen($formattedTaxNumber), strlen($item->tax_number));
            $item->percentage = (int) (100 - ($item->distance / $length) * 100);
        });
        return $result;
    }

    public function getAddressSuggestions($addressRaw, $limit = null): Collection
    {
        $query = Address::selectRaw('addresses.*, levenshtein(addresses.raw, ?) as distance', [$addressRaw])
            ->orderBy('distance');
        if ($limit) {
            $query->limit($limit);
        }
        $result = $query->get();
        $result->each(function ($item) use ($addressRaw) {
            $item->distance = (int) $item->distance;
            $length = max(strlen($addressRaw), strlen($item->raw));
            $item->percentage = (int) (100 - ($item->distance / $length) * 100);
        });
        return $result;
    }

    // the suggestion for the company based on the tax number.
    public function getShopSuggestions($companyId, $shopName, $limit = null): Collection
    {
        $query = Shop::selectRaw('shops.*, levenshtein(shops.name, ?) as distance', [$shopName])
            ->with('address')
            ->where('company_id', $companyId)
            ->orderBy('distance');
        if ($limit) {
            $query->limit($limit);
        }
        $result = $query->get();
        $result->each(function ($item) use ($shopName) {
            $item->distance = (int) $item->distance;
            $length = max(strlen($shopName), strlen($item->name));
            $item->percentage = (int) (100 - ($item->distance / $length) * 100);
        });
        return $result;
    }

    public function getItemSuggestions($itemRaw, $limit = null): Collection
    {
        $query = Item::selectRaw('items.*, levenshtein(items.name, ?) as distance', [$itemRaw])
            ->orderBy('distance');
        if ($limit) {
            $query->limit($limit);
        }
        $result = $query->get();
        $result->each(function ($item) use ($itemRaw) {
            $item->distance = (int) $item->distance;
            $length = max(strlen($itemRaw), strlen($item->raw));
            $item->percentage = (int) (100 - ($item->distance / $length) * 100);
        });
        return $result;
    }

    private function formatTaxNumber($taxNumber)
    {
        return preg_replace('/[^0-9]/', '', $taxNumber);
    }
}
