<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Address;
use App\Models\Company;

class CompanyCrud extends CrudPage
{
    public const PANEL_NAME = 'companyPanel';
    public $templateName = 'livewire.company-crud';

    public $companyName = '';
    public $companyTaxNumber = '';
    public $companyAddress = '';

    protected $listeners = [
        'company.create' => 'saveNew',
        'company.update' => 'update',
        'company.delete' => 'delete',
        'action.back' => 'clearAction',
    ];

    public function delete($modelId)
    {
        $company = Company::where('id', $modelId)
            ->withCount('shops')
            ->first();
        if ($company != null && $company->shops_count == 0) {
            $company->delete();
        }
        parent::clearAction();
    }

    public function getTemplateParameters()
    {
        return [
            'companies' =>  Company::withCount('shops')->with('address')->get(),
            'addresses' =>  Address::all()
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
        case parent::ACTION_CREATE:
            $this->companyName = '';
            $this->companyTaxNumber = '';
            $this->companyAddress = '';
            $this->createdAt = '';
            $this->updatedAt = '';
            break;
        case parent::ACTION_READ:
        case parent::ACTION_UPDATE:
        case parent::ACTION_DELETE:
            $company = Company::find($this->modelId);
            $this->companyName = $company->name;
            $this->companyTaxNumber = $company->tax_number;
            $this->companyAddress = $company->address_id;
            $this->createdAt = $company->created_at;
            $this->updatedAt = $company->updated_at;
            break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('panel.update', self::PANEL_NAME, [
            'action' => $this->action,
            'company' => [
                'companyName' => $this->companyName,
                'companyTaxNumber' => $this->companyTaxNumber,
                'companyAddress' => $this->companyAddress,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ],
            'addresses' => Address::all(),
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        if (array_key_exists('companyName', $model)) {
            $this->companyName = $model['companyName'];
        }
        if (array_key_exists('companyTaxNumber', $model)) {
            $this->companyTaxNumber = $model['companyTaxNumber'];
        }
        if (array_key_exists('companyAddress', $model)) {
            $this->companyAddress = $model['companyAddress'];
        }
        $this->validate([
            'companyName' => 'required|string',
            'companyTaxNumber' => 'required|string|unique:companies,tax_number|digits:11',
            'companyAddress' => 'required|string|exists:addresses,id',
        ]);
        $company = Company::firstOrCreate([
            'name' => $this->companyName,
            'tax_number' => $this->companyTaxNumber,
            'address_id' => $this->companyAddress,
        ]);
        $this->modelId = $company->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        if (array_key_exists('companyName', $model)) {
            $this->companyName = $model['companyName'];
        }
        if (array_key_exists('companyTaxNumber', $model)) {
            $this->companyTaxNumber = $model['companyTaxNumber'];
        }
        if (array_key_exists('companyAddress', $model)) {
            $this->companyAddress = $model['companyAddress'];
        }
        $this->validate([
            'modelId' => 'required|integer|exists:companies,id',
            'companyName' => 'required|string',
            'companyTaxNumber' => 'required|string|digits:11',
            'companyAddress' => 'required|integer|exists:addresses,id',
        ]);
        Company::where('id', $this->modelId)->update([
            'name' => $this->companyName,
            'tax_number' => $this->companyTaxNumber,
            'address_id' => $this->companyAddress,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }
}
