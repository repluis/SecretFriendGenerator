@extends('layouts.message')

@section('title', 'Juego No Iniciado - ' . $appName)

@section('gradient-from', '#f093fb')
@section('gradient-to', '#f5576c')

@section('content')
    <div class="message-container">
        <div class="icon">&#9203;</div>
        <div class="message">El juego a&uacute;n no ha empezado</div>
        <div class="submessage">Por favor, espera a que el administrador inicie el juego.</div>
    </div>
@endsection
