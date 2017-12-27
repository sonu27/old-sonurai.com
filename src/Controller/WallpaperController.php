<?php

namespace App\Controller;

use App\Repository\BingWallpaperRepository;
use App\Service\BingWallpaperUpdater;
use App\Service\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WallpaperController extends Controller
{
    private const PATH = '/wallpaper/';

    private $wallpaperRepo;
    private $pagination;

    public function __construct(BingWallpaperRepository $repository, Pagination $pagination)
    {
        $this->wallpaperRepo = $repository;
        $this->pagination    = $pagination;
    }

    public function index(int $page = 1, int $limit = 10): Response
    {
        $offset     = ($page * $limit) - $limit;
        $wallpapers = $this->wallpaperRepo->get($offset, $limit);
        $count      = $this->wallpaperRepo->countAll();

        if (\count($wallpapers) === 0) {
            throw new NotFoundHttpException('No wallpapers found');
        }

        $pagination = $this->pagination->paginate($count, 'wallpapers_index_page', $page, $limit);

        return $this->render('wallpaper/index.html.twig', [
            'page'       => $page,
            'pagination' => $pagination,
            'path'       => self::PATH,
            'wallpapers' => $wallpapers,
        ]);
    }

    public function id(int $id): Response
    {
        $wallpaper = $this->wallpaperRepo->find($id);
        if ($wallpaper === null) {
            throw new NotFoundHttpException('No wallpaper found');
        }

        $date = new \DateTimeImmutable($wallpaper->getDate());

        return $this->render('wallpaper/show.html.twig', [
            'date'      => $date,
            'path'      => self::PATH,
            'wallpaper' => $wallpaper,
        ]);
    }

    public function search(Request $request, int $page = 1, int $limit = 10): Response
    {
        $query      = $request->query->get('query');
        $offset     = ($page * $limit) - $limit;
        $wallpapers = $this->wallpaperRepo->search($query, $offset, $limit);
        $count      = $this->wallpaperRepo->countSearch($query);
        $pagination = $this->pagination->paginate($count, 'wallpapers_search_page', $page, $limit, ['query' => $query]);

        return $this->render('wallpaper/index.html.twig', [
            'count'      => $count,
            'page'       => $page,
            'pagination' => $pagination,
            'path'       => self::PATH,
            'query'      => $query,
            'wallpapers' => $wallpapers,
        ]);
    }

    public function update(): Response
    {
        $path            = $this->get('kernel')->getProjectDir().'/public/wallpaper/';

        $wallpaperHelper = $this->get(BingWallpaperUpdater::class);
        $result          = $wallpaperHelper->updateWallpapers($path);

        if (empty($result)) {
            return new Response('No Wallpapers Added');
        }

        return new Response(\count($result).' Wallpaper(s) Added - '.implode(', ', $result));
    }
}
