<?php

namespace App\Service;

use App\Entity\Student;
use App\Entity\Training;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentService
{
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function createStudents(array $studentsData): JsonResponse
    {
        foreach ($studentsData as $studentData) {

            if (!$this->entityManager->getRepository(Student::class)->findOneBy(['email' => $studentData['email']])) {
                $student = new Student();
                $student->setFirstName($studentData['firstName']);
                $student->setLastName($studentData['lastName']);
                $student->setEmail($studentData['email']);
                if ($studentData['invidual'] == 'FALSE' || $studentData['invidual'] == 'false' || $studentData['invidual'] == 0) {
                    $student->setInvidual(false);
                } else if ($studentData['invidual'] == 'TRUE' || $studentData['invidual'] == 'true' || $studentData['invidual'] == 1) {
                    $student->setInvidual(true);
                }
                $student->setCreatedAt();
                $student->setRoles(['ROLE_STUDENT']);
                $student->setPassword(bin2hex(random_bytes(16)));

                $this->entityManager->persist($student);
            }
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to create students'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Students created successfully'], Response::HTTP_CREATED);
    }

    public function newStudent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $student = new Student();

        if (!isset($data['firstName']) || !isset($data['lastName']) || !isset($data['email']) || !isset($data['invidual'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if (!preg_match("/^\S+@\S+\.\S+$/", $data['email'])) {
            return new JsonResponse(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
        }

        $student->setFirstName($data['firstName']);
        $student->setLastName($data['lastName']);
        $student->setEmail($data['email']);
        $student->setPhoto($data['photo'] != null ? $data['photo'] : "https://randomuser.me/api/portraits/men/25.jpg");
        $student->setCreatedAt();
        $student->setInvidual($data['invidual']);
        $student->setRoles(['ROLE_STUDENT']);
        $student->setPassword(
            $this->userPasswordHasher->hashPassword(
                $student,
                $data['password']
            )
        );

        try {
            $this->entityManager->persist($student);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to save the student'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Student created successfully'], Response::HTTP_CREATED);
    }

    public function getOneStudent(int $id): JsonResponse
    {
        $student = $this->entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        $studentData = [
            'id' => $student->getId(),
            'firstName' => $student->getFirstName(),
            'lastName' => $student->getLastName(),
            'email' => $student->getEmail(),
            'photo' => $student->getPhoto(),
            'roles' => $student->getRoles(),
            'invidual' => $student->isInvidual(),
            'createdAt' => $student->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $student->getUpdatedAt() ? $student->getUpdatedAt()->format('Y-m-d H:i:s') : null,
        ];

        return new JsonResponse($studentData, Response::HTTP_OK);
    }

    public function getAllStudents(): JsonResponse
    {
        $students = $this->entityManager->getRepository(Student::class)->findAll();

        $studentsData = [];

        foreach ($students as $student) {
            $studentsData[] = [
                'id' => $student->getId(),
                'firstName' => $student->getFirstName(),
                'lastName' => $student->getLastName(),
                'email' => $student->getEmail(),
                'photo' => $student->getPhoto(),
                'invidual' => $student->isInvidual(),
                'createdAt' => $student->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $student->getUpdatedAt() ? $student->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'deletedAt' => $student->getDeletedAt() ? $student->getDeletedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return new JsonResponse($studentsData, Response::HTTP_OK);
    }

    public function getAllStudentsByTraining(int $idTraining): JsonResponse
    {
        $training = $this->entityManager->getRepository(Training::class)->find($idTraining);

        $registrations = $training->getRegistrations();

        $studentsData = [];

        foreach ($registrations as $registration) {
            $student = $registration->getStudent();
            $studentData = [
                'id' => $student->getId(),
                'firstName' => $student->getFirstName(),
                'lastName' => $student->getLastName(),
                'email' => $student->getEmail(),
                'photo' => $student->getPhoto(),
                'invidual' => $student->isInvidual(),
                'createdAt' => $student->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $student->getUpdatedAt() ? $student->getUpdatedAt()->format('Y-m-d H:i:s') : null,
                'deletedAt' => $student->getDeletedAt() ? $student->getDeletedAt()->format('Y-m-d H:i:s') : null,
            ];

            $studentsData[] = $studentData;
        }

        return new JsonResponse($studentsData, Response::HTTP_OK);
    }

    public function editStudent(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $student = $this->entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['firstName'])) {
            $student->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $student->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            if (!preg_match("/^\S+@\S+\.\S+$/", $data['email'])) {
                return new JsonResponse(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
            }
            if ($data['email'] != $student->getEmail() && $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
                return new JsonResponse(['error' => 'Email already exists'], Response::HTTP_CONFLICT);
            }
            $student->setEmail($data['email']);
        }
        $student->setPhoto($data['photo'] != null ? $data['photo'] : "https://randomuser.me/api/portraits/men/25.jpg");
        if (isset($data['invidual'])) {
            $student->setInvidual($data['invidual']);
        }
        if (isset($data['password'])) {
            $student->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $student,
                    $data['password']
                )
            );
        }

        $student->setUpdatedAt();

        try {
            $this->entityManager->persist($student);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to edit the student'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Student edited successfully'], Response::HTTP_OK);
    }

    public function deleteStudent(int $id): JsonResponse
    {
        $student = $this->entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            // $this->entityManager->remove($student);
            $student->setDeletedAt();
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete the student'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Student deleted successfully'], Response::HTTP_OK);
    }
}
