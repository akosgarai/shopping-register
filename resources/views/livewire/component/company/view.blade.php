<div>
@if($model)
    <div class="text-center">
        {{ $model['name'] }}<br>
        {{ $model['tax_number'] }}<br>
        {{ $model['address']['raw'] }}<br>
    </div>
@endif
</div>
