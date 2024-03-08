<?php
namespace App\Service;

use App\Entity\Training;
use App\Entity\Organism;
use App\Entity\Former;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class TrainingService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $training = new Training();

        if (!isset($data['name']) || !isset($data['goalTraining'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $training->setName($data['name']);
        $training->setGoalTraining($data['goalTraining']);
        if (isset($data['organismId'])) {
            $organism = $this->entityManager->getRepository(Organism::class)->find($data['organismId']);
            if($organism) {
                $training->setOrganism($organism);
            } else {
                return new JsonResponse(['error' => 'Organism not found'], Response::HTTP_NOT_FOUND);
            }
        }

        try {
            $this->entityManager->persist($training);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to save the training'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Training created successfully'], Response::HTTP_CREATED);
    }

    public function read(int $id): Response
    {
        $training = $this->entityManager->getRepository(Training::class)->find($id);

        if ($training == null) {
            throw new NotFoundHttpException('Level not found');
        }

        $trainingData = [
            'id' => $training->getId(),
            'name' => $training->getName(),
            'goalTraining' => $training->getGoalTraining(),
            'organism' => $training->getOrganism(),
            'registrations' => $training->getRegistrations(),
            'lesson' => $training->getLesson(),
            'formers' => $training->getFormers()
        ];

        return new JsonResponse($trainingData, Response::HTTP_OK);
    }

    public function getAllByFormer(int $formerId): Response
    {
        $former = $this->entityManager->getRepository(Former::class)->find($formerId);
        if (!$former) {
            throw new NotFoundHttpException('Former not found');
        }

        $trainings = $former->getTrainings();

        $trainingsData = [];

        foreach ($trainings as $training) {
            $trainingsData[] = [
                'id' => $training->getId(),
                'name' => $training->getName(),
                'goalTraining' => $training->getGoalTraining(),
                'organism' => $training->getOrganism(),
                'registrations' => $training->getRegistrations(),
                'lesson' => $training->getLesson(),
                'formers' => $training->getFormers()
            ];
        }

        return new JsonResponse($trainingsData, Response::HTTP_OK);
    }

    public function getAll(): Response
    {
        $trainings = $this->entityManager->getRepository(Training::class)->findAll();
        $trainingsData = [];

        foreach ($trainings as $training) {
            $trainingsData[] = [
                'id' => $training->getId(),
                'name' => $training->getName(),
                'goalTraining' => $training->getGoalTraining(),
                'organism' => $training->getOrganism(),
                'registrations' => $training->getRegistrations(),
                'lesson' => $training->getLesson(),
                'formers' => $training->getFormers()
            ];
        }

        return new JsonResponse($trainingsData, Response::HTTP_OK);
    }

    public function update(Request $request, int $id): Response
    {
        $training = $this->entityManager->getRepository(Training::class)->find($id);

        if ($training == null) {
            throw new NotFoundHttpException('Training not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $training->setName($data['name']);
        }

        if (isset($data['goalTraining'])) {
            $training->setGoalTraining($data['goalTraining']);
        }

        if (isset($data['organismId'])) {
            $organism = $this->entityManager->getRepository(Organism::class)->find($data['organismId']);
            if($organism) {
                $training->setOrganism($organism);
            }
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the training'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Training updated successfully'], Response::HTTP_ACCEPTED);
    }

    public function delete(int $id): Response
    {
        $training = $this->entityManager->getRepository(Training::class)->find($id);

        if ($training == null) {
            throw new NotFoundHttpException('Training not found');
        }

        try {
            $this->entityManager->remove($training);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the training'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Training deleted successfully'], Response::HTTP_OK);
    }
}