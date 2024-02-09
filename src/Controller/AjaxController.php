<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ajax')]
class AjaxController extends AbstractController
{
    #[Route('/get/{id}', name: 'app_ajax_get', methods: ['GET'])]
    public function showByGET(Article $article): Response
    {
        return $this->json($article->getContent());
    }
        
    #[Route('/post', name: 'app_ajax_post', methods: ['POST'])]
    public function showByPOST(ArticleRepository $articleRepository): Response
    {
        $id = (int)$_POST['articleId'];
        $article = $articleRepository->find($id);
        return $this->json($article->getContent());
    }
}
