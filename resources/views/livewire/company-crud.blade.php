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
                        aria-controls="updateCompany" wire:click="load({{ $company->id }})">{{ __('Edit') }}
                    </button>
                    @if($company->shops->count() == 0)
                        <button class="btn btn-danger mb-1" type="button" wire:click="delete({{ $company->id }})">{{ __('Delete') }}
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
                <form wire:submit.prevent="saveNew">
                    @include('livewire.component.textinput', ['modelId' => 'companyName', 'formLabel' => __('Company Name')])
                    @include('livewire.component.textinput', ['modelId' => 'companyTaxNumber', 'formLabel' => __('Tax Number')])
                    @include('livewire.component.selectoraddress', ['modelId' => 'companyAddress', 'addresses' => $addresses, 'selected' => $companyAddress])
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
                <form wire:submit.prevent="update">
                    @include('livewire.component.textinput', ['modelId' => 'companyName', 'formLabel' => __('Company Name')])
                    @include('livewire.component.textinput', ['modelId' => 'companyTaxNumber', 'formLabel' => __('Tax Number')])
                    @include('livewire.component.selectoraddress', ['modelId' => 'companyAddress', 'addresses' => $addresses, 'selected' => $companyAddress])
                    @include('livewire.component.textinput', ['modelId' => 'createdAt', 'formLabel' => __('Created'), 'readonly' => true])
                    @include('livewire.component.textinput', ['modelId' => 'updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
    @include('livewire.component.offcanvasscipts')
</div>
