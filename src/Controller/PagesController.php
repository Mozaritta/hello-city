<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function home(): Response
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/PagesController.php',
        // ]);
        return $this->render('pages/home.html.twig');
    }

    /**
     * @Route("/city", name="app_city")
     */
    public function city(): Response
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/PagesController.php',
        // ]);
        return $this->render('pages/city.html.twig');
    }
}
