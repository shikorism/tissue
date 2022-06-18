{{-- 修正の際は resources/assets/js/components/LinkCard.tsx も修正してください! --}}
<div class="card link-card mb-2 px-0 col-12 d-none" style="font-size: small;">
    <a class="text-dark card-link {{ $is_too_sensitive ? 'card-spoiler' : '' }}" href="{{ $link }}" target="_blank" rel="noopener">
        <div class="row no-gutters">
            <div class="col-12 col-md-6 justify-content-center align-items-center">
                @if ($is_too_sensitive)
                    <div class="card-spoiler-img-overlay">
                        <span class="warning-text">クリックまたはタップで表示</span>
                    </div>
                @endif
                <img src="" alt="Thumbnail" class="w-100 bg-secondary">
            </div>
            <div class="col-12 col-md-6">
                <div class="card-body">
                    <h6 class="card-title font-weight-bold">タイトル</h6>
                    <p class="card-text">コンテンツの説明文</p>
                </div>
            </div>
        </div>
    </a>
</div>
