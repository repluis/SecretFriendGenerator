@props(['id', 'title' => ''])

<div class="modal-overlay" id="{{ $id }}">
    <div class="modal">
        <h3 id="{{ $id }}-title">{{ $title }}</h3>
        {{ $slot }}
    </div>
</div>
