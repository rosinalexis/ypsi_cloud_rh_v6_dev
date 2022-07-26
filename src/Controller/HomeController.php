<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return new JsonResponse(
            [
                "title" => "YPSI CLOUD RH V6",
                "version" => "0.6",
                "time" => new \DateTimeImmutable(),
                "message" => "Welcome To the backend of Ypsi Cloud RH "
            ],
            Response::HTTP_OK
        );
    }
}
