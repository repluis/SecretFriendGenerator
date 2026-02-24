@props(['icon', 'value', 'label', 'color' => 'primary', 'footer' => null])

<div class="stat-card">
    <div class="stat-card-header">
        <div class="stat-card-icon {{ $color }}">
            {{ $icon }}
        </div>
    </div>
    <div class="stat-card-value">{{ $value }}</div>
    <div class="stat-card-label">{{ $label }}</div>
    @if($footer)
        <div class="stat-card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
