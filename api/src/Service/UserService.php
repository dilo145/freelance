<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserService
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // TODO Uncomment when the admin role will be added
    // public function getUser($userId)
    // {
    //     $user = $this->entityManager->getRepository(User::class)->find($userId);

    //     if (!$user) {
    //         return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     $userData = [
    //         'id' => $user->getId(),
    //         'firstName' => $user->getFirstName(),
    //         'lastName' => $user->getLastName(),
    //         'email' => $user->getEmail(),
    //         'photo' => $user->getPhoto(),
    //         'userIdentifier' => $user->getUserIdentifier(),
    //         'messagesSended' => $user->getMessagesSended(),
    //         'messagesRecived' => $user->getMessagesRecived(),
    //         'roles' => $user->getRoles(),
    //         'createdAt' => $user->getCreatedAt(),
    //         'updatedAt' => $user->getUpdatedAt(),
    //         'deletedAt' => $user->getDeletedAt()
    //     ];

    //     return new JsonResponse($userData, Response::HTTP_OK);
    // }

    // TODO Uncomment when the admin role will be added
    // public function getAllUsers()
    // {
    //     $users = $this->entityManager->getRepository(User::class)->findAll();

    //     $usersData = [];

    //     foreach ($users as $user) {
    //         $usersData[] = [
    //             'id' => $user->getId(),
    //             'firstName' => $user->getFirstName(),
    //             'lastName' => $user->getLastName(),
    //             'email' => $user->getEmail(),
    //             'photo' => $user->getPhoto(),
    //             'userIdentifier' => $user->getUserIdentifier(),
    //             'messagesSended' => $user->getMessagesSended(),
    //             'messagesRecived' => $user->getMessagesRecived(),
    //             'roles' => $user->getRoles(),
    //             'createdAt' => $user->getCreatedAt(),
    //             'updatedAt' => $user->getUpdatedAt(),
    //             'deletedAt' => $user->getDeletedAt()
    //         ];
    //     }

    //     return new JsonResponse($usersData, Response::HTTP_OK);
    // }

    // TODO Uncomment when the admin role will be added
    // public function updateUser(Request $request, int $id)
    // {
    //     $user = $this->entityManager->getRepository(User::class)->find($id);

    //     if (!$user) {
    //         return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     $data = json_decode($request->getContent(), true);

    //     if (isset($data['firstName'])) {
    //         $user->setFirstName($data['firstName']);
    //     }
    //     if (isset($data['firstName'])) {
    //         $user->setLastName($data['lastName']);
    //     }
    //     if (isset($data['firstName'])) {
    //         $user->setEmail($data['email']);
    //     }
    //     if (isset($data['firstName'])) {
    //         $user->setPhoto($data['photo']);
    //     }
    //     if (isset($data['firstName'])) {
    //         $user->setUserIdentifier($data['userIdentifier']);
    //     }
    //     if (isset($data['firstName'])) {
    //         $user->setRoles($data['roles']);
    //     }

    //     $user->setUpdatedAt();

    //     try {
    //         $this->entityManager->flush();
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['error' => 'Failed to update user'], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     return new JsonResponse(['message' => 'User updated successfully'], Response::HTTP_OK);
    // }

    // TODO Uncomment when the admin role will be added
    // public function deleteUser($id)
    // {
    //     $user = $this->entityManager->getRepository(User::class)->find($id);

    //     if (!$user) {
    //         return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     $user->setDeletedAt();

    //     try {
    //         $this->entityManager->flush();
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['error' => 'Failed to delete user'], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     return new JsonResponse(['message' => 'User deleted successfully'], Response::HTTP_OK);
    // }
}