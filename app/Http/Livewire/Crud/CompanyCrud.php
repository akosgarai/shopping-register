<?php

namespace App\Http\Livewire\Crud;

use Illuminate\Validation\ValidationException;

use App\Models\Address;
use App\Models\Company;

class CompanyCrud extends CrudPage
{
    public const PANEL_NAME = 'companyPanel';
    public const ORDERABLE_COLUMNS = ['id', 'name', 'tax_number', 'created_at', 'updated_at'];
    public $templateName = 'livewire.crud.company-crud';

    public $name = '';
    public $taxNumber = '';
    public $address = '';

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
        if ($this->modelId != '') {
            $this->viewData = $this->getCompany()->toArray();
        }
        return [
            'companies' => $this->companyList(),
            'addresses' =>  $this->getAddresses(),
            'viewData' => $this->viewData,
            'panelCompany' => [
                'name' => $this->name,
                'taxNumber' => $this->taxNumber,
                'address' => $this->address,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ]
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
        case parent::ACTION_CREATE:
            $this->name = '';
            $this->taxNumber = '';
            $this->address = '';
            $this->createdAt = '';
            $this->updatedAt = '';
            break;
        case parent::ACTION_READ:
        case parent::ACTION_UPDATE:
        case parent::ACTION_DELETE:
            $company = $this->getCompany();
            $this->name = $company->name;
            $this->taxNumber = $company->tax_number;
            $this->address = $company->address_id;
            $this->createdAt = $company->created_at;
            $this->updatedAt = $company->updated_at;
            $this->viewData = $company->toArray();
            break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('crudaction.update', [
            'action' => $this->action,
            'viewData' => $this->viewData,
            'company' => [
                'name' => $this->name,
                'taxNumber' => $this->taxNumber,
                'address' => $this->address,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ],
            'addresses' => $this->getAddresses(),
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'name' => 'required|string|max:255',
            'taxNumber' => 'required|string|unique:companies,tax_number|digits:11',
            'address' => 'required|integer|exists:addresses,id',
        ]);
        $company = (new Company())->firstOrCreate([
            'name' => $this->name,
            'tax_number' => $this->taxNumber,
            'address_id' => $this->address,
        ]);
        $this->modelId = $company->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'modelId' => 'required|integer|exists:companies,id',
            'name' => 'required|string|max:255',
            'taxNumber' => 'required|string|digits:11',
            'address' => 'required|integer|exists:addresses,id',
        ]);
        Company::where('id', $this->modelId)->update([
            'name' => $this->name,
            'tax_number' => $this->taxNumber,
            'address_id' => $this->address,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }

    private function updateModelParams(array $model)
    {
        if (array_key_exists('name', $model)) {
            $this->name = $model['name'];
        }
        if (array_key_exists('taxNumber', $model)) {
            $this->taxNumber = $model['taxNumber'];
        }
        if (array_key_exists('address', $model)) {
            $this->address = $model['address'];
        }
    }

    private function getAddresses()
    {
        return (new Address())->all();
    }
    private function getCompany()
    {
        return Company::where('id', $this->modelId)
            ->with('address')
            ->first();
    }

    private function companyList()
    {
        return Company::withCount('shops')->with('address')
            ->orderBy($this->orderColumn, $this->orderDirection)
            ->paginate(parent::ITEM_LIMIT);
    }
}
