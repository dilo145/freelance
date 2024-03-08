<?php
namespace App\Service;

use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;

class AnswerService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $student = $this->entityManager->getRepository(Student::class)->findOneById($data["student_id"]);
        
        if ($student == null) {
            throw new NotFoundHttpException('Error while creating answer: Question not found');
        }

        $answer = new Answer();
        $answer->setAnswer($data['answer']);
        $answer->setStudent($student);

        try {
            $this->entityManager->persist($answer);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to create the answer'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Answer created successfully'], Response::HTTP_CREATED);
    }

    public function readAll(): Response
    {
        $answers = $this->entityManager->getRepository(Answer::class)->findAll();
        $data = [];

        foreach ($answers as $answer) {
            $data[] = [
                'id' => $answer->getId(),
                'answer' => $answer->getAnswer(),
                'student' => $answer->getStudent()->getFirstName()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function read(int $id): Response
    {
        $answer = $this->entityManager->getRepository(Answer::class)->find($id);

        if ($answer == null) {
            throw new NotFoundHttpException('Error while reading answer: Answer not found');
        }

        $data = [
            'id' => $answer->getId(),
            'answer' => $answer->getAnswer(),
            'student' => $answer->getStudent()->getFirstName()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function update(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $answer = $this->entityManager->getRepository(Answer::class)->find($id);

        if ($answer == null) {
            throw new NotFoundHttpException('Error while updating answer: Answer not found');
        }

        if(isset($data['answer'])) {
            $answer->setAnswer($data['answer']);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the answer'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Answer updated successfully'], Response::HTTP_OK);
    }


    public function delete(int $id): Response
    {
        $answer = $this->entityManager->getRepository(Answer::class)->find($id);

        if ($answer == null) {
            throw new NotFoundHttpException('Error while deleting answer: Answer not found');
        }

        try {
            $this->entityManager->remove($answer);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the answer'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Answer deleted successfully'], Response::HTTP_OK);
    }

}