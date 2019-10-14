<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function addUser(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository)
    {
        $user = new User();

        //Here, without Symfony's Form (see HOC2019_GIFTS-LB for Symfony Form Use)
        $login = $request->request->get('login');
        $user->setLogin($login);

        $password = $request->request->get('password');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $mail = $request->request->get('mail');

        $userSearchResults = $userRepository->findByMail($mail);

        if (!$userSearchResults) {
            $user->setMail($mail);
        }

        $roles = $user->getRoles();
        $user->setRoles($roles);

        $birthDate = new DateTime($request->request->get('birth_date'));
        $user->setBirthDate($birthDate);

        $user->setInscriptionDate(new DateTime());

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return $errorsString;
        }

        /* you can fetch the EntityManager via $this->getDoctrine()->getManager() or you can add an argument to the action: addUser(EntityManagerInterface $entityManager)
        */
        // tell Doctrine you want to (eventually) save the user (no queries yet)
        $entityManager->persist($user);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $user;
    }

    public function deleteUser(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        VoteRepository $voteRepository,
        $user)
    {
        $userId = $user->$request->request->get('id');
        //First delete all the votes of the current user
        $votesSearchResults = $voteRepository->findByUser($userId);

        foreach ($votesSearchResults as $votesSearchItem) {

        }


        //Then delete the user in the DBB


        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return $errorsString;
        }

        /* you can fetch the EntityManager via $this->getDoctrine()->getManager() or you can add an argument to the action: addUser(EntityManagerInterface $entityManager)
        */
        // tell Doctrine you want to (eventually) save the user (no queries yet)
        $entityManager->persist($user);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $user;
    }
}
