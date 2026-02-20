@props(['message', 'submessage' => ''])

<div class="empty-state">
    <p>{{ $message }}</p>
    @if($submessage)
        <p style="margin-top: 0.5rem; font-size: 0.85rem;">{{ $submessage }}</p>
    @endif
</div>
