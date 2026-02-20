@extends('layouts.message')

@section('title', 'Tu Amigo Secreto - Secret Friend Generator')

@section('styles')
<meta http-equiv="refresh" content="5;url=/">
<style>
    .message-container { max-width: 600px; }
    .friend-name {
        font-size: 3rem;
        font-weight: bold;
        color: #667eea;
        margin: 2rem 0;
        text-transform: uppercase;
        letter-spacing: 2px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .countdown { font-size: 1.2rem; color: #666; margin-top: 2rem; }
</style>
@endsection

@section('content')
    <div class="message-container">
        <h1 style="font-size: 1.5rem; font-weight: bold; color: #333; margin-bottom: 1rem;">Felicitaciones tu amigo secreto es</h1>
        <div class="friend-name">{{ $friendName }}</div>
        <div class="countdown">Esta p&aacute;gina se cerrar&aacute; autom&aacute;ticamente en <span id="seconds">5</span> segundos</div>
    </div>
@endsection

@section('scripts')
<script>
    let seconds = 5;
    const countdownElement = document.getElementById('seconds');

    const interval = setInterval(() => {
        seconds--;
        if (seconds > 0) {
            countdownElement.textContent = seconds;
        } else {
            clearInterval(interval);
            window.location.href = '/';
        }
    }, 1000);
</script>
@endsection
