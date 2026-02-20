@props(['variant' => 'primary', 'type' => 'button'])

@php
$class = match($variant) {
    'primary' => 'btn-save',
    'cancel' => 'btn-cancel',
    'danger' => 'btn btn-danger',
    'ghost' => 'btn btn-ghost',
    'success' => 'btn btn-success',
    default => 'btn btn-primary',
};
@endphp

<button type="{{ $type }}" class="{{ $class }}" {{ $attributes }}>{{ $slot }}</button>
