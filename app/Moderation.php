<?php

namespace App;

use App\Mail\Moderated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Moderation extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'comment',
        'send_email',
    ];

    protected $casts = [
        'action' => ModerationAction::class,
    ];

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function ejaculation()
    {
        return $this->belongsTo(Ejaculation::class, 'ejaculation_id');
    }

    public function performAction(): bool
    {
        switch ($this->action) {
            case ModerationAction::SuspendCheckin: {
                $ejaculation = $this->ejaculation;
                if ($ejaculation === null) {
                    return false;
                }

                $ejaculation->is_private = true;
                $ejaculation->saveOrFail();

                break;
            }
            case ModerationAction::SuspendUser: {
                $targetUser = $this->targetUser;
                $targetUser->is_protected = true;
                $targetUser->saveOrFail();

                break;
            }
            default:
                throw new \LogicException('unknown action');
        }

        if ($this->send_email) {
            Mail::to($this->targetUser->email)->send(new Moderated($this));
        }

        return true;
    }
}
