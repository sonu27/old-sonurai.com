<?php

namespace App\Service;

use App\Entity\BingWallpaper;
use App\Repository\BingWallpaperRepository;

class BingWallpaperUpdater
{
    private const CHINA_MARKET = 'zh-cn';
    private const BING_URL     = 'http://www.bing.com';
    private const MARKETS      = [
        'en-ww',
        'en-gb',
        'en-us',
        'zh-cn',
    ];

    private $wallpaperRepo;

    public function __construct(BingWallpaperRepository $wallpaperRepo)
    {
        $this->wallpaperRepo = $wallpaperRepo;
    }

    public function updateWallpapers(string $path): array
    {
        $saved = [];

        foreach (self::MARKETS as $market) {
            $this->updateForMarket($market, $path, $saved);
        }

        return $saved;
    }

    private function updateForMarket(string $market, string $path, array &$saved)
    {
        $images = $this->getImages($market);

        foreach ($images as $image) {
            if (!$this->imagesExist($image)) {
                continue;
            }

            $nameId    = $this->cleanTitle($image->urlbase);
            $wallpaper = $this->wallpaperRepo->findOneByNameId($nameId);

            $cleanName = $this->getNameFromUrlBase($image->urlbase);
            $filename  = $path.$cleanName;

            if ($wallpaper !== null) {
                // It's not a china wallpaper but exists in the db as a china wallpaper
                if ($market !== self::CHINA_MARKET && $wallpaper->getMarket() === self::CHINA_MARKET) {
                    if ($this->copyImages($image, $filename)) {
                        $this->saveWallpaper($market, $image, $wallpaper);
                        $saved[] = $wallpaper->getName().' - '.$wallpaper->getMarket();
                    }
                }
            } elseif ($this->copyImages($image, $filename)) {
                $wallpaper = $this->saveWallpaper($market, $image);
                $saved[]   = $wallpaper->getName().' - '.$wallpaper->getMarket();
            }
        }
    }

    public function cleanTitle($url)
    {
        $name = $this->getNameFromUrlBase($url);
        $name = explode('_', $name);
        $name = trim($name[0]);

        return $name;
    }

    private function copyImages($image, $path)
    {
        $fullCopied      = copy($this->getFullUrl($image), $path.'.jpg');
        $thumbnailCopied = copy($this->getThumbnailUrl($image), $path.'_th.jpg');

        return ($fullCopied && $thumbnailCopied);
    }

    public function getNameFromUrlBase($url)
    {
        $replacements = [
            '/az/hprichbg/rb/',
            '/th?id=OHR.',
        ];

        return str_replace($replacements, '', $url);
    }

    public function getImages($market)
    {
        $url    = self::BING_URL.'/HPImageArchive.aspx?format=js&idx=0&n=10&mkt='.$market;
        $json   = file_get_contents($url);
        $images = json_decode($json, false)->images;

        return array_reverse($images);
    }

    private function imagesExist($image)
    {
        $bothExist = false;
        $fullImage = $this->getFullUrl($image);
        $thumbnail = $this->getThumbnailUrl($image);

        if ($this->fileExist($fullImage) && $this->fileExist($thumbnail)) {
            $bothExist = true;
        }

        return $bothExist;
    }

    private function getFullUrl($image)
    {
        return self::BING_URL.$image->urlbase.'_1920x1200.jpg';
    }

    private function getThumbnailUrl($image)
    {
        return self::BING_URL.$image->url;
    }

    public function fileExist($url)
    {
        $curl = curl_init($url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);

        $fileExists = false;

        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

            if ($statusCode === 200) {
                $fileExists = true;
            }
        }

        curl_close($curl);

        return $fileExists;
    }

    private function saveWallpaper(string $market, $image, BingWallpaper $wallpaper = null): BingWallpaper
    {
        if ($wallpaper === null) {
            $wallpaper = new BingWallpaper();
            $wallpaper->setDate($image->startdate);
        }

        $wallpaper->setMarket($market);
        $wallpaper->setName($this->getNameFromUrlBase($image->urlbase));
        $wallpaper->setDescription($image->copyright);

        $this->wallpaperRepo->save($wallpaper);

        return $wallpaper;
    }
}
