<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @Rest\Post("/user", name="add_user")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function createNewUser(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository)
    {
        $userService = new UserService($passwordEncoder);
        $user = $userService->addUser($request, $validator, $entityManager, $userRepository);

        return new JsonResponse(
            $this->serializer->serialize(
                $user,
                'json',
                [
                    'groups' => [
                        User::GROUP_SELF,
                    ]
                ]
            ),
            Response::HTTP_CREATED,
            [],
            true
        );
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
     * @Rest\Delete("/user", name="delete_user")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function deleteCurrentUser(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    )
    {

        $userService = new UserService($passwordEncoder);
        $user = $userService->deleteUser($request, $validator, $entityManager, $userRepository);

        return new JsonResponse(
            $this->serializer->serialize(
                $user,
                'json',
                [
                    'groups' => [
                        User::GROUP_SELF,
                    ]
                ]
            ),
            Response::HTTP_CREATED,
            [],
            true
        );

    }
}