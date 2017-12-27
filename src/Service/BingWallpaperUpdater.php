<?php

namespace App\Service;

use App\Entity\BingWallpaper;
use App\Repository\BingWallpaperRepository;

class BingWallpaperUpdater
{
    private const CHINA_MARKET = 'zh-cn';
    private const BING_URL     = 'http://www.bing.com';

    private $wallpaperRepo;

    public function __construct(BingWallpaperRepository $wallpaperRepo)
    {
        $this->wallpaperRepo = $wallpaperRepo;
    }

    public function updateWallpapers(string $path): array
    {
        $saved   = [];
        $markets = [
            'en-ww',
            'en-gb',
            'en-us',
            'zh-cn',
        ];

        foreach ($markets as $market) {
            $titles          = $this->getAllTitles();
            $chinaWallpapers = $this->getAllChinaTitles();
            $images          = $this->getImages($market);

            foreach ($images as $image) {
                $cleanTitle = $this->cleanTitle($image->urlbase);
                $cleanName  = $this->getNameFromUrlBase($image->urlbase);
                $filename   = $path.$cleanName;

                if (// It's not a china wallpaper but exists in the db as a china wallpaper
                    $market !== self::CHINA_MARKET
                    && \in_array($cleanTitle, $chinaWallpapers, true)
                    && $this->imagesExist($image)
                ) {
                    $chinaWallpaper = $this->getChina($cleanTitle);
                    if ($chinaWallpaper !== false) {
                        if ($this->copyImages($image, $filename)) {
                            $this->saveWallpaper($market, $image, $chinaWallpaper);
                            $saved[] = $chinaWallpaper->getName().' - '.$chinaWallpaper->getMarket();
                        }
                    }
                } elseif (
                    !\in_array($cleanTitle, $titles, true)
                    && $this->imagesExist($image)
                ) {
                    if ($this->copyImages($image, $filename)) {
                        $wallpaper = $this->saveWallpaper($market, $image);
                        $saved[]   = $wallpaper->getName().' - '.$wallpaper->getMarket();
                    }
                }
            }
        }

        return $saved;
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

    private function getAllTitles()
    {
        return $this->getNames($this->wallpaperRepo->findAll());
    }

    private function getNames(array $wallpapers)
    {
        $names = [];
        foreach ($wallpapers as $wallpaper) {
            $names[] = $this->cleanTitle($wallpaper->getName());
        }

        return $names;
    }

    public function getNameFromUrlBase($url)
    {
        return str_replace('/az/hprichbg/rb/', '', $url);
    }

    private function getAllChinaTitles()
    {
        return $this->getNames($this->wallpaperRepo->findByMarket('zh-cn'));
    }

    public function getImages($market)
    {
        $url = self::BING_URL.'/HPImageArchive.aspx?format=js&idx=0&n=10&mkt='.$market;

        $json = file_get_contents($url);
        $images = (\json_decode($json))->images;


//        $images = [];
//        foreach ($xml->image as $item) {
//            $images[] = $item;
//        }

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
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $fileExists = true;
            }
        }

        curl_close($curl);

        return $fileExists;
    }

    private function getChina($name)
    {
        $chinaWallpapers = $this->wallpaperRepo->findByNameAndMarket($name, self::CHINA_MARKET);

        foreach ($chinaWallpapers as $chinaWallpaper) {
            $cleanTitle = $this->cleanTitle($chinaWallpaper->getName());
            if ($cleanTitle == $name) {
                return $chinaWallpaper;
            }
        }

        return false;
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
