<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Address;
use App\Models\Company;

class CompanyCrud extends OffcanvasPage
{
    public $templateName = 'livewire.company-crud';

    public $companyName = '';
    public $companyTaxNumber = '';
    public $companyAddress = '';

    protected $listeners = ['offcanvasClose'];

    public function load($id)
    {
        $this->modelId = $id;
        $this->action = parent::ACTION_UPDATE;
        $company = Company::find($this->modelId);
        $this->companyName = $company->name;
        $this->companyTaxNumber = $company->tax_number;
        $this->companyAddress = $company->address_id;
        $this->createdAt = $company->created_at;
        $this->updatedAt = $company->updated_at;
    }

    public function getTemplateParameters()
    {
        return [
            'companies' =>  Company::all(),
            'addresses' =>  Address::all()
        ];
    }

    public function initialize()
    {
            $this->modelId = '';
            $this->companyName = '';
            $this->companyTaxNumber = '';
            $this->companyAddress = '';
            $this->createdAt = '';
            $this->updatedAt = '';
    }

    public function saveNew()
    {
        try {
            $this->validate([
                'companyName' => 'required|string',
                'companyTaxNumber' => 'required|string|unique:companies,tax_number|digits:11',
                'companyAddress' => 'required|string|exists:addresses,id',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'new', 'model' => 'Company', 'messages' => $messages]);
            return;
        }
        $company = Company::firstOrCreate([
            'name' => $this->companyName,
            'tax_number' => $this->companyTaxNumber,
            'address_id' => $this->companyAddress,
        ]);
        return redirect()->route('company', ['action' => 'update', 'id' => $company->id]);
    }

    public function update()
    {
        try {
            $this->validate([
                'modelId' => 'required|integer|exists:companies,id',
                'companyName' => 'required|string',
                'companyTaxNumber' => 'required|string|digits:11',
                'companyAddress' => 'required|integer|exists:addresses,id',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'update', 'model' => 'Company', 'messages' => $messages]);
            return;
        }
        Company::where('id', $this->modelId)->update([
            'name' => $this->companyName,
            'tax_number' => $this->companyTaxNumber,
            'address_id' => $this->companyAddress,
        ]);
        return redirect()->route('company', ['action' => 'update', 'id' => $this->modelId]);
    }

    public function delete($id)
    {
        $company = Company::find($id);
        if ($company != null && $company->shops->count() == 0) {
            $company->delete();
        }
    }
}
