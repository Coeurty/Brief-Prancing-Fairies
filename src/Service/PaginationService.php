<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    private ArticleRepository $articleRepository;
    private PaginatorInterface $paginator;

    public function __construct(ArticleRepository $articleRepository, PaginatorInterface $paginator)
    {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
    }

    public function paginate(Request $request, int $limit = 6)
    {
        $query = $this->articleRepository->findAllPaginated();

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );
    }
}