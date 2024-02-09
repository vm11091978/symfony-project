<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends AbstractController
{
    public function show(FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $code = $exception->getStatusCode();
        if ($code == 404) {
            return $this->render('exception/error.html.twig', [
                'errorMessage' => "<strong>Такой страницы не существует</strong>" 
            ]);
        }
    }
}
