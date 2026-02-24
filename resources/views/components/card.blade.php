@props(['class' => ''])

<div class="card {{ $class }}" {{ $attributes }}>
    {{ $slot }}
</div>
