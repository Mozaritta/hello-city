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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class PinsController extends AbstractController
{

    /**
     * @Route("/", name="app_home", methods="GET")
     * @Route("/pin/app_home")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }
    /**
     * @Route("/pin/{id<[0-9]+>}",methods={"GET","HEAD"}, name="app_pins_show")
     */
    public function show(AuthenticationUtils $authenticationUtils, PinRepository $pinRepository, int $id): Response
    {
        // $pin = ;
        // return dd($_GET['pin']);
        // dd($authenticationUtils->getLastUsername());
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
            $this->addFlash(
                'success',
                $message
            );
            return $this->render('pins/show.html.twig', compact('message'));
        }
    }

    /**
     * @Route("/pin/create", name="pin_create", methods="GET|POST")
     */
    public function create(AuthenticationUtils $authenticationUtils, Request $request, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            // dd($authenticationUtils->getLastUsername());
            // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
                $this->addFlash(
                    'success',
                    'Pin added successfully'
                );
                return $this->redirectToRoute('app_home');
            }

            return $this->render('pins/create.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->addFlash(
                'danger',
                'You are not logged in!'
            );
            return $this->redirectToRoute('app_login');
        }
    }
    /**
     * @Route("/pin/edit/{id<[0-9]+>}", methods={"GET","HEAD", "POST"}, name="app_edit")
     */
    public function edit(AuthenticationUtils $authenticationUtils, PinRepository $pinRepository, Request $request, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $pin = $pinRepository->findOneBy(['id' => $id]);
            $user = $pin->getUser()->getId();
            $connection = mysqli_connect("localhost", "root", "", "pinterest");
            $query = "SELECT email FROM users WHERE id = $user";
            $result = mysqli_query($connection, $query);
            $yes = mysqli_fetch_assoc($result);
            // dd($yes);
            if ($yes === $authenticationUtils->getLastUsername()) {
                $form = $this->createForm(PinType::class, $pin);
                // $form = $this->createFormBuilder($pin)
                //     ->add('title')
                //     ->add('description')
                //     ->getForm();
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em->flush();
                    $this->addFlash(
                        'info',
                        'Pin updated successfully'
                    );
                    return $this->redirectToRoute('app_home');
                }
                return $this->render('pins/edit.html.twig', [
                    'pin' => $pin,
                    'form' => $form->createView()
                ]);
            } else {
                $this->addFlash(
                    'info',
                    'You didn\' create this pin so you can\'t update it :/'
                );
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash(
                'danger',
                'You are not logged in!'
            );
            return $this->redirectToRoute('app_login');
        }
    }
    /**
     * @Route("/pin/delete/{id<[0-9]+>}", name="app_delete", methods={"GET", "HEAD", "POST", "DELETE"})
     */
    public function delete(AuthenticationUtils $authenticationUtils, Request $request, PinRepository $pinRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $pinn = $pinRepository->findOneBy(['id' => $id]);
            $user = $pinn->getUser()->getId();
            $connection = mysqli_connect("localhost", "root", "", "pinterest");
            $query = "SELECT email FROM users WHERE id = $user";
            $result = mysqli_query($connection, $query);
            $yes = mysqli_fetch_assoc($result);
            // dd($yes);
            if ($yes === $authenticationUtils->getLastUsername()) {
                // if ($this->isCsrfTokenValid('delete-pin', $pinn->getId(), $request->request->get('csrf_token'))) {
                $em->remove($pinn);
                $em->flush();
                $message = 'Pin deleted successfully';
                $this->addFlash(
                    'danger',
                    $message
                );
                // }
                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash(
                    'info',
                    'You didn\' create this pin so you can\'t delete it :/'
                );
                return $this->redirectToRoute('app_home');
            }
        } else {
            $this->addFlash(
                'danger',
                'You are not logged in!'
            );
            return $this->redirectToRoute('app_login');
        }
    }
}