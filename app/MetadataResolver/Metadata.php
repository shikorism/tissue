<?php

namespace App\MetadataResolver;

use Carbon\Carbon;

class Metadata
{
    /** @var string タイトル */
    public $title = '';

    /** @var string 概要 */
    public $description = '';

    /** @var string サムネイルのURL */
    public $image = '';

    /** @var Carbon|null メタデータの有効期限 */
    public $expires_at = null;

    /**
     * @var string[] タグ
     * チェックインタグと同様に保存されるため、スペースや改行文字を含めてはいけません。
     */
    public $tags = [];
}
