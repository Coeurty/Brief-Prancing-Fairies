<?php

namespace App\EventListener;

use App\Entity\ArticleCategory;
use App\Service\Slugger;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CategoryListener
{
    private $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ArticleCategory) {
            return;
        }

        $entity->setSlug($this->slugger->slugify($entity->getName()));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ArticleCategory) {
            return;
        }

        $entity->setSlug($this->slugger->slugify($entity->getName()));
    }
}

