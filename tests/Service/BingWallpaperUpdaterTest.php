<?php

namespace App\Tests\Service;

use App\Repository\BingWallpaperRepository;
use App\Service\BingWallpaperUpdater;
use PHPUnit\Framework\TestCase;

class BingWallpaperUpdaterTest extends TestCase
{
    protected $obj;
    protected $routeName = 'fake_route';

    protected function setUp(): void
    {
        $wallpaperRepoProphecy = $this->prophesize(BingWallpaperRepository::class);

        $this->obj = new BingWallpaperUpdater($wallpaperRepoProphecy->reveal());
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
