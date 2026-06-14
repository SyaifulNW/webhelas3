@if($data->isEmpty())
    <tr>
        <td colspan="20" class="text-center text-muted py-4">
            Tidak ada data yang sesuai dengan filter yang dipilih.
        </td>
    </tr>
@else
    @foreach($data as $item)
        @include('admin.database.partials.row', ['item' => $item, 'loop' => $loop, 'kelas' => $kelas])
    @endforeach
@endif