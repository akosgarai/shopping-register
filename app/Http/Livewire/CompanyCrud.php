<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Address;
use App\Models\Company;

class CompanyCrud extends Component
{
    public $action = '';
    public $companyId = '';
    public $companyName = '';
    public $companyTaxNumber = '';
    public $companyAddress = '';
    public $createdAt = '';
    public $updatedAt = '';

    protected $queryString = [
        'action' => ['except' => ''],
        'companyId' => ['except' => '', 'as' => 'id'],
    ];

    public function mount()
    {
        $this->action = request()->query('action', '');
        $id = request()->query('id', '');
        if ($id != '') {
            $this->loadCompany($id);
        }
    }

    public function loadCompany($id)
    {
        $this->companyId = $id;
        $this->action = 'update';
        $company = Company::find($this->companyId);
        $this->companyName = $company->name;
        $this->companyTaxNumber = $company->tax_number;
        $this->companyAddress = $company->address_id;
        $this->createdAt = $company->created_at;
        $this->updatedAt = $company->updated_at;
    }

    public function render()
    {
        return view('livewire.company-crud', [ 'companies' =>  Company::all(), 'addresses' =>  Address::all() ])
            ->extends('layouts.app');
    }

    public function setAction($action)
    {
        $this->action = $action;
        if ($action != 'update') {
            $this->companyId = '';
            $this->companyName = '';
            $this->companyTaxNumber = '';
            $this->companyAddress = '';
            $this->createdAt = '';
            $this->updatedAt = '';
        }
    }

    public function saveNewCompany()
    {
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
        return redirect()->route('company', ['action' => 'update', 'id' => $company->id]);
    }

    public function updateCompany()
    {
        $this->validate([
            'companyId' => 'required|integer|exists:companies,id',
            'companyName' => 'required|string',
            'companyTaxNumber' => 'required|string|digits:11',
            'companyAddress' => 'required|integer|exists:addresses,id',
        ]);
        Company::where('id', $this->companyId)->update([
            'name' => $this->companyName,
            'tax_number' => $this->companyTaxNumber,
            'address_id' => $this->companyAddress,
        ]);
        return redirect()->route('company', ['action' => 'update', 'id' => $this->companyId]);
    }

    public function deleteCompany($id)
    {
        $company = Company::find($id);
        if ($company != null && $company->shops->count() == 0) {
            $company->delete();
        }
    }
}
