<?php

namespace App\Service;

use App\Entity\Organism;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Training;

class OrganismService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->entityManager->getRepository(User::class)->find($data['created_by']);
        if ($user == null) {
            throw new NotFoundHttpException('Error while creating organism: User not found');
        }

        $organism = new Organism();
        $organism->setName($data['name']);
        $organism->setLogo($data['logo']);
        $organism->setCreatedBy($data['created_by']);

        try {
            $this->entityManager->persist($organism);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to save the organism'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Organism created successfully'], Response::HTTP_CREATED);
    }

    public function readByCreator(int $id): Response
    {
        try {
            $organisms = $this->entityManager->getRepository(Organism::class)->findBy(['createdBy' => $id]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to read the organism'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $organismsData = [];

        foreach ($organisms as $organism) {
            $data[] = [
                'id' => $organism->getId(),
                'name' => $organism->getName(),
                'logo' => $organism->getLogo(),
                'created_by' => $organism->getCreatedBy()
            ];
        }
        return new JsonResponse($organismsData, Response::HTTP_OK);
    }

    public function read(int $id): Response
    {
        $organism = $this->entityManager->getRepository(Organism::class)->find($id);
        $trainings = $this->entityManager->getRepository(Training::class)->findBy(['organism' => $organism]);

        if ($organism == null) {
            throw new NotFoundHttpException('Organism not found');
        }

        $trainingsData = [];
        foreach ($trainings as $training) {
            $trainingsData[] = [
                'id' => $training->getId(),
                'name' => $training->getName(),
                'goal' => $training->getGoalTraining(),
                'organism' => [
                    'id' => $training->getOrganism()->getId(),
                    'name' => $training->getOrganism()->getName(),
                    'logo' => $training->getOrganism()->getLogo(),
                    'created_by' => $training->getOrganism()->getCreatedBy()
                ]
            ];
        }

        $data = [
            'id' => $organism->getId(),
            'name' => $organism->getName(),
            'logo' => $organism->getLogo(),
            'created_by' => $organism->getCreatedBy(),
            'trainings' => $trainingsData
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function readAll(): Response
    {
        $organisms = $this->entityManager->getRepository(Organism::class)->findAll();

        $data = [];
        foreach ($organisms as $organism) {
            $data[] = [
                'id' => $organism->getId(),
                'name' => $organism->getName(),
                'logo' => $organism->getLogo(),
                'created_by' => $organism->getCreatedBy()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function update(Request $request, int $id): Response
    {
        try {
            $organism = $this->entityManager->getRepository(Organism::class)->find($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the organism'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $trainings = $this->entityManager->getRepository(Training::class)->findBy(['organism' => $organism]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the organism'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($organism == null) {
            throw new NotFoundHttpException('Organism not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $organism->setName($data['name']);
        }

        if (isset($data['logo'])) {
            $organism->setLogo($data['logo']);
        }

        if (isset($data['created_by'])) {
            $organism->setCreatedBy($data['created_by']);
        }

        if (isset($data['trainings'])) {
            foreach ($trainings as $training) {
                $organism->removeTraining($training);
            }

            foreach ($data['trainings'] as $trainingId) {
                $training = $this->entityManager->getRepository(Training::class)->findOneById($trainingId);
                if ($training == null) {
                    throw new NotFoundHttpException('Training not found');
                }
                $organism->addTraining($training);
            }
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the organism'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Organism updated successfully'], Response::HTTP_OK);
    }

    public function delete(int $id): Response
    {
        $organism = $this->entityManager->getRepository(Organism::class)->find($id);

        if ($organism == null) {
            throw new NotFoundHttpException('Organism not found');
        }

        try {
            $this->entityManager->remove($organism);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the organism'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Organism deleted successfully'], Response::HTTP_OK);
    }
}
