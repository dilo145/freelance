<?php
namespace App\Service;

use App\Entity\Categories;
use App\Entity\Lesson;
use App\Entity\Level;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class LessonService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createLesson(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || !isset($data['description']) || !isset($data['place']) || !isset($data['goal']) || !isset($data['level']['id'])) {
            return new JsonResponse(['error' => 'All fields are required'], Response::HTTP_BAD_REQUEST);
        }

        $level = $this->entityManager->getRepository(Level::class)->find($data['level']['id']);

        if ($level == null) {
            return new JsonResponse(['error' => 'Level not found'], Response::HTTP_NOT_FOUND);
        }

        $lesson = new Lesson();
        $lesson->setTitle($data['title']);
        $lesson->setDescription($data['description']);
        $lesson->setPlace($data['place']);
        $lesson->setGoal($data['goal']);
        $lesson->setLevel($level);
        if (isset($data['category'])) {
            foreach ($data['category'] as $categoryId) {
                $category = $this->entityManager->getRepository(Categories::class)->findOneById($categoryId);
                if ($category == null) {
                    throw new NotFoundHttpException('Category not found');
                }
                $lesson->addCategory($category);
            }
        }

        try {
            $this->entityManager->persist($lesson);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to create the lesson'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Lesson created successfully'], Response::HTTP_CREATED);
    }

    public function getOneLessons(int $id): JsonResponse
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if ($lesson == null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        $lessonData = [
            'id' => $lesson->getId(),
            'title' => $lesson->getTitle(),
            'description' => $lesson->getDescription(),
            'place' => $lesson->getPlace(),
            'goal' => $lesson->getGoal(),
            'level' => $lesson->getLevel()->getName(),
            'category' => $lesson->getCategory()
        ];

        return new JsonResponse($lessonData, Response::HTTP_OK);
    }

    public function getAllLessons(): JsonResponse
    {

        $lessons = $this->entityManager->getRepository(Lesson::class)->findAll();

        if ($lessons == null) {
            throw new NotFoundHttpException('No lessons found');
        }

        $lessonsData = [];

        foreach ($lessons as $lesson) {
            $lessonsData[] = [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'description' => $lesson->getDescription(),
                'place' => $lesson->getPlace(),
                'goal' => $lesson->getGoal(),
                'level' => $lesson->getLevel()->getName(),
                'category' => $lesson->getCategory()
            ];
        }

        return new JsonResponse($lessonsData, Response::HTTP_OK);
    }

    public function updateLesson(Request $request, int $id): JsonResponse 
    {
     
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if ($lesson == null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $lesson->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $lesson->setDescription($data['description']);
        }

        if (isset($data['place'])) {
            $lesson->setPlace($data['place']);
        }

        if (isset($data['goal'])) {
            $lesson->setGoal($data['goal']);
        }

        if (isset($data['levelId'])) {
            $level = $this->entityManager->getRepository(Level::class)->find($data['levelId']);
            if ($level == null) {
                return new JsonResponse(['error' => 'Level not found'], Response::HTTP_NOT_FOUND);
            }
            $lesson->setLevel($level);
        }

        if (isset($data['categories'])) {
            $categories = $lesson->getCategory();
            foreach ($categories as $category) {
                $lesson->removeCategory($category);
            }
            foreach ($data['categories'] as $categoryId) {
                $category = $this->entityManager->getRepository(Categories::class)->findOneById($categoryId);
                if ($category == null) {
                    throw new NotFoundHttpException('Category not found');
                }
                $lesson->addCategory($category);
            }
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the lesson'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Lesson updated successfully'], Response::HTTP_ACCEPTED);

    }

    public function deleteLesson(int $id): JsonResponse
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

        if ($lesson == null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        try {
            $this->entityManager->remove($lesson);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the lesson'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Lesson deleted successfully'], Response::HTTP_OK);
    }

}