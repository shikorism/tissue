@extends('layouts.base')

@section('content')
    <div class="container text-center">
        <img src="{{ asset('maintenance.svg') }}" width="200" height="200" alt="Under maintenance">
        <h2>ただいまメンテナンス中です</h2>
        <hr>
        <p class="mb-1">メンテナンス中はTissueをご利用いただくことができません。終了まで今しばらくお待ちください。</p>
        <p>ご不便をおかけしておりますが、ご理解いただきますようお願いいたします。</p>
    </div>
@endsection
