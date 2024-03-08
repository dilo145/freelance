<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categories;
use App\Entity\Lesson;

class CategoryService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];

        if (empty($name)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $category = new Categories();
        $category->setName($name);
        $category->setDescription($name);
        if (isset($data['lessonId'])) {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($data['lessonId']);
            if ($lesson == null) {
                throw new NotFoundHttpException('Lesson not found');
            }
            $category->setLesson($lesson);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Category created!'], Response::HTTP_CREATED);
    }

    public function read(int $id): Response
    {
        $category = $this->entityManager->getRepository(Categories::class)->find($id);

        if ($category == null) {
            throw new NotFoundHttpException('Category not found');
        }

        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function readAll(): Response
    {
        $categories = $this->entityManager->getRepository(Categories::class)->findAll();

        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function update(Request $request, int $id): Response
    {
        $category = $this->entityManager->getRepository(Categories::class)->find($id);

        if ($category == null) {
            throw new NotFoundHttpException('Category not found');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $category->setName($data['name']);
        }

        if (isset($data['description'])) {
            $category->setDescription($data['description']);
        }
        
        if (isset($data['lessonId'])) {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($data['lessonId']);
            if ($lesson == null) {
                throw new NotFoundHttpException('Lesson not found');
            }
            $category->setLesson($lesson);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to update the category'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Category updated successfully'], Response::HTTP_ACCEPTED);
    }

    public function delete(int $id): Response
    {
        $category = $this->entityManager->getRepository(Categories::class)->find($id);

        if ($category == null) {
            throw new NotFoundHttpException('Category not found');
        }

        try {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the category'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Category deleted successfully'], Response::HTTP_OK);
    }
}