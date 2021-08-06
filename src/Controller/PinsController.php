<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findAll();
        return $this->render('pins/index.html.twig', compact('pins'));
    }
    /**
     * @Route("/pin/{id<[0-9]+>}",methods={"GET","HEAD"}, name="app_pins_show")
     */
    public function show(int $id): Response
    {
        // $pin = ;
        // return dd($_GET['pin']);
        $connection = mysqli_connect("localhost", "root", "", "pinterest");
        $query = "SELECT * FROM pins WHERE id = $id";
        $result = mysqli_query($connection, $query);
        $pin = mysqli_fetch_assoc($result);
        // dd($pin);
        return $this->render('pins/show.html.twig', compact('pin'));
    }
}