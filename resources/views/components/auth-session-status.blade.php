@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-white bg-emerald-500 py-3 px-4 rounded text-sm']) }}>
        {{ $status }}
    </div>
@endif
