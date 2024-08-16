@extends('setting.base')

@section('title', 'データのインポート')

@section('tab-content')
    <h3>データのインポート</h3>
    <hr>
    <p>外部で作成したチェックインデータをTissueに取り込むことができます。</p>
    <form class="mt-4" action="{{ route('setting.import') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <strong>取り込むファイルを選択してください。</strong>
            <small class="form-text text-muted">{{ Formatter::normalizeIniBytes(ini_get('upload_max_filesize')) }}までのCSVファイル、文字コードは Shift_JIS と UTF-8 (BOMなし) に対応しています。</small>
            <input name="file" type="file" class="form-control-file {{ $errors->has('file') ? ' is-invalid' : '' }} mt-2">
            @if ($errors->has('file'))
                <div class="invalid-feedback">{{ $errors->first('file') }}</div>
            @endif
        </div>
        @if (session('import_errors'))
            <div class="alert alert-danger">
                <p class="alert-heading"><i class="ti ti-alert-triangle"></i> <strong>インポートに失敗しました</strong></p>
                @foreach (session('import_errors') as $err)
                    <p class="mb-0">{{ $err }}</p>
                @endforeach
            </div>
        @endif
        <button type="submit" class="btn btn-primary mt-2">アップロード</button>
    </form>
    <h3 class="mt-5">インポートしたデータを一括削除</h3>
    <hr>
    <p class="mb-0">取り込んだチェックインデータをすべて削除することができます。データにミスがあってやり直したい場合などにお使いください。</p>
    <p class="text-danger">ただし、インポート後に個別に手修正などしている場合、そのデータも失われてしまうことに注意してください！</p>
    <form id="destroy-form" class="mt-4" action="{{ route('setting.import.destroy') }}" method="post">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button type="submit" class="btn btn-danger mt-2">データを削除</button>
    </form>
@endsection

@push('script')
    @vite('resources/assets/js/setting/import.ts')
@endpush
