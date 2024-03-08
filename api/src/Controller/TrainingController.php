<?php

namespace App\Controller;

use App\Service\TrainingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/trainings')]
class TrainingController extends AbstractController
{

    private $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    #[Route('/new', name: 'api_training_new', methods: ['POST'])]
    public function newTraining(Request $request): JsonResponse
    {
        return $this->trainingService->create($request);
    }

    #[Route('/{id}', name: 'api_training_get_one', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        return $this->trainingService->read($id);
    }

    // TODO: Use in front-end
    #[Route('/getAllbyFormer/{formerId}', name: 'app_training_index', methods: ['GET'])]
    public function getAllByFormer(int $formerId): JsonResponse
    {
        return $this->trainingService->getAllByFormer($formerId);
    }

    #[Route('/', name: 'app_training_index', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        return $this->trainingService->getAll();
    }

    #[Route('/edit/{id}', name: 'api_training_edit', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->trainingService->update($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_training_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return $this->trainingService->delete($id);
    }
}
