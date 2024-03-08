<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\OrganismService;

#[Route('/api/organisms')]
class OrganismController extends AbstractController
{
    private $organismService;

    public function __construct(OrganismService $organismService)
    {
        $this->organismService = $organismService;
    }

    #[Route('/new', name: 'api_organism_new', methods: ['POST'])]
    public function newOrganism(Request $request)
    {
        return $this->organismService->create($request);
    }

    #[Route('/by-Creator/{id}', name: 'api_organism_by_creator', methods: ['GET'])]
    public function getOrganismByCreator($id)
    {
        return $this->organismService->readByCreator($id);
    }
    
    #[Route('/', name: 'api_organism_read_all', methods: ['GET'])]
    public function readAllOrganisms(): Response
    {
        return $this->organismService->readAll();
    }

    #[Route('/{id}', name: 'api_organism_read', methods: ['GET'])]
    public function readOrganism(int $id): Response
    {
        return $this->organismService->read($id);
    }

    #[Route('/edit/{id}', name: 'api_organism_edit', methods: ['PUT'])]
    public function updateOrganism(Request $request, int $id): Response
    {
        return $this->organismService->update($request, $id);
    }

    #[Route('/delete/{id}', name: 'api_organism_delete', methods: ['DELETE'])]
    public function deleteOrganism(int $id): Response
    {
        return $this->organismService->delete($id);
    }
}
