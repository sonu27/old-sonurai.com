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

        $paginator  = new Paginator($query, false);
        $itemCount = count($paginator);
        $pageCount = ceil($itemCount / $limit);

        $images = [];
        foreach ($paginator as $image) {
            $images[] = $image;
        }

        if (empty($images)) {
            throw $this->createNotFoundException('No images!');
        }

        $padding    = 4;
        $pagination = [];
        $start      = $page - $padding;
        $end        = $page + $padding;

        if ($start < 1) {
            $end -= $start - 1;
            $start = 1;
        }

        if ($end > $pageCount) {
            $end = $pageCount;
            for (null; ($start > 1) && (($end - $start) <= 3); $start--) {
                ;
            }
        }

        if ($page > 1) {
            $pagination[] = [
                'page'  => '&laquo;',
                'url'   => $this->generateUrl('wallpaper_page', array('page' => $page - 1)),
                'class' => false,
            ];
        }

        for ($i = $start; $i <= $end; $i++) {
            $pagination[] = [
                'page'   => $i,
                'url'    => $this->generateUrl('wallpaper_page', array('page' => $i)),
                'class' => ($i == $page) ? 'active' : false,
            ];
        }

        if ($page != $pageCount) {
            $pagination[] = [
                'page'  => '&raquo;',
                'url'   => ($page < $pageCount ? $this->generateUrl('wallpaper_page', array('page' => $page + 1)) : '#'),
                'class' => false,
            ];
        }

        $wallpapers = $query->getResult();

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
