<?php

namespace App\Enum;

enum NameType: string
{
    case ONLY_TITLE = 'only-title';

    case TITLE_AND_LASTNAME = 'title-and-lastname';

    case FULL_NAME = 'full-name';

    public static function byWordCount(string $words): self|string
    {
        return match (str_word_count($words)) {
            1 => self::ONLY_TITLE,
            2 => self::TITLE_AND_LASTNAME,
            3 => self::FULL_NAME,
            default => 'Unsupported Type',
        };
    }
}
