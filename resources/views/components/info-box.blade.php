<table class="table infobox">
    <caption>{{ $title }}</caption>

    <tbody>
    @foreach($rows as $head => $value)
        <tr>
            <th scope="row">{{ $head }}</th>
            <td>{{ $value }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
