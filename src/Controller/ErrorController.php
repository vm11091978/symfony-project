<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException as Unique;

class ErrorController extends AbstractController
{
    public function show(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $exc = $request->attributes->get('exception');
        $patch = $request->getPathInfo();
        if ($exc instanceof Unique) {
            $login = $request->request->all('user')['login'] ?? null;
            $this->addFlash('message_error', "логин $login занят! Придумайте другой логин!");
            return $this->redirect($patch);
        }

        $code = $exception->getStatusCode();
        if ($code == 404) {
            $errorMessage = 'Такой страницы не существует';
        } else {
            $errorMessage = 'Неизвестная ошибка';
        }
        return $this->render('exception/error.html.twig', [
            'errorMessage' => "<strong>$errorMessage</strong>" 
        ]);
    }
}
