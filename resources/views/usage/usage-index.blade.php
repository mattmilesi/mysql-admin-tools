@include('common.topbar')

<!-- Table primary keys usage -->
<table class="table table-sm">
    <thead>
    <tr>
        <th>Table name</th>
        <th>Type</th>
        <th>Usage %</th>
    </tr>
    </thead>
    <tbody>
    @foreach($usages as $name => $data)
        <tr>
            <td>{{ $name }}</td>
            <td>{{ $data['pk_type'] }}</td>
            <td>{{ $data['usage'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@include('common.common')
