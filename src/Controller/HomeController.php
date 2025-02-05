<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class HomeController extends AbstractController
{
    public const USER_DATA = [
        [
            'id' => '1',
            'email' => 'ipz231_sko@student.ztu.edu.ua',
            'name' => 'Kateryna'
        ],
        [
            'id' => '2',
            'email' => 'peter2@doe1.com',
            'name' => 'Peter'
        ],
        [
            'id' => '3',
            'email' => 'daria3@doe1.com',
            'name' => 'Daria'
        ],
        [
            'id' => '4',
            'email' => 'maxim4@doe1.com',
            'name' => 'Maxim'
        ],
        [
            'id' => '5',
            'email' => 'david5@doe1.com',
            'name' => 'David'
        ],
        [
            'id' => '6',
            'email' => 'sofia6@doe1.com',
            'name' => 'Sofia'
        ],
        [
            'id' => '7',
            'email' => 'anastasia7@doe1.com',
            'name' => 'Anastasia'
        ],
    ];

    #[Route('/users', name: 'collection_users', methods: ['GET'])]
    public function getCollectionOfUsers(): JsonResponse
    {
        return new JsonResponse([
            'data' => self::USER_DATA
            ], Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'item_users', methods: ['GET'])]
    public function getItem(string $id): JsonResponse
    {
        $userData = $this->findUserByid($id);
        return new JsonResponse([
            'data' => $userData
        ], Response::HTTP_OK);
    }

    #[Route('/users', name: 'create_users', methods: ['POST'])]
    public function createItem(Request $request): JsonResponse
    {
        //POST json
        $requestData = json_decode($request->getContent(), true);

        if(!isset($requestData['email'], $requestData['name'])) {
            throw new UnprocessableEntityHttpException("name and email are required");
        }

        //check by regex

        $email = $requestData['email'];
        $name = $requestData['name'];

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UnprocessableEntityHttpException("email is not valid");
        }

        if(!preg_match('/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ\'\- ]+$/u', $name)){
            throw new UnprocessableEntityHttpException("name is not valid");
        }

        $countOfUsers = count(self::USER_DATA);

        $newUser=[
            'id' => $countOfUsers = 8,
            'name' => $requestData['name'],
            'email' => $requestData['email']
        ];

        //TODO add new user to collection

        return new JsonResponse([
            'data' => $newUser
        ], Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'delete_users', methods: ['DELETE'])]
    public function deleteItem(string $id): JsonResponse
    {
        $this->findUserByid($id);
        //TODO remove user from collection


        return new JsonResponse([],Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'update_users', methods: ['PATCH'])]
    public function updateItem(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if(!isset($requestData['name'])) {
            throw new UnprocessableEntityHttpException("name is required");
        }

        $userData = $this->findUserByid($id);
        //TODO update user name

        $userData['name'] = $requestData['name'];

        return new JsonResponse(['data' => $userData],Response::HTTP_OK);
    }

    public function findUserById(string $id): array{
        $userData = null;

        foreach (self::USER_DATA as $user) {
            if(!isset($user['id'])) {
                continue;
            }

            if($user['id'] == $id) {
                $userData = $user;

                break;
            }
        }

        if(!$userData) {
            throw new NotFoundHttpException("User with id " .$id. " not found");
        }
        return $userData;
    }
}