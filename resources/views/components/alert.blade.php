@props([
    'type' => 'status',
])

@php
    $classes = match ($type) {
        'success' => 'alert alert-success',
        'error', 'danger' => 'alert alert-error',
        'warning' => 'alert alert-warning',
        'info' => 'alert alert-info',
        default => 'alert',
    };
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    {{ $slot }}
</div>