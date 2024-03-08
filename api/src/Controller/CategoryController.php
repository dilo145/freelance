<?php

namespace App\Controller;

use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{

    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[Route('/new', name: 'api_category_new', methods: ['POST'])]
    public function newCategory(Request $request)
    {
        return $this->categoryService->create($request);
    }

    #[Route('/{id}', name: 'api_category_read', methods: ['GET'])]
    public function readCategory(int $id): Response
    {
        return $this->categoryService->read($id);
    }

    #[Route('/', name: 'api_category_read_all', methods: ['GET'])]
    public function readAllLevels(): Response
    {
        return $this->categoryService->readAll();
    }
    
    #[Route('/edit/{id}', name: 'api_category_edit', methods: ['PUT'])]
    public function updateCategory(Request $request, int $id): Response
    {
        return $this->categoryService->update($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_category_delete', methods: ['DELETE'])]
    public function deleteLevel(int $id): Response
    {
        return $this->categoryService->delete($id);
    }
}
