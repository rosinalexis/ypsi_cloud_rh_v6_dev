<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(
            [
                "title" => "YPSI CLOUD RH V6",
                "version" => "0.6",
                "time" => new DateTimeImmutable(),
                "message" => "Welcome To Ypsi Cloud RH API "
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/app/config', name: 'app_config', methods: ['POST'])]
    public function addFirstAdminAndCompany(Request $request, SerializerInterface $serializer,EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $userInfo = json_encode($request->toArray()['user']);
        $companyInfo = json_encode($request->toArray()['company']);

        /**
         * @var $user User
         */
        $user = $serializer->deserialize($userInfo, User::class, 'json');
        $user->setBlocked(false);
        $user->setConfirmed(true);
        $user->setRoles(User::ROLE_ADMIN);

        /**
         * @var $company Company
         */
        $company = $serializer->deserialize($companyInfo,Company::class,'json');


        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $errors = $validator->validate($company);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($user);
        $em->flush();

        $em->persist($company);
        $user->setCompany($company);
        $em->flush();

        $jsonUser = $serializer->serialize($user,'json',['groups' => 'user:read']);

        //envoyer l'email de confirmation

        return new JsonResponse($jsonUser, Response::HTTP_CREATED,[],true);
    }
}
