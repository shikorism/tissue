@extends('layouts.base')

@push('head')
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            @component('components.profile', ['user' => Auth::user()])
            @endcomponent
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">サイトからのお知らせ</div>
                <div class="list-group list-group-flush">
                    @foreach($informations as $info)
                        <a class="list-group-item" href="{{ route('info.show', ['id' => $info->id]) }}">
                            <span class="badge {{ $categories[$info->category]['class'] }}">{{ $categories[$info->category]['label'] }}</span> {{ $info->title }} <small class="text-secondary">- {{ $info->created_at->format('n月j日') }}</small>
                        </a>
                    @endforeach
                    <a href="{{ route('info') }}" class="list-group-item text-right">お知らせ一覧 &raquo;</a>
                </div>
            </div>
            @if (!empty($publicLinkedEjaculations))
                <div class="card mb-4">
                    <div class="card-header">お惣菜コーナー</div>
                    <div class="card-body">
                        <p class="card-text">最近の公開チェックインから、オカズリンク付きのものを表示しています。</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($publicLinkedEjaculations as $ejaculation)
                            <li class="list-group-item pt-3 pb-3">
                                <!-- span -->
                                <div class="d-flex justify-content-between">
                                    <h5>
                                        <a href="{{ route('user.profile', ['id' => $ejaculation->user->name]) }}" class="text-dark"><img src="{{ $ejaculation->user->getProfileImageUrl(30) }}" width="30" height="30" class="rounded d-inline-block align-bottom"> &commat;{{ $ejaculation->user->name }}</a>
                                        <a href="{{ route('checkin.show', ['id' => $ejaculation->id]) }}" class="text-muted"><small>{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></a>
                                    </h5>
                                    <div>
                                        <a class="text-secondary timeline-action-item" href="{{ route('checkin', ['link' => $ejaculation->link, 'tags' => $ejaculation->textTags()]) }}"><span class="oi oi-reload" data-toggle="tooltip" data-placement="bottom" title="同じオカズでチェックイン"></span></a>
                                    </div>
                                </div>
                                <!-- tags -->
                                @if ($ejaculation->tags->isNotEmpty())
                                    <p class="mb-2">
                                        @foreach ($ejaculation->tags as $tag)
                                            <span class="badge badge-secondary"><span class="oi oi-tag"></span> {{ $tag->name }}</span>
                                        @endforeach
                                    </p>
                                @endif
                                <!-- okazu link -->
                                @if (!empty($ejaculation->link))
                                    <div class="row mx-0">
                                        <div class="card link-card mb-2 px-0 col-12 col-md-6 d-none" style="font-size: small;">
                                            <a class="text-dark card-link" href="{{ $ejaculation->link }}" target="_blank" rel="noopener">
                                                <img src="" alt="Thumbnail" class="card-img-top bg-secondary">
                                                <div class="card-body">
                                                    <h6 class="card-title font-weight-bold">タイトル</h6>
                                                    <p class="card-text">コンテンツの説明文</p>
                                                </div>
                                            </a>
                                        </div>
                                        <p class="mb-2 col-12 px-0">
                                            <span class="oi oi-link-intact mr-1"></span><a href="{{ $ejaculation->link }}" target="_blank" rel="noopener">{{ $ejaculation->link }}</a>
                                        </p>
                                    </div>
                                @endif
                                <!-- note -->
                                @if (!empty($ejaculation->note))
                                    <p class="mb-0 tis-word-wrap">
                                        {!! Formatter::linkify(nl2br(e($ejaculation->note))) !!}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        $('.link-card').each(function () {
            var $this = $(this);
            $.ajax({
                url: '{{ url('/api/checkin/card') }}',
                method: 'get',
                type: 'json',
                data: {
                    url: $this.find('a').attr('href')
                }
            }).then(function (data) {
                var $title = $this.find('.card-title');
                var $desc = $this.find('.card-text');
                var $image = $this.find('img');

                if (data.title === '') {
                    $title.hide();
                } else {
                    $title.text(data.title);
                }

                if (data.description === '') {
                    $desc.hide();
                } else {
                    $desc.text(data.description);
                }

                if (data.image === '') {
                    $image.hide();
                } else {
                    $image.attr('src', data.image);
                }

                if (data.title !== '' || data.description !== '' || data.image !== '') {
                    $this.removeClass('d-none');
                }
            });
        });
    </script>
@endpush