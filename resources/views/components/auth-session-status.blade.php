@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'fw-bold text-success']) }}>
        {{ $status }}
    </div>
@endif
