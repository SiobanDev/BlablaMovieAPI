<?php
namespace App\Controller;
use App\Repository\UserRepository;
use Exception;
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
     * @Route("/api/logout", name="app_logout")
     * @throws Exception
     */
    public function logout()
    {
        throw new \Exception('You are no more connected.');
//        $this->redirect('accueil', Response::HTTP_PERMANENTLY_REDIRECT);
    }
}