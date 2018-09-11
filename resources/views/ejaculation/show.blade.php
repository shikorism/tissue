@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            @component('components.profile', ['user' => $user])
            @endcomponent
        </div>
        <div class="col-lg-8">
            @if ($user->is_protected && !$user->isMe())
                <div class="card">
                    <div class="card-body">
                        <span class="oi oi-lock-locked"></span> このユーザはチェックイン履歴を公開していません。
                    </div>
                </div>
            @elseif ($ejaculation->is_private && !$user->isMe())
                <div class="card">
                    <div class="card-body">
                        <span class="oi oi-lock-locked"></span> 非公開チェックインのため、表示できません
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <!-- span -->
                        <div class="d-flex justify-content-between">
                            <h5>{{ $ejaculatedSpan ?? '精通' }} <small class="text-muted">{{ $ejaculation->before_date }}{{ !empty($ejaculation->before_date) ? ' ～ ' : '' }}{{ $ejaculation->ejaculated_date->format('Y/m/d H:i') }}</small></h5>
                            <div>
                                <a class="text-secondary timeline-action-item" href="{{ route('checkin', ['link' => $ejaculation->link, 'tags' => $ejaculation->textTags()]) }}"><span class="oi oi-reload" data-toggle="tooltip" data-placement="bottom" title="同じオカズでチェックイン"></span></a>
                                @if ($user->isMe())
                                    <a class="text-secondary timeline-action-item" href="{{ route('checkin.edit', ['id' => $ejaculation->id]) }}"><span class="oi oi-pencil" data-toggle="tooltip" data-placement="bottom" title="修正"></span></a>
                                    <a class="text-secondary timeline-action-item" href="#" data-toggle="modal" data-target="#deleteCheckinModal" data-id="{{ $ejaculation->id }}" data-date="{{ $ejaculation->ejaculated_date }}"><span class="oi oi-trash" data-toggle="tooltip" data-placement="bottom" title="削除"></span></a>
                                @endif
                            </div>
                        </div>
                        <!-- tags -->
                        @if ($ejaculation->is_private || $ejaculation->tags->isNotEmpty())
                        <p class="mb-2">
                            @if ($ejaculation->is_private)
                                <span class="badge badge-warning"><span class="oi oi-lock-locked"></span> 非公開</span>
                            @endif
                            @foreach ($ejaculation->tags as $tag)
                                <a class="badge badge-secondary" href="{{ route('search', ['q' => $tag->name]) }}"><span class="oi oi-tag"></span> {{ $tag->name }}</a>
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
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@component('components.modal', ['id' => 'deleteCheckinModal'])
    @slot('title')
        削除確認
    @endslot
    <span class="date-label"></span> のチェックインを削除してもよろしいですか？
    <form action="{{ route('checkin.destroy', ['id' => '@']) }}" method="post">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
    @slot('footer')
        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
        <button type="button" class="btn btn-danger">削除</button>
    @endslot
@endcomponent
@endsection

@push('script')
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/holderjs@2.9.4/holder.min.js"></script>
    <script>
        $('#deleteCheckinModal').on('show.bs.modal', function (event) {
            var target = $(event.relatedTarget);
            var modal = $(this);
            modal.find('.modal-body .date-label').text(target.data('date'));
            modal.data('id', target.data('id'));
        }).find('.btn-danger').on('click', function (event) {
            var modal = $('#deleteCheckinModal');
            var form = modal.find('form');
            form.attr('action', form.attr('action').replace('@', modal.data('id')));
            form.submit();
        });

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