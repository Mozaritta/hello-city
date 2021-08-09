<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class PinsController extends AbstractController
{

    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
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

    /**
     * @Route("/pin/create", name="pin_create", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
            // $form = $this->createFormBuilder(new Pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            // // // ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $pin = $form->getData();
            // // $data = $form->getData();
            // // $pin = new Pin;
            // // $pin->setTitle($data['title']);
            // // $pin->setDescription($data['description']);
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', [
            'myForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/pins/{id<[0-9]+>}/edit", methods={"GET","POST"}, name="app_edit")
     */
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/edit.html.twig', [
            'pin ' => $pin,
            'myForm' => $form->createView()
        ]);
    }
}