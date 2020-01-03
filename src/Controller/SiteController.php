<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends AbstractController
{
    public function home(): Response
    {
        return $this->render('site/home.html.twig');
    }

    public function about(): Response
    {
        return $this->render('site/about.html.twig');
    }

    public function contact(): Response
    {
        return $this->render('site/contact.html.twig');
    }
}
