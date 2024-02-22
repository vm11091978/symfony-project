<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
    public function index(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
	// если авторизованный пользователь заходит с публичной страницы сайта на страницу авторизации,
	// сразу перенаправим его в администраторскую часть приложения
	if ($this->isGranted('ROLE_AUTH_USER')) {
	    return $this->redirectToRoute('app_article_index');
	} elseif ($this->isGranted('ROLE_BLOCKED')) {
	    $this->addFlash('message_error', 'Ваш аккаунт заблокирован, обратитесь к администратору.');
	} elseif ($this->isGranted('ROLE_USER')) {
	    $this->addFlash('message_error', 'Недействительные аутентификационные данные.');
	}

        // получить ошибку входа, если она есть
        $error = $authenticationUtils->getLastAuthenticationError();
	if ($error) {
	    $this->addFlash('message_error', 'Неправильный логин или пароль, попробуйте ещё раз.');
	}

        // последнее имя пользователя, введенное пользователем
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'pageTitle' => $this->pageTitle,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // контроллер может быть пустым: он не будет вызван!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
