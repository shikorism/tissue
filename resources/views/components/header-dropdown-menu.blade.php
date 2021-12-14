<a href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" class="dropdown-item">
    <strong>{{ Auth::user()->display_name }}</strong>
    <p class="mb-0 text-muted">
        <span>&commat;{{ Auth::user()->name }}</span>
    </p>
</a>
<div class="dropdown-divider"></div>
<a href="{{ route('user.profile', ['name' => Auth::user()->name]) }}" class="dropdown-item">プロフィール</a>
<a href="{{ route('user.stats', ['name' => Auth::user()->name]) }}" class="dropdown-item">グラフ</a>
<a href="{{ route('user.okazu', ['name' => Auth::user()->name]) }}" class="dropdown-item">オカズ</a>
<a href="{{ route('user.likes', ['name' => Auth::user()->name]) }}" class="dropdown-item">いいね</a>
<a href="{{ route('user.collections', ['name' => Auth::user()->name]) }}" class="dropdown-item">コレクション</a>
<div class="dropdown-divider"></div>
<a href="{{ route('setting') }}" class="dropdown-item">設定</a>
@can ('admin')
    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">管理</a>
@endcan
<a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
