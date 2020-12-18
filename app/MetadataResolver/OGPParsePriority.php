<?php

namespace App\MetadataResolver;

class OGPParsePriority
{
    const OGP = 0;
    const TWITTER_CARDS = 1;

    /** @var int */
    private $title = self::OGP;
    /** @var int */
    private $description = self::OGP;
    /** @var int */
    private $image = self::OGP;

    public static function preferTo(int $priority): OGPParsePriority
    {
        return new static($priority, $priority, $priority);
    }

    public function __construct(int $title = self::OGP, int $description = self::OGP, int $image = self::OGP)
    {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
    }

    public function sortForTitle(string ...$expressions): array
    {
        return $this->sort($this->title, ...$expressions);
    }

    public function sortForDescription(string ...$expressions): array
    {
        return $this->sort($this->description, ...$expressions);
    }

    public function sortForImage(string ...$expressions): array
    {
        return $this->sort($this->image, ...$expressions);
    }

    private function sort(int $priority, string ...$expressions): array
    {
        switch ($priority) {
            case self::OGP:
                $needle = 'og:';
                break;
            case self::TWITTER_CARDS:
                $needle = 'twitter:';
                break;
            default:
                throw new \InvalidArgumentException('$priority has an invalid value.');
        }

        $preferred = [];
        $rest = [];
        foreach ($expressions as $expr) {
            if (stripos($expr, $needle) !== false) {
                $preferred[] = $expr;
            } else {
                $rest[] = $expr;
            }
        }

        return array_merge($preferred, $rest);
    }
}
