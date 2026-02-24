@props(['name', 'size' => 'md', 'color' => null])

@php
    $sizes = [
        'sm' => 'width: 32px; height: 32px; font-size: 0.75rem;',
        'md' => 'width: 40px; height: 40px; font-size: 0.875rem;',
        'lg' => 'width: 48px; height: 48px; font-size: 1rem;',
        'xl' => 'width: 64px; height: 64px; font-size: 1.25rem;',
    ];
    
    $colors = [
        '#6366f1', '#8b5cf6', '#a855f7', '#ec4899', '#f43f5e',
        '#f97316', '#eab308', '#22c55e', '#14b8a6', '#06b6d4'
    ];
    
    $initials = strtoupper(substr($name, 0, 2));
    $colorIndex = array_sum(array_map('ord', str_split($name))) % count($colors);
    $bgColor = $color ?? $colors[$colorIndex];
    $sizeStyle = $sizes[$size] ?? $sizes['md'];
@endphp

<div 
    class="table-avatar" 
    style="{{ $sizeStyle }} background: {{ $bgColor }}; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;"
    {{ $attributes }}
>
    {{ $initials }}
</div>
