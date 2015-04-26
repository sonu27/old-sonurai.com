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

    public function paginate($items, $routeName, $page, $limit, $padding = 4)
    {
        $itemCount = count($items);
        $pageCount = ceil($itemCount / $limit);

        $padding    = 4;
        $pagination = [];
        $start      = $page - $padding;
        $end        = $page + $padding;

        if ($start < 1) {
            $end -= $start - 1;
            $start = 1;
        }

        if ($end > $pageCount) {
            $end = $pageCount;
            for (null; ($start > 1) && (($end - $start) <= 3); $start--) {
                ;
            }
        }

        if ($page > 1) {
            $pagination[] = [
                'page'  => '&laquo;',
                'url'   => $this->router->generate($routeName, array('page' => $page - 1)),
                'class' => false,
            ];
        }

        for ($i = $start; $i <= $end; $i++) {
            $pagination[] = [
                'page'   => $i,
                'url'    => $this->router->generate($routeName, array('page' => $i)),
                'class' => ($i == $page) ? 'active' : false,
            ];
        }

        if ($page != $pageCount) {
            $pagination[] = [
                'page'  => '&raquo;',
                'url'   => ($page < $pageCount ? $this->router->generate($routeName, array('page' => $page + 1)) : '#'),
                'class' => false,
            ];
        }

        return $pagination;
    }

}