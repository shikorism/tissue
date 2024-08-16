@extends('layouts.base')

@section('title', 'チェックインの修正')

@section('content')
<div class="container">
    <h2>チェックインの修正</h2>
    <hr>
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            <form method="post" action="{{ route('checkin.update', ['id' => $ejaculation->id]) }}">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <div id="checkinForm">
                    <div class="text-center small" style="height: 640px;">しばらくお待ちください…</div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script id="initialState" type="application/json">@json($initialState)</script>
    @vite('resources/assets/js/checkin.tsx')
@endpush
