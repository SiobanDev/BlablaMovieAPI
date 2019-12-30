<?php
namespace App\Controller;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
class IndexController extends AbstractController
{
    /**
     * @Rest\Get("/", name="home")
     * @return JsonResponse|Response
     */
    public function index()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $user = $this->getUser();
        if($user) {
            return new JsonResponse($serializer->serialize($user->getUsername(), 'json'), Response::HTTP_OK, [], true);
        }
        return new JsonResponse($serializer->serialize("Bienvenue sur BlablaMovie, vous etes deconnecte(e)", 'json'), Response::HTTP_OK, [], true);
    }
}