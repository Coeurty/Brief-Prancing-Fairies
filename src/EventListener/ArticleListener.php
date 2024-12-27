<?php

namespace App\EventListener;

use App\Entity\Article;
use App\EventListener\AbstractEntityListener;

class ArticleListener extends AbstractEntityListener
{
    protected function supports($entity): bool
    {
        return $entity instanceof Article;
    }

    protected function getSlugSource($entity): string
    {
        return $entity->getTitle();
    }
}