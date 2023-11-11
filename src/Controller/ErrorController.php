<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    public function NotFoundShow(): Response
    {
        // Personaliza la respuesta de error 404
        return $this->render('error/notFound.html.twig', [
            'route' => "app:error404"
        ]);
    }

}
