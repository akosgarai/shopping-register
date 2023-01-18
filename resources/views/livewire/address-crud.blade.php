<div class="container">
    <table class="table table-striped table-hover">
        <thead>
            <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($addresses as $address)
            <tr>
                <th scope="row">{{ $address->id }}</th>
                <td>{{ $address->raw }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
