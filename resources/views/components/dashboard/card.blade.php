<div class="card">
    @if($header)
        <div class="card-header">{{ $header }}</div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
