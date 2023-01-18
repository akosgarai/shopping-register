<div class="container">
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#newCompany" aria-controls="newCompany" wire:click="setAction('new')">{{ __('New Company') }}</button>
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">{{ __('Tax Number') }}</th>
                <th scope="col">{{ __('Address') }}</th>
                <th scope="col">{{ __('Created') }}</th>
                <th scope="col">{{ __('Updated') }}</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
            <tr>
                <th scope="row">{{ $company->id }}</th>
                <td>{{ $company->name }}</td>
                <td>{{ $company->tax_number }}</td>
                <td>{{ $company->address->raw }}</td>
                <td>{{ $company->created_at }}</td>
                <td>{{ $company->updated_at }}</td>
                <td>
                    <button class="btn btn-primary mb-1" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#updateCompany"
                        aria-controls="updateCompany" wire:click="loadCompany({{ $company->id }})">{{ __('Edit') }}
                    </button>
                    @if($company->shops->count() == 0)
                        <button class="btn btn-danger mb-1" type="button" wire:click="deleteCompany({{ $company->id }})">{{ __('Delete') }}
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'new') show @endif" data-bs-scroll="true" tabindex="-1" id="newCompany" aria-labelledby="newCompanyLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="newCompanyLabel">{{ __('New Company') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <form wire:submit.prevent="saveNewCompany">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">{{ __('Company Name') }}</label>
                        <input type="text" class="form-control" id="companyName" wire:model="companyName">
                    </div>
                    <div class="mb-3">
                        <label for="companyTaxNumber" class="form-label">{{ __('Tax Number') }}</label>
                        <input type="text" class="form-control" id="companyTaxNumber" wire:model="companyTaxNumber">
                    </div>
                    <div class="mb-3">
                        <label for="companyAddress" class="form-label">{{ __('Address') }}</label>
                        <select class="form-select" wire:model="companyAddress" id="companyAddress">
                            <option value="" @if($companyAddress == "") selected @endif>{{ __('Select Address') }}</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address['id'] }}" @if($companyAddress == $address['id']) selected @endif>{{ $address['raw'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div wire:ignore>
        <div class="offcanvas offcanvas-start @if($action == 'update') show @endif" data-bs-scroll="true" tabindex="-1" id="updateCompany" aria-labelledby="updateCompanyLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="updateCompanyLabel">{{ __('Update Company') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close" wire:click="setAction('')"></button>
            </div>
            <div class="offcanvas-body">
                <form wire:submit.prevent="updateCompany">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">{{ __('Company Name') }}</label>
                        <input type="text" class="form-control" id="companyName" wire:model="companyName">
                    </div>
                    <div class="mb-3">
                        <label for="companyTaxNumber" class="form-label">{{ __('Tax Number') }}</label>
                        <input type="text" class="form-control" id="companyTaxNumber" wire:model="companyTaxNumber">
                    </div>
                    <div class="mb-3">
                        <label for="companyAddress" class="form-label">{{ __('Address') }}</label>
                        <select class="form-select" wire:model="companyAddress" id="companyAddress">
                            @foreach ($addresses as $address)
                                <option value="{{ $address['id'] }}" @if($companyAddress == $address['id']) selected @endif>{{ $address['raw'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Created') }}</label>
                        <input type="text" class="form-control" wire:model="createdAt" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Updated') }}</label>
                        <input type="text" class="form-control" wire:model="updatedAt" readonly disabled>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
