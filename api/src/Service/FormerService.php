<?php

namespace App\Service;

use App\Entity\Former;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FormerService
{
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function newFormer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $former = new Former();

        if (!isset($data['firstName']) || !isset($data['lastName']) || !isset($data['email'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if (!preg_match("/^\S+@\S+\.\S+$/", $data['email'])) {
            return new JsonResponse(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
        }

        $former->setFirstName($data['firstName']);
        $former->setLastName($data['lastName']);
        $former->setEmail($data['email']);
        $former->setCreatedAt();
        $former->setPhoto($data['photo'] != null ? $data['photo'] : "https://randomuser.me/api/portraits/men/25.jpg");
        $former->setRoles(['ROLE_FORMER']);
        //hash password
        $former->setPassword(
            $this->userPasswordHasher->hashPassword(
                $former,
                $data['password']
            )
        );


        try {
            $this->entityManager->persist($former);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to save the former'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Former created successfully'], Response::HTTP_CREATED);
    }

    public function getOneFormer(int $id): JsonResponse
    {
        $former = $this->entityManager->getRepository(Former::class)->findOneById($id);

        if (!$former) {
            return new JsonResponse(['error' => 'Former not found'], Response::HTTP_NOT_FOUND);
        }

        $formerData = [
            'id' => $former->getId(),
            'firstName' => $former->getFirstName(),
            'lastName' => $former->getLastName(),
            'email' => $former->getEmail(),
            'photo' => $former->getPhoto(),
            'createdAt' => $former->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $former->getUpdatedAt() ? $former->getUpdatedAt()->format('Y-m-d H:i:s') : null,
            'deletedAt' => $former->getDeletedAt() ? $former->getDeletedAt()->format('Y-m-d H:i:s') : null,
        ];


        return new JsonResponse($formerData, Response::HTTP_OK);
    }

    public function getAllFormers(): JsonResponse
    {
        $formers = $this->entityManager->getRepository(Former::class)->findAll();

        $formersData = [];

        foreach ($formers as $former) {
            $formerData[] = [
                'id' => $former->getId(),
                'firstName' => $former->getFirstName(),
                'lastName' => $former->getLastName(),
                'email' => $former->getEmail(),
                'photo' => $former->getPhoto(),
                'createdAt' => $former->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $former->getUpdatedAt() ? $former->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'deletedAt' => $former->getDeletedAt() ? $former->getDeletedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return new JsonResponse($formersData, Response::HTTP_OK);
    }

    public function editFormer(Request $request, int $id): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $former = $this->entityManager->getRepository(Former::class)->find($id);

        if (!$former) {
            return new JsonResponse(['error' => 'Former not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['firstName'])) {
            $former->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $former->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            if (!preg_match("/^\S+@\S+\.\S+$/", $data['email'])) {
                return new JsonResponse(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
            }
            if ($data['email'] != $former->getEmail() && $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
                return new JsonResponse(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
            }
            $former->setEmail($data['email']);
        }
        if (isset($data['photo'])) {
        $former->setPhoto($data['photo'] ?? "https://randomuser.me/api/portraits/men/25.jpg");
        }
        if (isset($data['individual'])) {
            $former->setIndividual($data['individual']);
        }
        if (isset($data['password'])) {
            $former->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $former,
                    $data['password']
                )
            );
        }

        $former->setUpdatedAt();

        try {
            $this->entityManager->persist($former);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to edit the former'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Former edited successfully'], Response::HTTP_OK);
    }

    public function deleteFormer(int $id): JsonResponse
    {
        $former = $this->entityManager->getRepository(Former::class)->find($id);

        if (!$former) {
            return new JsonResponse(['error' => 'Former not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            // $this->entityManager->remove($former);
            $former->setDeletedAt();
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the former'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Former deleted successfully'], Response::HTTP_OK);
    }
}
