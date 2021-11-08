@extends('layouts.base')

@section('title', 'お惣菜コーナー')

@section('content')
    <div class="container pb-1">
        <h2 class="mb-3">お惣菜コーナー</h2>
        <p class="text-secondary">公開チェックインから、オカズリンク付きのものを表示しています。</p>
    </div>
    <div class="container-fluid">
        <div class="row mx-1">
            @foreach($ejaculations as $ejaculation)
                <div class="col-12 col-lg-6 col-xl-4 py-3 text-break border-top">
                    @component('components.ejaculation', compact('ejaculation'))
                    @endcomponent
                </div>
            @endforeach
        </div>
        {{ $ejaculations->links(null, ['className' => 'mt-4 justify-content-center']) }}
    </div>

    @component('components.delete-checkin-modal')
    @endcomponent
@endsection
