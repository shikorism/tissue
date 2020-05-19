@extends('setting.base')

@section('title', 'データのエクスポート')

@section('tab-content')
    <h3>データのエクスポート</h3>
    <hr>
    <p>チェックインデータをCSVファイルとしてダウンロードすることができます。</p>
    <form class="mt-4" action="{{ route('setting.export.csv') }}" method="get">
        {{ csrf_field() }}
        <div class="mb-2"><strong>文字コード</strong></div>
        <div class="form-check">
            <label><input class="form-check-input" type="radio" name="charset" value="utf8" checked>UTF-8</label>
        </div>
        <div class="form-check">
            <label><input class="form-check-input" type="radio" name="charset" value="sjis">Shift_JIS</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">ダウンロード</button>
    </form>
@endsection

@push('script')
@endpush
