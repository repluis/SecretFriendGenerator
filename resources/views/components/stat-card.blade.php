@props(['label', 'value', 'color' => 'slate', 'detail' => ''])

<div class="stat-card">
    <div class="stat-value {{ $color }}">{{ $value }}</div>
    <div class="stat-label">{{ $label }}</div>
</div>
