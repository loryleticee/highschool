<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Helpers\MessageExecuted;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Security $security, MessageBusInterface $messageBus): Response
    {
        if ($security->getUser()) {
            $user = $security->getUser();
            $MessageExecuted = new MessageExecuted($user->getId());
            
            $messageBus->dispatch( $MessageExecuted );
        }

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $encoder, MailerInterface $mailer, MessageBusInterface $messageBus): Response
    {
        $user = new User();
        $mentors = $userRepository->findByRole("mentor");

        $form = $this->createForm(UserType::class, $user, ['mentors' => $mentors, 'mode' => 'new']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashPassword = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hashPassword);

            $file = $form['image_url']->getData();
            $name = $file->getClientOriginalName();
            $dir = "/Users/loryleticee/sites/highSchool/assets/images";
            $file->move($dir, $name);

            $user->setImageUrl("/images/".$name);
         
            $userRepository->add($user);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $mentors = $userRepository->findByRole("mentor");
        $form = $this->createForm(UserType::class, $user, ["mode" => __FUNCTION__, 'mentors' => $mentors]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($file = $form['new_image_url']->getData()) {
                $name = $file->getClientOriginalName();
                $dir = "/Users/loryleticee/sites/highSchool/public/images";
                $file->move($dir, $name);
                
                $user->setImageUrl("/images/".$name);
            }

            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
