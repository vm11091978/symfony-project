<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @var string Тайтл страницы
     */
    private $pageTitle = "Admin Login | Widget News";

    /**
     * Вход в систему / Выводит на экран форму для входа в систему
     */
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'pageTitle' => $this->pageTitle,
        ]);
    }
}
