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

    public function vision()
    {
        $client = new \GuzzleHttp\Client();

        $results = [];

        $wallpapers = $this->wallpaperRepo->get(0, 5);

        /** @var BingWallpaper $wallpaper */
        foreach ($wallpapers as $wallpaper) {
            $fileName = 'http://sonurai.com/wallpaper/'.$wallpaper->getName().'.jpg';

            $url = 'https://vision.googleapis.com/v1/images:annotate?key=AIzaSyB_2cI_2XHtQyOhfU-zqPPFD49DNHv-FwQ';
            $res = $client->request('POST', $url, ['json' => [
                'requests' => [
                    [
                        'image'    => [
                            'source' => [
                                'imageUri' => $fileName,
                            ],
                        ],
                        'features' => [
                            [
                                'type' => 'LABEL_DETECTION',
                            ],
                            [
                                'type' => 'IMAGE_PROPERTIES',
                            ],
                        ],
                    ],
                ],
            ]]);

            $content   = (string)$res->getBody();
            $content   = json_decode($content, true);
            $content   = $content['responses'];
            $results[] = $content;

            $wallpaper->setData($content);
            $this->wallpaperRepo->save($wallpaper);
        }

        return new JsonResponse($results);
    }
}
