<?php

namespace App\Controller;

use App\Service\StudentService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/students')]
class StudentController extends AbstractController
{
    private $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }
    
    #[Route('/export/{id}', name:'export_csv', methods: ['GET'])]
    public function exportStudents(UserRepository $UserRepository, int $id): JsonResponse
    {
        // Fetch data from your database or any source
        $students = $UserRepository->findByuser($id);

        // Generate CSV data
        $csvData = "ID,FirstName,LastName,Email\n"; // CSV header
        foreach ($students as $student) {
            $csvData .= "{$student['id']},{$student['firstName']},{$student['lastName']},{$student['email']}\n";
        }

        // Return CSV data as response
        $response = new Response($csvData);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="students.csv"');

        return $response;
    }
      
    #[Route('/new', name: 'api_student_new', methods: ['POST'])]
    public function newStudent(Request $request): JsonResponse
    {
        return $this->studentService->newStudent($request);
    }
  
    #[Route('/new/import', name: 'api_student_import', methods: ['POST'])]
    public function importStudents(Request $request): JsonResponse
    {
        $csvData = $request->request->get('csvData');

        if ($csvData) {
            $lines = explode("\n", $csvData);

            array_shift($lines);

            $students = [];
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $lineData = explode(",", $line);
                    $student = [
                        'firstName' => $lineData[0],
                        'lastName' => $lineData[1],
                        'email' => $lineData[2],
                        'invidual' => $lineData[3]
                    ];
                    $students[] = $student;
                }
            }
            return $this->studentService->createStudents($students);
        } else {
            return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/', name: 'api_student_get_all', methods: ['GET'])]
    public function getAllStudents(): JsonResponse
    {
        return $this->studentService->getAllStudents();
    }

    #[Route('/{id}', name: 'api_student_get_one', methods: ['GET'])]
    public function getOneStudent(int $id): JsonResponse
    {
        return $this->studentService->getOneStudent($id);
    }

    #[Route('/getByTraining/{trainingId}', name: 'api_student_get_all_by_training', methods: ['GET'])]
    public function getAllStudentsByTraining(int $trainingId): JsonResponse
    {
        return $this->studentService->getAllStudentsByTraining($trainingId);
    }

    #[Route('/edit/{id}', name: 'api_student_edit', methods: ['PUT'])]
    public function editStudent(Request $request, int $id): JsonResponse
    {
        return $this->studentService->editStudent($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_student_delete', methods: ['DELETE'])]
    public function deleteStudent(int $id): JsonResponse
    {
        return $this->studentService->deleteStudent($id);
    }
}
