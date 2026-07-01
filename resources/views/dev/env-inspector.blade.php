<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Environment Inspector</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-sliders me-2 text-warning"></i>Environment Inspector</h4>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header fw-semibold">Runtime Info</div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                @foreach ($info as $key => $val)
                                    <tr>
                                        <th class="ps-3 text-muted" style="width:160px;">{{ str_replace('_',' ',ucwords($key,'_')) }}</th>
                                        <td><code>{{ $val }}</code></td>
                                    </tr>
                                @endforeach
                                @foreach ($extra as $key => $val)
                                    <tr>
                                        <th class="ps-3 text-muted">{{ str_replace('_',' ',ucwords($key,'_')) }}</th>
                                        <td><code>{{ $val }}</code></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    @php $statusMap = ['green'=>['bg'=>'success','icon'=>'bi-check-circle-fill'],'yellow'=>['bg'=>'warning','icon'=>'bi-exclamation-circle-fill'],'red'=>['bg'=>'danger','icon'=>'bi-x-circle-fill']]; @endphp
                    <div class="card">
                        <div class="card-header fw-semibold">System Health</div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-2">
                                @foreach ($health as $key => $check)
                                    @php $style = $statusMap[$check['status']] ?? $statusMap['yellow']; @endphp
                                    <div class="d-flex align-items-center gap-2 border rounded px-3 py-2">
                                        <i class="bi {{ $style['icon'] }} text-{{ $style['bg'] }}"></i>
                                        <div>
                                            <span class="fw-semibold small">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            <span class="text-muted small ms-2">{{ $check['message'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
