<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/bingwallpapers")
 */
class WallpaperController extends Controller
{
    const PATH = '//sonurai.com/bundles/arwallpaper/img/bingwallpaper/';

    /**
     * @Route("", name="wallpaper_index")
     * @Route("/page/{page}", requirements={"id" = "\d+"}, name="wallpaper_page")
     */
    public function indexAction($page = 1, $limit = 25)
    {
        $offset = ($page * $limit) - $limit;
        $em    = $this->getDoctrine()->getManager();
        $query = $em
            ->createQuery('SELECT i FROM AppBundle:BingWallpaper i ORDER BY i.date DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $wallpapers  = new Paginator($query, false);
        $paginator = $this->get('pagination');
        $pagination = $paginator->paginate($wallpapers, 'wallpaper_page', $page, $limit);

        return $this->render('wallpaper/index.html.twig', [
            'page' => $page,
            'pagination' => $pagination,
            'path' => self::PATH,
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
            'date' => $date,
            'path' => self::PATH,
            'wallpaper' => $wallpaper,
        ]);
    }
}
