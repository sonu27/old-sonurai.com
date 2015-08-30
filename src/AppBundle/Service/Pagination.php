<?php

namespace AppBundle\Service;

use Symfony\Component\Routing\RouterInterface;

class Pagination
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function paginate($count, $routeName, $page, $limit, $padding = 4)
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
            $pagination[] = [
                'page'  => '&laquo;',
                'url'   => $this->router->generate($routeName, ['page' => $page - 1]),
                'class' => false,
            ];
        }

        for ($i = $start; $i <= $end; $i++) {
            $pagination[] = [
                'page'  => $i,
                'url'   => $this->router->generate($routeName, ['page' => $i]),
                'class' => ($i == $page) ? 'active' : false,
            ];
        }

        if ($page != $pageCount) {
            $pagination[] = [
                'page'  => '&raquo;',
                'url'   => ($page < $pageCount ? $this->router->generate($routeName, ['page' => $page + 1]) : '#'),
                'class' => false,
            ];
        }

        return $pagination;
    }
}
