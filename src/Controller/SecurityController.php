<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Cassandra\Exception\UnauthorizedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param UserRepository $userRepository
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $user = $this->getUser();
        $userId = $user->getId();
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $response = ["lastUsername" => $lastUsername, "error" => $error];

        $connectedUser = $userRepository->findOneById($userId);

        if(is_null($connectedUser)) {
            throw new UnauthorizedException('The user is no more connected.', Response::HTTP_FORBIDDEN, '');
        }

        return new JsonResponse($serializer->serialize($response, 'json'), Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('You are no more connected.');
    }
}
