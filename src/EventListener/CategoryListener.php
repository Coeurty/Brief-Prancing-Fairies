<?php

namespace App\EventListener;

use App\EventListener\AbstractEntityListener;
use App\Entity\ArticleCategory;

class CategoryListener extends AbstractEntityListener
{
    protected function supports($entity): bool
    {
        return $entity instanceof ArticleCategory;
    }

    protected function getSlugSource($entity): string
    {
        return $entity->getName();
    }
}