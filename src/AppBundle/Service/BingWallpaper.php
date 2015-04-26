<?php

namespace AppBundle\Service;

use AppBundle\Entity\BingWallpaper as Wallpaper;
use AppBundle\Entity\BingWallpaperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class BingWallpaper
{
    const CHINA_MARKET = 'zh-cn';
    const BING_URL = 'http://www.bing.com';

    private $wallpaperRepo;
    private $marketRepo;
    private $em;

    public function __construct(
        BingWallpaperRepository $wallpaperRepo,
        EntityRepository $marketRepo,
        EntityManagerInterface $em
    ) {
        $this->wallpaperRepo = $wallpaperRepo;
        $this->marketRepo = $marketRepo;
        $this->em = $em;
    }

    public function updateWallpapers($path)
    {
        //$path = '/www/live/src/AR/WallpaperBundle/Resources/public/img/';
        $saved   = [];
        $markets = $this->marketRepo->findAll();

        foreach ($markets as $market) {
            $titles          = $this->getAllTitles();
            $chinaWallpapers = $this->getAllChinaTitles();
            $market          = $market->getName();
            $images          = $this->getImages($market);
            foreach ($images as $image) {
                $cleanTitle = $this->cleanTitle($image->urlBase);
                $cleanName  = $this->getNameFromUrlBase($image->urlBase);
                $fullImage  = $path.'bingwallpaper/'.$cleanName.'.jpg';
                $thumbnail  = $path.'bingwallpaper/'.$cleanName.'_th.jpg';

                if (// It's not a china wallpaper but exists in the db as a china wallpaper
                    $market != 'zh-cn'
                    && in_array($cleanTitle, $chinaWallpapers)
                    && $this->imagesExist($image)
                ) {
                    $chinaWallpaper = $this->getChina($cleanTitle);
                    if (false !== $chinaWallpaper) {
                        $fullCopied      = copy($this->getFullUrl($image), $fullImage);
                        $thumbnailCopied = copy($this->getThumbnailUrl($image), $thumbnail);

                        if ($fullCopied && $thumbnailCopied) {
                            $this->saveWallpaper($market, $image, $chinaWallpaper);
                            $saved[] = $chinaWallpaper->getName().' - '.$chinaWallpaper->getMarket();
                        }
                    }
                } elseif (
                    !in_array($cleanTitle, $titles)
                    && $this->imagesExist($image)
                ) {
                    $fullCopied      = copy($this->getFullUrl($image), $fullImage);
                    $thumbnailCopied = copy($this->getThumbnailUrl($image), $thumbnail);

                    if ($fullCopied && $thumbnailCopied) {
                        $wallpaper = $this->saveWallpaper($market, $image);
                        $saved[]   = $wallpaper->getName().' - '.$wallpaper->getMarket();
                    }
                }
            }
        }

        return $saved;
    }

    public function getImages($market)
    {
        $xmlUrl = self::BING_URL.'/HPImageArchive.aspx?format=xml&idx=0&n=10&mkt='.$market;

        $xml = simplexml_load_file($xmlUrl);

        unset($xml->tooltips);

        $images = [];
        foreach ($xml->image as $item) {
            $images[] = $item;
        }

        return array_reverse($images);
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

    public function getNameFromUrlBase($url)
    {
        return str_replace('/az/hprichbg/rb/', '', $url);
    }

    private function cleanTitle($url)
    {
        $name = $this->getNameFromUrlBase($url);
        $name = explode('_', $name);
        $name = trim($name[0]);

        return $name;
    }

    private function getAllTitles()
    {
        return $this->getNames($this->wallpaperRepo->findAll());
    }

    private function getAllChinaTitles()
    {
        return $this->getNames($this->wallpaperRepo->findByMarket('zh-cn'));
    }

    private function getNames(array $wallpapers)
    {
        $names = [];
        foreach ($wallpapers as $wallpaper) {
            $names[] = $this->cleanTitle($wallpaper->getName());
        }

        return $names;
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

    private function wallpaperInDb($name)
    {
        $query = $this->wallpaperRepo->createQueryBuilder('i')
            ->where('i.name LIKE :name')
            ->setParameter('name', $name.'%')
            ->getQuery();

        $image = $query->getResult();

        if (!$image) {
            $inDb = false;
        } else {
            $inDb = true;
        }

        return $inDb;
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
        return self::BING_URL.$image->urlBase.'_1920x1200.jpg';
    }

    private function getThumbnailUrl($image)
    {
        return self::BING_URL.$image->url;
    }

    private function saveWallpaper($market, $image, $wallpaper = false)
    {
        if (false === $wallpaper) {
            $wallpaper = new Wallpaper();
        }

        $wallpaper->setMarket($market);
        $wallpaper->setDate($image->startdate);
        $wallpaper->setName($this->getNameFromUrlBase($image->urlBase));
        $wallpaper->setDescription($image->copyright);

        $this->wallpaperRepo->save($wallpaper);

        return $wallpaper;
    }
}
