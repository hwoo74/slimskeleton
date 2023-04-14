<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;


class ListUsersAction extends UserAction
{
    //
    // {@inheritdoc}
    //
    protected function action(): Response
    {
        $users = $this->userRepository->findAll();

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}


/*
use App\Infrastructure\Persistence\User\InMemoryUserRepository as UserRepository;

class ListUsersAction
{
    public function __construct() {
        echo 'new listuserAction constructor';
    }

    //
    // {@inheritdoc}
    //
    protected function action( UserRepository $userRepository ): Response
    {
        $users = $userRepository->findAll();

        //$this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
*/