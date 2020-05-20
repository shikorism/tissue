@extends('setting.base')

@section('title', 'データのエクスポート')

@section('tab-content')
    <h3>データのエクスポート</h3>
    <hr>
    <p>チェックインデータをCSVファイルとしてダウンロードすることができます。</p>
    <form class="mt-4" action="{{ route('setting.export.csv') }}" method="get">
        {{ csrf_field() }}
        <div class="mb-2"><strong>文字コード</strong></div>
        <div class="form-group">
            <div class="custom-control custom-radio custom-control-inline">
                <input id="charsetUTF8" class="custom-control-input" type="radio" name="charset" value="utf8" checked>
                <label for="charsetUTF8" class="custom-control-label">UTF-8</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline ml-3">
                <input id="charsetSJIS" class="custom-control-input" type="radio" name="charset" value="sjis">
                <label for="charsetSJIS" class="custom-control-label">Shift_JIS</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">ダウンロード</button>
    </form>
@endsection

@push('script')
@endpush
