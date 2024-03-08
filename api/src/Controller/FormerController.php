<?php

namespace App\Controller;

use App\Service\FormerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/formers')]
class FormerController extends AbstractController
{

    private $formerService;

    public function __construct(FormerService $formerService)
    {
        $this->formerService = $formerService;
    }

    #[Route('/new', name: 'api_former_new', methods: ['POST'])]
    public function newFormer(Request $request): JsonResponse
    {
        return $this->formerService->newFormer($request);
    }

    #[Route('/{id}', name: 'app_former_get_one', methods: ['GET'])]
    public function getOneFormer(int $id): JsonResponse
    {
        return $this->formerService->getOneFormer($id);
    }
    
    #[Route('/', name: 'app_former_get_all', methods: ['GET'])]
    public function getAllFormers(): JsonResponse
    {
        return $this->formerService->getAllFormers();
    }
    
    #[Route('/edit/{id}', name: 'api_former_edit', methods: ['PUT'])]
    public function editFormer(Request $request, int $id): JsonResponse
    {
        return $this->formerService->editFormer($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_former_delete', methods: ['DELETE'])]
    public function deleteFormer(Request $request, int $id): JsonResponse
    {
        return $this->formerService->deleteFormer($id);
    }
}
