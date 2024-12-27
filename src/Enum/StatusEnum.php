<?php
namespace App\Enum;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function GetStatusAsString(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PUBLISHED => 'Publié',
            self::ARCHIVED => 'Archivé',
        };
    }
}
