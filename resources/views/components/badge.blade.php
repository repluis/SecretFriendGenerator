@props(['color' => 'slate'])

@php
    $colorClasses = [
        'primary' => 'badge-primary',
        'success' => 'badge-success',
        'danger' => 'badge-danger',
        'warning' => 'badge-warning',
        'info' => 'badge-info',
        'slate' => 'badge-slate',
        // Aliases antiguos
        'green' => 'badge-success',
        'red' => 'badge-danger',
        'amber' => 'badge-warning',
        'blue' => 'badge-info',
    ];
    
    $class = $colorClasses[$color] ?? 'badge-slate';
@endphp

<span class="badge {{ $class }}" {{ $attributes }}>{{ $slot }}</span>
