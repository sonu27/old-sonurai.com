<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function indexAction($page = 1, $limit = 25)
    {
        $offset = ($page * $limit) - $limit;
        $em     = $this->getDoctrine()->getManager();
        $query  = $em
            ->createQuery('SELECT i FROM AppBundle:BingWallpaper i ORDER BY i.date DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $wallpapers = new Paginator($query, false);

        if (count($wallpapers) === 0) {
            throw $this->createNotFoundException('No images found');
        }

        $paginator  = $this->get('app.pagination');
        $pagination = $paginator->paginate($wallpapers, 'wallpaper_page', $page, $limit);

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
