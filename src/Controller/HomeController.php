<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(
            [
                "title" => "YPSI CLOUD RH V6",
                "version" => "0.6",
                "time" => new \DateTimeImmutable(),
                "message" => "Welcome To Ypsi Cloud RH API "
            ],
            Response::HTTP_OK
        );
    }
    #[Route('/app/config',name: 'app_config',methods: ['POST'])]
    public function addFirstAdminAndCompany(Request $request, SerializerInterface $serializer,EntityManagerInterface $em,UserRepository $userRepository):JsonResponse
    {
        /**
         * @var $user User
         */
        $user = $serializer->deserialize($request->getContent(),User::class,'json');
        $user->setBlocked(false);
        $user->setConfirmed(true);


        $checkUser = $userRepository->findOneBy(['email' =>$user->getEmail()]);

        $data =[
            'status' => Response::HTTP_CONFLICT,
            'message' => 'user already exists.'
        ];

        $status = Response::HTTP_CONFLICT;

        if (!$checkUser)
        {
            $em->persist($user);
            $em->flush();

            $data =[
                'status' => Response::HTTP_CREATED,
                'message' => $user
            ];

            $status = Response::HTTP_CREATED;
        }

        return  new JsonResponse($data,$status);
    }
}
