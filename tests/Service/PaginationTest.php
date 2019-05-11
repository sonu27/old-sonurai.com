<?php

namespace App\Tests\Service;

use App\Service\Pagination;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Routing\RouterInterface;

class PaginationTest extends TestCase
{
    protected $obj;
    protected $routeName = 'fake_route';

    protected function setUp(): void
    {
        $routerProphecy = $this->prophesize();
        $routerProphecy->willExtend('stdClass');
        $routerProphecy->willImplement(RouterInterface::class);
        $routerProphecy->generate(Argument::any(), Argument::any())->willReturn('/fake-url');

        $this->obj = new Pagination($routerProphecy->reveal());
    }

    public function testOutputIsAnEmptyArrayWhenCountIsLessThanOne()
    {
        $pagination = $this->obj->paginate(0, $this->routeName, 1, 2);

        $this->assertEquals([], $pagination);
    }

    public function testLogicExceptionIsThrownWhenPageCannotExist()
    {
        $this->expectException(\LogicException::class);
        $pagination = $this->obj->paginate(10, $this->routeName, 3, 5);

        $this->assertEquals([], $pagination);
    }

    public function testArrayOutput1()
    {
        $expected = [
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

        $pagination = $this->obj->paginate(3, $this->routeName, 1, 2);

        $this->assertEquals($expected, $pagination);
    }

    public function testArrayOutputWhenPageIsMoreThanOne()
    {
        $expected = [
            [
                'page'  => '&laquo;',
                'url'   => '/fake-url',
                'class' => false,
            ],
            [
                'page'  => 1,
                'url'   => '/fake-url',
                'class' => false,
            ],
            [
                'page'  => 2,
                'url'   => '/fake-url',
                'class' => 'active',
            ],
        ];

        $pagination = $this->obj->paginate(3, $this->routeName, 2, 2);

        $this->assertEquals($expected, $pagination);
    }
}
