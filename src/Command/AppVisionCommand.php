<?php

namespace App\Command;

use App\Entity\BingWallpaper;
use App\Repository\BingWallpaperRepository;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppVisionCommand extends Command
{
    private $wallpaperRepo;

    protected static $defaultName = 'app:vision';

    public function __construct(BingWallpaperRepository $wallpaperRepo)
    {
        parent::__construct();

        $this->wallpaperRepo = $wallpaperRepo;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Runs Google Vision on wallpapers')
            ->addArgument('offset', InputArgument::OPTIONAL, 'Offset')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Limit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->hasArgument('offset') && $input->hasArgument('limit')) {
            $offset = (int)$input->getArgument('offset');
            $limit  = (int)$input->getArgument('limit');

            $wallpapers = $this->wallpaperRepo->get($offset, $limit);
        } else {
            $wallpapers = $this->wallpaperRepo->findByEmptyData();
        }

        $results = [];

        foreach ($wallpapers as $wallpaper) {
            $results[] = $this->vision($wallpaper);
        }

        $io->success('Done '.\count($results));
    }

    private function vision(BingWallpaper $wallpaper)
    {
        $client   = new Client();
        $fileName = 'https://sonurai.com/wallpaper/'.$wallpaper->getName().'.jpg';

        $url = 'https://vision.googleapis.com/v1/images:annotate?key='.getenv('VISION_API_KEY');
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

        $content = (string)$res->getBody();
        $content = json_decode($content, true);
        $content = $content['responses'];

        $wallpaper->setData($content);
        $this->wallpaperRepo->save($wallpaper);

        return $content;
    }
}
