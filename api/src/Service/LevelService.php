<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Level;
use App\Entity\Lesson;

class LevelService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['description'])) {
            return new JsonResponse(['error' => 'Name and description are required'], Response::HTTP_BAD_REQUEST);
        }

        $level = new Level();
        $level->setName($data['name']);
        $level->setDescription($data['description']);

        try {
            $this->entityManager->persist($level);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to create the level'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Level created successfully'], Response::HTTP_CREATED);
    }

    public function read(int $id): Response
    {
        $level = $this->entityManager->getRepository(Level::class)->find($id);

        if ($level == null) {
            throw new NotFoundHttpException('Level not found');
        }

        $data = [
            'id' => $level->getId(),
            'name' => $level->getName(),
            'description' => $level->getDescription()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function readAll(): Response
    {
        $levels = $this->entityManager->getRepository(Level::class)->findAll();

        $data = [];

        foreach ($levels as $level) {
            $data[] = [
                'id' => $level->getId(),
                'name' => $level->getName(),
                'description' => $level->getDescription()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function update(Request $request, int $id): Response
    {
        $level = $this->entityManager->getRepository(Level::class)->find($id);

        if ($level == null) {
            throw new NotFoundHttpException('Level not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $level->setName($data['name']);
        }

        if (isset($data['description'])) {
            $level->setDescription($data['description']);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the level'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Level updated successfully'], Response::HTTP_ACCEPTED);
    }

    public function delete(int $id): Response
    {
        $level = $this->entityManager->getRepository(Level::class)->find($id);

        if ($level == null) {
            throw new NotFoundHttpException('Level not found');
        }

        $lesson_that_uses_level = $this->entityManager->getRepository(Lesson::class)->findOneBy(['level' => $level]);

        if ($lesson_that_uses_level != null) {
            $lesson_that_uses_level->setLevel(null);
        }

        try {
            $this->entityManager->remove($level);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the level'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Level deleted successfully'], Response::HTTP_OK);
    }

}