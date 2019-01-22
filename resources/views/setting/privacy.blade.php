@extends('setting.base')

@section('title', 'プライバシー設定')

@section('tab-content')
    <h3>プライバシー</h3>
    <hr>
    <form action="{{ route('setting.privacy.update') }}" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <div class="custom-control custom-checkbox mb-2">
                <input id="protected" name="is_protected" class="custom-control-input" type="checkbox" {{ (old('is_protected') ?? Auth::user()->is_protected ) ? 'checked' : '' }}>
                <label class="custom-control-label" for="protected">全てのチェックイン履歴を非公開にする</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input id="accept-analytics" name="accept_analytics" class="custom-control-input" type="checkbox" {{ (old('accept_analytics') ?? Auth::user()->accept_analytics ) ? 'checked' : '' }}>
                <label class="custom-control-label" for="accept-analytics">匿名での統計にチェックインデータを利用することに同意します</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">更新</button>
    </form>
@endsection

@push('script')
    <script>
        $('#protected').on('change', function () {
            if (!$(this).prop('checked')) {
                alert('チェックイン履歴を公開に切り替えると、個別に非公開設定されているものを除いた全てのチェックインが誰でも閲覧できるようになります。\nご注意ください。');
            }
        });
    </script>
@endpush
