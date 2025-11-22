<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;

class LikedOkazuCsvExporter
{
    /** @var User Target user */
    private $user;
    /** @var string Output filename */
    private $filename;
    /** @var string Output charset */
    private $charset;

    public function __construct(User $user, string $filename, string $charset)
    {
        $this->user = $user;
        $this->filename = $filename;
        $this->charset = $charset;
    }

    public function execute()
    {
        $csv = Writer::createFromPath($this->filename, 'wb');
        $csv->setEndOfLine("\r\n");
        if ($this->charset === 'SJIS-win') {
            $csv->appendStreamFilterOnWrite('convert.mbstring.encoding.UTF-8:SJIS-win');
        }

        $header = ['いいねした日時', 'チェックインURL', 'チェックインユーザーID', 'オカズリンク'];
        $csv->insertOne($header);

        DB::transaction(function () use ($csv) {
            $query = $this->user->likes()
                ->select('likes.*')
                ->orderBy('likes.id')
                ->with([
                    'ejaculation' => function ($query) {
                        $query->with('user', 'tags');
                    }
                ])
                ->join('ejaculations', 'likes.ejaculation_id', '=', 'ejaculations.id')
                ->join('users', 'ejaculations.user_id', '=', 'users.id')
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('ejaculations.user_id', $this->user->id)
                            ->orWhere(function ($query) {
                                $query->where('ejaculations.is_private', false)->where('users.is_protected', false);
                            });
                    });
                });
            $query->chunk(1000, function ($likes) use ($csv) {
                foreach ($likes as $like) {
                    $record = [
                        $like->created_at->format('Y/m/d H:i:s'),
                        route('checkin.show', ['id' => $like->ejaculation->id]),
                        $like->ejaculation->user->name,
                        $like->ejaculation->link,
                    ];
                    $csv->insertOne($record);
                }
            });
        });
    }
}
