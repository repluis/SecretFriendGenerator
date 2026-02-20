@props(['headers' => [], 'title' => '', 'toolbar' => null])

<div class="table-section">
    @if($title || $toolbar)
    <div class="table-toolbar">
        @if($title)
            <h3>{{ $title }}</h3>
        @endif
        @if($toolbar)
            {{ $toolbar }}
        @endif
    </div>
    @endif
    <table>
        @if(count($headers) > 0)
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{!! $header !!}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
