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

    public function testOutputIsAnEmptyArrayWhenCountIsLessThanOne()
    {
        $pagination = $this->obj->paginate(0, $this->routeName, 1, 2);

        $this->assertEquals([], $pagination);
    }

    /**
     * @expectedException \LogicException
     */
    public function testLogicExceptionIsThrownWhenPageCannotExist()
    {
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
