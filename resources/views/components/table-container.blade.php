@props(['title' => null, 'actions' => null])

<div class="table-container">
    @if($title || $actions)
        <div class="table-header">
            @if($title)
                <h3 class="table-title">{{ $title }}</h3>
            @endif
            @if($actions)
                <div class="table-actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="table-wrapper">
        {{ $slot }}
    </div>
</div>
