<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\Service\Vote\VoteService;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    private $serializer;
    private $entityManager;
    private $userRepository;
    private $userService;

    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param UserService $userService
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
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
     * To test the function with Postman, you need to set a mail and a password keys in the body parameters (form-data)
     *
     * @Rest\Post("/user", name="add_user")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function createNewUser(
        Request $request,
        ValidatorInterface $validator)
    {
        $user = $this->userService->addUser($request, $validator, $this->entityManager, $this->userRepository);

        return new JsonResponse(
            $this->serializer->serialize(
                $user,
                'json'
            ),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * Http verb DELETE don't need to return a JsonResponse !
     *
     * @Rest\Delete("/user", name="delete_user")
     * @param VoteService $voteService
     * @param SecurityController $securityController
     * @return void
     */
    public function deleteCurrentUser(VoteService $voteService, SecurityController $securityController)
    {
        $user = $this->getUser();
        $deletingResult = $this->userService->deleteUser($this->entityManager, $this->userRepository, $voteService, $user);

        return $securityController->logout();
    }
}