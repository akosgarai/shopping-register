<div>
@if($model)
    <div class="text-center">
        {{ $model['name'] }}<br>
        {{ $model['company']['name'] }}<br>
        {{ $model['address']['raw'] }}<br>
    </div>
@endif
</div>
