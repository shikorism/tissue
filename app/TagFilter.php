<?php

namespace App;

use App\Utilities\Formatter;
use Illuminate\Database\Eloquent\Model;

class TagFilter extends Model
{
    /** @var int ユーザーごとの作成数制限 */
    const PER_USER_LIMIT = 100;

    /** 内容を非表示にする */
    const MODE_MASK = 1;
    /** 検索結果から除外 */
    const MODE_REMOVE = 2;

    protected $fillable = ['tag_name', 'mode'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (TagFilter $tagFilter) {
            $tagFilter->normalized_tag_name = app(Formatter::class)->normalizeTagName($tagFilter->tag_name);
        });
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
