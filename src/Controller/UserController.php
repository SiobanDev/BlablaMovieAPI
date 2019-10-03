<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\AddUserService;
use App\Service\User\CustomUserSerializer;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    private $serializer;

    /**
     * UserController constructor.
     * @param $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Using Symfony's Form
     * Create a form in order to create a new User
     * @param $user
     * @return FormInterface
     */
    public function createFormNewUser($user)
    {
        $form = $this->createForm(Usertype::class, $user);
        return $form;
    }

    /**
     * @Rest\Post("/users/add")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function createNewUser(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $serializer = new CustomUserSerializer();
        $userService = new AddUserService($passwordEncoder);
        $user = $userService->addUser($request, $validator, $entityManager);

        return new JsonResponse($serializer->customUserSerializer()->serialize($user, 'json'), Response::HTTP_CREATED, [], true);
    }

//    /**
//     * @Rest\Get("/users", name="users_list")
//     * @param UserRepository $userRepository
//     * @return JsonResponse
//     *
//     */
//    public function getAllUsers(UserRepository $userRepository)
//    {
//        $allUsersArray = $userRepository->findAll();
//        return new JsonResponse($this->serializer->serialize($allUsersArray, 'json'));
//    }

    /**
     * @Rest\Post("/votation", name="votation")
     */
    public function votation()
    {
        //add an optional message - seen by developers
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without having ROLE_USER');

    }
}