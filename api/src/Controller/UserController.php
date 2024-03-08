<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/users')]
class UserController extends AbstractController
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    // TODO Uncomment when the admin role will be added
    // #[Route('/', name: 'app_user_index', methods: ['GET'])]
    // public function showAll(): JsonResponse
    // {
    //     return $this->userService->getAllUsers();
    // }

    // TODO Uncomment when the admin role will be added
    // #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    // public function show(int $id): JsonResponse
    // {
    //     return $this->userService->getUser($id);
    // }

    // TODO Uncomment when the admin role will be added
    // #[Route('update/{id}', name: 'app_user_update', methods: ['PUT'])]
    // public function update(Request $request, int $id): JsonResponse
    // {
    //     return $this->userService->updateUser($request, $id);
    // }


    // TODO Uncomment when the admin role will be added
    // #[Route('/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    // public function delete(int $id): JsonResponse
    // {
    //     return $this->userService->deleteUser($id);
    // }
}
