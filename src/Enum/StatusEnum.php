<?php
namespace App\Enum;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
