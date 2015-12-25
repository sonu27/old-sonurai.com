<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/bingwallpapers")
 */
class WallpaperController extends Controller
{
    const PATH = '/wallpaper/';

    /**
     * @Route("", name="wallpaper_index")
     * @Route("/page/{page}", requirements={"id" = "\d+"}, name="wallpaper_page")
     */
    public function indexAction($page = 1, $limit = 10)
    {
        $wallpaperRepo = $this->get('app.bing_wallpaper_repository');

        $offset     = ($page * $limit) - $limit;
        $wallpapers = $wallpaperRepo->get($offset, $limit);
        $count      = $wallpaperRepo->countAll();

        if (count($wallpapers) === 0) {
            throw $this->createNotFoundException('No wallpapers found');
        }

        $paginator  = $this->get('app.pagination');
        $pagination = $paginator->paginate($count, 'wallpaper_page', $page, $limit);

        return $this->render('wallpaper/index.html.twig', [
            'page'       => $page,
            'pagination' => $pagination,
            'path'       => self::PATH,
            'wallpapers' => $wallpapers,
        ]);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="wallpaper_show")
     * @ParamConverter("wallpaper", class="AppBundle:BingWallpaper")
     */
    public function showAction(\AppBundle\Entity\BingWallpaper $wallpaper)
    {
        $date = new \DateTime($wallpaper->getDate());

        return $this->render('wallpaper/show.html.twig', [
            'date'      => $date,
            'path'      => self::PATH,
            'wallpaper' => $wallpaper,
        ]);
    }

    /**
     * @Route("/search", name="wallpaper_search")
     * @Route("/search/page/{page}", requirements={"id" = "\d+"}, name="wallpaper_search_page")
     */
    public function searchWallpaperAction(Request $request, $page = 1, $limit = 10)
    {
        $wallpaperRepo = $this->get('app.bing_wallpaper_repository');

        $query      = $request->query->get('query');
        $offset     = ($page * $limit) - $limit;
        $wallpapers = $wallpaperRepo->search($query, $offset, $limit);
        $count      = $wallpaperRepo->countSearch($query);

        $paginator  = $this->get('app.pagination');
        $pagination = $paginator->paginate($count, 'wallpaper_search_page', $page, $limit, ['query' => $query]);

        return $this->render('wallpaper/search.html.twig', [
            'count'      => $count,
            'page'       => $page,
            'pagination' => $pagination,
            'path'       => self::PATH,
            'query'      => $query,
            'wallpapers' => $wallpapers,
        ]);
    }

    /**
     * @Route("/update", name="wallpaper_update")
     */
    public function updateWallpapersAction()
    {
        $path            = $this->get('kernel')->getBundle('AppBundle')->getPath().'/Resources/public/wallpaper/';
        $wallpaperHelper = $this->get('app.bing_wallpaper');
        $result          = $wallpaperHelper->updateWallpapers($path);

        if (empty($result)) {
            return new Response('No Wallpapers Added');
        } else {
            return new Response(count($result).' Wallpaper(s) Added - '.implode(", ", $result));
        }
    }
}
