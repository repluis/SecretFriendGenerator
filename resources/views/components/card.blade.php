@props(['class' => ''])

<div class="summary-card {{ $class }}" {{ $attributes }}>
    {{ $slot }}
</div>
