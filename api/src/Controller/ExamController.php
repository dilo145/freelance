<?php
namespace App\Controller;

use App\Service\ExamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


#[Route('/api/exams')]
class ExamController extends AbstractController
{
    private $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    #[Route('/new', name: 'api_exam_new', methods: ['POST'])]
    public function newExam(Request $request): Response
    {
        return $this->examService->create($request);
    }

    #[Route('/{id}', name: 'api_exam_read', methods: ['GET'])]
    public function readExam(int $id): Response
    {
        return $this->examService->read($id);
    }

    #[Route('/', name: 'api_exam_read_all', methods: ['GET'])]
    public function readAllExams(): Response
    {
        return $this->examService->readAll();
    }

    #[Route('/edit/{id}', name: 'api_exam_edit', methods: ['PATCH'])]
    public function updateExam(Request $request, int $id): Response
    {
        return $this->examService->update($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_exam_delete', methods: ['DELETE'])]
    public function deleteExam(int $id): Response
    {
        return $this->examService->delete($id);
    }
}
?>