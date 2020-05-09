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

    /**
     * 重複を排除し、正規化を行ったタグの集合を返します。
     * @return string[]
     */
    public function normalizedTags(): array
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            $tag = $this->sanitize($tag);
            $tag = $this->trim($tag);
            $tags[$tag] = true;
        }

        return array_keys($tags);
    }

    private function sanitize(string $value): string
    {
        return preg_replace('/\r?\n/u', ' ', $value);
    }

    private function trim(string $value): string
    {
        return trim($value);
    }
}
