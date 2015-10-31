<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\BingMarketRepository;
use AppBundle\Entity\BingWallpaperRepository;
use AppBundle\Service\BingWallpaper;
use Prophecy\Argument;

class BingWallpaperTest extends \PHPUnit_Framework_TestCase
{
    protected $obj;
    protected $routeName = 'fake_route';

    protected function setUp()
    {
        $wallpaperRepoProphecy = $this->prophesize('AppBundle\Entity\BingWallpaperRepository');

        $marketRepoProphecy = $this->prophesize('AppBundle\Entity\BingMarketRepository');

        $this->obj = new BingWallpaper($wallpaperRepoProphecy->reveal(), $marketRepoProphecy->reveal());
    }

    public function testCleanTitleRemoveString()
    {
        $url = '/az/hprichbg/rb/';

        $this->assertEquals('', $this->obj->cleanTitle($url));
    }

    public function testCleanTitleOnlyReturnsName()
    {
        $url = 'EasterIslandSmiles_EN-US8811665215';

        $this->assertEquals('EasterIslandSmiles', $this->obj->cleanTitle($url));
    }
}
