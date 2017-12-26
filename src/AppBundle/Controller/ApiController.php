<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/bingwallpapers", name="wallpaper_index_json")
     * @Route("/bingwallpapers/page/{page}", requirements={"id" = "\d+"}, name="wallpaper_page_json")
     */
    public function indexAction(int $page = 1, int $limit = 10): JsonResponse
    {
        $wallpaperRepo = $this->get('app.bing_wallpaper_repository');

        $offset     = ($page * $limit) - $limit;
        $wallpapers = $wallpaperRepo->get($offset, $limit);

        if (\count($wallpapers) === 0) {
            throw new NotFoundHttpException('No wallpapers found');
        }

        return new JsonResponse([
            'wallpapers' => $wallpapers,
            'pagination' => [
                'prev'    => $page - 1,
                'current' => $page,
                'next'    => $page + 1,
            ],
        ]);
    }

    /**
     * @Route("/bingwallpapers/{id}", requirements={"id" = "\d+"}, name="wallpaper_show_json")
     * @ParamConverter("wallpaper", class="AppBundle:BingWallpaper")
     */
    public function showAction(\AppBundle\Entity\BingWallpaper $wallpaper): JsonResponse
    {
        return new JsonResponse([
            'wallpaper' => $wallpaper,
        ]);
    }
}
