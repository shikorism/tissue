<div class="container-fluid border-bottom mb-4 mt-n1 mt-lg-n4 px-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4">
                @component('components.profile-mini', ['user' => $user])
                @endcomponent
            </div>
            <div class="col-lg-8 mt-3 mt-lg-2 px-0 px-md-2">
                <ul class="nav tis-nav-underline-tabs flex-nowrap overflow-auto">
                    <li class="nav-item flex-shrink-0">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.profile' ? 'active' : '' }}" href="{{ route('user.profile', ['name' => $user->name]) }}">タイムライン</a>
                    </li>
                    <li class="nav-item flex-shrink-0">
                        <a class="nav-link {{ stripos(Route::currentRouteName(), 'user.stats') === 0 ? 'active' : '' }}" href="{{ route('user.stats', ['name' => $user->name]) }}">グラフ</a>
                    </li>
                    <li class="nav-item flex-shrink-0">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.okazu' ? 'active' : '' }}" href="{{ route('user.okazu', ['name' => $user->name]) }}">オカズ</a>
                    </li>
                    <li class="nav-item flex-shrink-0">
                        <a class="nav-link {{ Route::currentRouteName() === 'user.likes' ? 'active' : '' }}" href="{{ route('user.likes', ['name' => $user->name]) }}">いいね
                            @if ($user->isMe() || !($user->is_protected || $user->private_likes))
                                <span class="badge {{ Route::currentRouteName() === 'user.likes' ? 'badge-primary' : 'badge-secondary' }}">{{ $user->likes()->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item flex-shrink-0">
                        <a class="nav-link {{ stripos(Route::currentRouteName(), 'user.collections') === 0 ? 'active' : '' }}" href="{{ route('user.collections', ['name' => $user->name]) }}">コレクション</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
