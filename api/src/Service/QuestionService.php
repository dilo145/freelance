<?php
namespace App\Service;

use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Exam;
use App\Entity\Question;

class QuestionService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $exam = $this->entityManager->getRepository(Exam::class)->findOneById($data["exam_id"]);

        
        if ($exam == null) {
            throw new NotFoundHttpException('Error while creating question: exam not found');
        }

        $question = new Question();
        $question->setQuestion($data['question']);
        $question->setAnswer($data['answer']);
        $question->setExam($exam);
        if(isset($data['answer_question_id'])) {
            $answerQuestion = $this->entityManager->getRepository(Answer::class)->findOneById($data["answer_question_id"]);
            $question->setAnswerQuestion($answerQuestion);
        }

        try {
            $this->entityManager->persist($question);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to save the question'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Question created successfully'], Response::HTTP_CREATED);
    }

    public function readAll(): Response
    {
        $questions = $this->entityManager->getRepository(Question::class)->findAll();

        $data = [];
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'question' => $question->getQuestion(),
                'answer' => $question->getAnswer(),
                'exam_id' => $question->getExam()->getId(),
                'answer_question_id' => $question->getAnswerQuestion() ? $question->getAnswerQuestion()->getId() : null
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function read(int $id): Response
    {
        $question = $this->entityManager->getRepository(Question::class)->findOneById($id);

        if ($question == null) {
            throw new NotFoundHttpException('Error while reading question: question not found');
        }

        $data = [
            'id' => $question->getId(),
            'question' => $question->getQuestion(),
            'answer' => $question->getAnswer(),
            'exam_id' => $question->getExam()->getId(),
            'answer_question_id' => $question->getAnswerQuestion() ? $question->getAnswerQuestion()->getId() : null
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function update(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $question = $this->entityManager->getRepository(Question::class)->findOneById($id);

        if ($question == null) {
            throw new NotFoundHttpException('Error while updating question: question not found');
        }

        if (isset($data['exam_id'])) {
            $exam = $this->entityManager->getRepository(Exam::class)->findOneById($data["exam_id"]);
            if ($exam == null) {
                throw new NotFoundHttpException('Error while updating question: exam not found');
            }
            $question->setExam($exam);
        }
        
        if (isset($data['answer_question_id'])) {
            $answerQuestion = $this->entityManager->getRepository(Answer::class)->findOneById($data["answer_question_id"]);
            if ($answerQuestion == null) {
                throw new NotFoundHttpException('Error while updating question: answer not found');
            }
            $question->setAnswerQuestion($answerQuestion);
        }

        if (isset($data['question'])) {
            $question->setQuestion($data['question']);
        }

        if (isset($data['answer'])) {
            $question->setAnswer($data['answer']);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the question'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Question updated successfully'], Response::HTTP_OK);
    }

    public function delete(int $id): Response
    {
        $question = $this->entityManager->getRepository(Question::class)->findOneById($id);

        if ($question == null) {
            throw new NotFoundHttpException('Error while deleting question: question not found');
        }

        try {
            $this->entityManager->remove($question);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the question'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Question deleted successfully'], Response::HTTP_OK);
    }
}
?>