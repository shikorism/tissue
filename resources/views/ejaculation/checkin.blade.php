@extends('layouts.base')

@section('title', 'チェックイン')

@section('content')
<div class="container">
    <h2>今致してる？</h2>
    <hr>
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('checkin') }}">
                {{ csrf_field() }}
                <div id="checkinForm">
                    <div class="text-center small" style="height: 640px;">しばらくお待ちください…</div>
                </div>
            </form>
            <p class="text-center small mt-4"><strong>Tips</strong>: ブックマークレットや共有機能で、簡単にチェックインできます！ <a href="{{ route('checkin.tools') }}" target="_blank" rel="noopener">使い方はこちら</a></p>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script id="initialState" type="application/json">@json($initialState)</script>
    @vite('resources/assets/js/checkin.tsx')
@endpush
