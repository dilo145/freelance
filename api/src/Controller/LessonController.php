<?php

namespace App\Controller;

use App\Service\LessonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/lessons')]
class LessonController extends AbstractController
{
    private $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    #[Route('/new', name: 'api_lesson_new', methods: ['POST'])]
    public function createLesson(Request $request): JsonResponse
    {
        return $this->lessonService->createLesson($request);
    }

    #[Route('/', name: 'api_lesson_get_all', methods: ['GET'])]
    public function getAllLessons(): JsonResponse
    {
        return $this->lessonService->getAllLessons();
    }

    #[Route('/{id}', name: 'api_lesson_one_all', methods: ['GET'])]
    public function getOneLessons(int $id): JsonResponse
    {
        return $this->lessonService->getOneLessons($id);
    }

    #[Route('/edit/{id}', name: 'api_lesson_update', methods: ['PUT'])]
    public function updateLesson(Request $request, int $id): JsonResponse
    {
        return $this->lessonService->updateLesson($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_lesson_delete', methods: ['DELETE'])]
    public function deleteLesson(int $id): JsonResponse
    {
        return $this->lessonService->deleteLesson($id);
    }
}
