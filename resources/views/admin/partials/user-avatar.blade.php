@props([
    'user',
    'size' => 'md',
    'class' => '',
])

@php
    $sizes = [
        'sm' => 'kecamatan-user-avatar--sm',
        'md' => 'kecamatan-user-avatar--md',
        'lg' => 'kecamatan-user-avatar--lg',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatarUrl())
    <img
        src="{{ $user->avatarUrl() }}"
        alt="{{ $user->name }}"
        {{ $attributes->class(['kecamatan-user-avatar', 'kecamatan-user-avatar--photo', $sizeClass, $class]) }}
    >
@else
    <span {{ $attributes->class(['kecamatan-user-avatar', 'kecamatan-user-avatar--fallback', $sizeClass, $class]) }}>
        {{ $user->avatarInitial() }}
    </span>
@endif
