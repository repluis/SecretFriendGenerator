@props(['id', 'label' => '', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => ''])

<div class="form-group" id="{{ $id }}-group">
    @if($label)
        <label for="{{ $id }}">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        class="form-input"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
</div>
