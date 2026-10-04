@props(['role'])

@php
    $label = ucfirst((string) $role);

    $class = match ((string) $role) {
        'seller' => 'badge badge-seller',
        'buyer' => 'badge badge-buyer',
        'admin' => 'badge badge-admin',
        default => 'badge',
    };
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    {{ $label }}
</span>