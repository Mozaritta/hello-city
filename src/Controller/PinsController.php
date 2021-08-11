<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
    public function show(PinRepository $pinRepository, int $id): Response
    {
        // $pin = ;
        // return dd($_GET['pin']);
        $connection = mysqli_connect("localhost", "root", "", "pinterest");
        $query = "SELECT * FROM pins WHERE id = $id";
        $result = mysqli_query($connection, $query);
        $pinn = mysqli_fetch_assoc($result);
        $pin = $pinRepository->findOneBy(['id' => $id]);

        if (isset($pinn['id'])) {
            // dd($pin);
            return $this->render('pins/show.html.twig', compact('pin'));
        } else {
            $message = "No such Pin in the database!";
            return $this->render('pins/show.html.twig', compact('message'));
        }
    }

    /**
     * @Route("/pin/create", name="pin_create", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);
        // $form = $this->createFormBuilder($pin)
        //     // $form = $this->createFormBuilder(new Pin)
        //     ->add('title')
        //     ->add('description')
        //     // // // ->add('submit', SubmitType::class)
        //     ->getForm();
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
     * @Route("/pin/edit/{id<[0-9]+>}", methods={"GET","HEAD", "POST"}, name="app_edit")
     */
    public function edit(PinRepository $pinRepository, Request $request, int $id, EntityManagerInterface $em): Response
    {
        $pin = $pinRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(PinType::class, $pin);
        // $form = $this->createFormBuilder($pin)
        //     ->add('title')
        //     ->add('description')
        //     ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/pin/delete/{id<[0-9]+>}", name="app_delete", methods={"GET", "HEAD", "POST", "DELETE"})
     */
    public function delete(Request $request, PinRepository $pinRepository, int $id, EntityManagerInterface $em): Response
    {
        $pinn = $pinRepository->findOneBy(['id' => $id]);
        if ($this->isCsrfTokenValid('delete-pin', $pinn->getId(), $request->request->get('csrf_token'))) {
            $em->remove($pinn);
            $em->flush();
        }
        return $this->redirectToRoute('app_home');
    }
}