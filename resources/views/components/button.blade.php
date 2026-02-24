@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
    'disabled' => false
])

@php
    $classes = 'btn';
    
    // Variant classes
    $variantClasses = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'warning' => 'btn-warning',
        'ghost' => 'btn-ghost',
        'ghost-primary' => 'btn-ghost-primary',
        'ghost-danger' => 'btn-ghost-danger',
        'ghost-success' => 'btn-ghost-success',
    ];
    
    // Size classes
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
        'icon' => 'btn-icon',
    ];
    
    $classes .= ' ' . ($variantClasses[$variant] ?? 'btn-primary');
    $classes .= ' ' . ($sizeClasses[$size] ?? '');
@endphp

<button 
    type="{{ $type }}" 
    class="{{ $classes }}" 
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes }}
>
    @if($icon)
        <span>{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>
