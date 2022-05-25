<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestfulController extends AbstractController
{
    protected const ENTITY_NOT_FOUND        = 'Entity not found';
    protected const ENTITY_HAS_BEEN_CREATED = 'Entity has been created';
    protected const ENTITY_HAS_BEEN_UPDATED = 'Entity has been updated';
    protected const ENTITY_HAS_BEEN_DELETED = 'Entity has been deleted';
    protected const UNPROCESSABLE_ENTITY    = 'Unprocessable entity';
    protected const EMAIL_IS_NOT_VALID      = 'Email is not valid email address';
    protected const EMAIL_ALREADY_TAKEN     = 'Email already taken';

    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);
        return $request;
    }

    protected function unprocessableExceptionMessage(\Exception $e): string
    {
        return self::UNPROCESSABLE_ENTITY.', '.$e->getMessage();
    }
}