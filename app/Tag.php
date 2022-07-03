<?php

namespace App;

use App\Utilities\Formatter;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    protected $fillable = [
        'name'
    ];
    protected $visible = [
        'name'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Tag $tag) {
            $tag->normalized_name = app(Formatter::class)->normalizeTagName($tag->name);
        });
    }

    public function ejaculations()
    {
        return $this->belongsToMany('App\Ejaculation')->withTimestamps();
    }

    public function metadata()
    {
        return $this->belongsToMany('App\Metadata')->withTimestamps();
    }

    public function collectionItems()
    {
        return $this->belongsToMany(CollectionItem::class)->withTimestamps();
    }
}
