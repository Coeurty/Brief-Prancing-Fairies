<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreadcrumbService
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generateBreadcrumb(array $routes): array
    {
        $breadcrumbs = [];

        foreach ($routes as $route) {
            $breadcrumbs[] = [
                'label' => $route['label'],
                'url' => $route['name'] ? $this->urlGenerator->generate($route['name'], $route['params'] ?? []) : null,
            ];
        }

        return $breadcrumbs;
    }
}
