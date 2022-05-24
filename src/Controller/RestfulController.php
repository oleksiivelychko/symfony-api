<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestfulController extends AbstractController
{
    protected const USER_NOT_FOUND = 'User not found';
    protected const USER_HAS_BEEN_CREATED = 'User has been created';
    protected const USER_HAS_BEEN_UPDATED = 'User has been updated';
    protected const UNPROCESSABLE_ENTITY = 'Unprocessable entity';
    protected const EMAIL_IS_NOT_VALID = 'Email is not valid email address';
    protected const EMAIL_ALREADY_TAKEN = 'Email already taken';

    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);
        return $request;
    }
}