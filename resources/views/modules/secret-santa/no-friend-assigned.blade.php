@extends('layouts.message')

@section('title', 'Sin Amigo Asignado - ' . $appName)

@section('gradient-from', '#4facfe')
@section('gradient-to', '#00f2fe')

@section('content')
    <div class="message-container">
        <div class="icon">&#10067;</div>
        <div class="message">No tienes un amigo asignado</div>
        <div class="submessage">Por favor, contacta al administrador.</div>
    </div>
@endsection
