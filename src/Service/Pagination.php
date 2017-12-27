<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;

class Pagination
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function paginate($count, $routeName, $page, $limit, $params = [], $padding = 4): array
    {
        $pagination = [];

        if ($count < 1) {
            return $pagination;
        }

        $pageCount = (int)ceil($count / $limit);

        if ($page > $pageCount) {
            throw new \LogicException('Page cannot be more than page count');
        }

        $pageMinusPadding = $page - $padding;
        $pagePlusPadding  = $page + $padding;
        $start            = ($pageMinusPadding < 1) ? 1 : $pageMinusPadding;
        $end              = ($pagePlusPadding > $pageCount) ? $pageCount : $pagePlusPadding;

        if ($page > 1) {
            $routeParams  = array_merge(['page' => $page - 1], $params);
            $pagination[] = [
                'page'  => '&laquo;',
                'url'   => $this->router->generate($routeName, $routeParams),
                'class' => false,
            ];
        }

        for ($i = $start; $i <= $end; $i++) {
            $routeParams  = array_merge(['page' => $i], $params);
            $pagination[] = [
                'page'  => $i,
                'url'   => $this->router->generate($routeName, $routeParams),
                'class' => $i === $page ? 'active' : false,
            ];
        }

        if ($page !== $pageCount) {
            $routeParams  = array_merge(['page' => $page + 1], $params);
            $pagination[] = [
                'page'  => '&raquo;',
                'url'   => $page < $pageCount ? $this->router->generate($routeName, $routeParams) : '#',
                'class' => false,
            ];
        }

        return $pagination;
    }
}
