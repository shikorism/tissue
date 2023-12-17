<?php

namespace App;

use App\Utilities\Formatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    use HasFactory;

    const PER_COLLECTION_LIMIT = 1000;

    protected $fillable = [
        'link',
        'note',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (CollectionItem $item) {
            $item->note ??= '';
            $item->normalized_link = app(Formatter::class)->normalizeUrl($item->link);
        });
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function textTags()
    {
        return implode(' ', $this->tags->map(function ($v) {
            return $v->name;
        })->all());
    }

    /**
     * このアイテムでチェックインするためのURLを生成
     * @return string
     */
    public function makeCheckinURL(): string
    {
        return route('checkin', [
            'link' => $this->link,
            'tags' => $this->textTags(),
        ]);
    }
}
