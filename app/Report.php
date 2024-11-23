<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'target_user_id',
        'ejaculation_id',
        'violated_rule_id',
        'comment',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function ejaculation()
    {
        return $this->belongsTo(Ejaculation::class, 'ejaculation_id');
    }

    public function violatedRule()
    {
        return $this->belongsTo(Rule::class, 'violated_rule_id');
    }

    public function moderations()
    {
        return $this->hasMany(Moderation::class, 'report_id');
    }
}
