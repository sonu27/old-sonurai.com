<?php

namespace AppBundle\Tests\Service;

use AppBundle\Service\Pagination;
use Prophecy\Argument;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    protected $obj;
    protected $routeName = 'fake_route';

    protected function setUp()
    {
        $routerProphecy = $this->prophesize();
        $routerProphecy->willExtend('stdClass');
        $routerProphecy->willImplement('Symfony\Component\Routing\RouterInterface');
        $routerProphecy->generate(Argument::any(), Argument::any())->willReturn('/fake-url');

        $this->obj = new Pagination($routerProphecy->reveal());
    }

    public function testArrayOutput1()
    {
        $items = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $expected   = [
            [
                'page'  => 1,
                'url'   => '/fake-url',
                'class' => 'active',
            ],
            [
                'page'  => 2,
                'url'   => '/fake-url',
                'class' => false,
            ],
            [
                'page'  => '&raquo;',
                'url'   => '/fake-url',
                'class' => false,
            ],
        ];

        $pagination = $this->obj->paginate($items, $this->routeName, 1, 2);

        $this->assertEquals($expected, $pagination);
    }
}
