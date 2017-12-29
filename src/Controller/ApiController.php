<?php

namespace App\Controller;

use App\Entity\BingWallpaper;
use App\Repository\BingWallpaperRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController
{
    private $wallpaperRepo;

    public function __construct(BingWallpaperRepository $repository)
    {
        $this->wallpaperRepo = $repository;
    }

    public function index(int $page = 1, int $limit = 10): JsonResponse
    {
        $offset     = ($page * $limit) - $limit;
        $wallpapers = $this->wallpaperRepo->get($offset, $limit);

        return new JsonResponse([
            'wallpapers' => $wallpapers,
            'pagination' => [
                'prev'    => $page - 1,
                'current' => $page,
                'next'    => $page + 1,
            ],
        ]);
    }

    public function id(int $id): JsonResponse
    {
        $wallpaper = $this->wallpaperRepo->find($id);

        return new JsonResponse([
            'wallpaper' => $wallpaper,
        ]);
    }
}
