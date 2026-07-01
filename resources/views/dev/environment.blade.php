<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Environment</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-info-circle me-2 text-warning"></i>Environment</h4>
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tbody>
                            @foreach ($info as $key => $value)
                                <tr>
                                    <th class="ps-3" style="width:200px;white-space:nowrap;">{{ str_replace('_', ' ', ucwords($key, '_')) }}</th>
                                    <td><code>{{ $value }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
