<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/')]
class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Security $security): Response
    {
        if ($security->getUser()) {
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
