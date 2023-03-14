<?php

namespace App\Http\Livewire\Component\Scan;

use Livewire\Component;

use App\Models\Address;
use App\Models\Company as CompanyModel;
use App\Services\DataPredictionService;

class Company extends Component
{

    const DATA_TYPE_ADDRESS = 'address';
    const DATA_TYPE_COMPANY = 'company';

    // The scanned texts.
    public $scannedName = '';
    public $scannedAddress = '';
    public $scannedTaxNumber = '';

    // The edited values.
    public $name = '';
    public $address = '';
    public $taxNumber = '';

    // The date for validation.
    public $date = '';

    public $addressSuggestions = [];
    public $companySuggestions = [];

    public $selectedCompany = '';
    public $selectedAddress = '';

    // Save new options.
    public $allowSaveAddress = false;
    public $allowSaveCompany = false;

    protected $listeners = [
        'basket.data.extracted' => 'basketDataHandler',
        'company.data.select' => 'companyDataHandler',
    ];

    public function basketDataHandler($basket, DataPredictionService $dataPrediction)
    {
        $this->scannedName = $basket['companyName'];
        $this->scannedAddress = $basket['companyAddress'];
        $this->scannedTaxNumber = $basket['taxNumber'];
        $this->date = $basket['date'];
        $this->mount();
        $this->getPredictions($dataPrediction);
    }

    public function mount()
    {
        $this->name = $this->scannedName;
        $this->address = $this->scannedAddress;
        $this->taxNumber = $this->scannedTaxNumber;
    }

    public function render(DataPredictionService $dataPrediction)
    {
        $this->getPredictions($dataPrediction);
        return view('livewire.component.scan.company');
    }

    public function companyDataHandler($data, DataPredictionService $dataPrediction)
    {
        switch ($data) {
        case self::DATA_TYPE_ADDRESS:
            $this->updateAddress($dataPrediction);
            break;
        case self::DATA_TYPE_COMPANY:
            $this->updateCompany($dataPrediction);
            break;
        }
    }

    public function insertNew($data, DataPredictionService $dataPrediction)
    {
        switch ($data) {
        case self::DATA_TYPE_ADDRESS:
            $this->addAddress($dataPrediction);
            break;
        case self::DATA_TYPE_COMPANY:
            $this->addCompany($dataPrediction);
            break;
        }
    }

    public function validateInputs()
    {
        $this->validate([
            'name' => 'required|string',
            'taxNumber' => 'required|string|exists:companies,tax_number|digits:11',
        ]);
        $this->emitUp('basket.data.update', ['companyId' => $this->companySuggestions[0]['id']]);
    }

    private function updateAddress(DataPredictionService $dataPrediction)
    {
        $this->address = $this->selectedAddress;
        $this->getPredictions($dataPrediction);
    }

    private function updateCompany(DataPredictionService $dataPrediction)
    {
        if ($this->selectedCompany != '') {
            $this->taxNumber = $this->selectedCompany;
        }
        $this->getPredictions($dataPrediction);
    }

    private function addAddress(DataPredictionService $dataPrediction)
    {
        $this->validate([
            'address' => 'required|string',
        ]);
        (new Address())->firstOrCreate([
            'raw' => $this->address,
        ]);
        $this->getPredictions($dataPrediction);
    }

    private function addCompany(DataPredictionService $dataPrediction)
    {
        $this->validate([
            'name' => 'required|string',
            'taxNumber' => 'required|string|unique:companies,tax_number|digits:11',
        ]);
        (new CompanyModel())->firstOrCreate([
            'name' => $this->name,
            'tax_number' => $this->taxNumber,
            'address_id' => $this->companySuggestions[0]['address_id'],
        ]);
        $this->getPredictions($dataPrediction);
    }

    private function getPredictions(DataPredictionService $dataPrediction)
    {
        $this->addressSuggestions = $dataPrediction->getAddressSuggestions($this->address, 10)->toArray();
        $this->companySuggestions = $dataPrediction->getCompanySuggestions($this->taxNumber, 10)->toArray();
        // setup the selected company and address if the distance is small enough
        $firstAddress = $this->addressSuggestions[0] ?? null;
        $firstCompany = $this->companySuggestions[0] ?? null;
        $this->allowSaveAddress = is_null($firstAddress);
        $this->allowSaveCompany = false;
        if ($firstAddress) {
            if ($firstAddress['distance'] > 0) {
                $this->allowSaveAddress = true;
            }
            // if the address distance is 0, then we can set the selected address
            if ($firstAddress['distance'] == 0) {
                $this->selectedAddress = $firstAddress['raw'];
            }
            if (!$this->allowSaveAddress) {
                $this->allowSaveCompany = is_null($firstCompany);
                if ($firstCompany && $firstCompany['distance'] > 0) {
                    $this->allowSaveCompany = true;
                }
            }
        }
    }
}
