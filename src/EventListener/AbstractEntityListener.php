<?php

namespace App\EventListener;

use App\Service\Slugger;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;

abstract class AbstractEntityListener
{
    protected $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    abstract protected function supports($entity): bool;

    abstract protected function getSlugSource($entity): string;

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$this->supports($entity)) {
            return;
        }

        $this->setSlug($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$this->supports($entity)) {
            return;
        }

        $this->setSlug($entity);
    }

    protected function setSlug($entity): void
    {
        $entity->setSlug($this->slugger->slugify($this->getSlugSource($entity)));
    }
}