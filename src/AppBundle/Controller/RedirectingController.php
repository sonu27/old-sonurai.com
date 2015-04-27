<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RedirectingController extends Controller
{
    public function wallpaperRedirectAction($name)
    {
        $basePath = $this->get('kernel')->getBundle('AppBundle')->getPath().'/Resources/public/wallpaper/';
        $path = $basePath.$name;

        if (!file_exists($path)) {
            $wallpaperService = $this->get('app.bing_wallpaper');
            $wallpaperRepo    = $this->get('app.bing_wallpaper_repository');

            $cleanName = $wallpaperService->cleanTitle($name);
            $wallpaper = $wallpaperRepo->findOneLikeName($cleanName);

            if ($wallpaper !== null && file_exists($basePath.$wallpaper->getName().'.jpg')) {
                return $this->redirect('/wallpaper/'.$wallpaper->getName().'.jpg');
            }
        }

        throw $this->createNotFoundException('No wallpaper found');
    }

    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }
}
