<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends Controller
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
