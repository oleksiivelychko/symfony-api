<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestfulController extends AbstractController
{
    public const ENTITY_NOT_FOUND        = 'Entity not found';
    public const ENTITY_HAS_BEEN_CREATED = 'Entity has been created';
    public const ENTITY_HAS_BEEN_UPDATED = 'Entity has been updated';
    public const ENTITY_HAS_BEEN_DELETED = 'Entity has been deleted';
    public const UNPROCESSABLE_ENTITY    = 'Unprocessable entity';
    public const EMAIL_IS_NOT_VALID      = 'Email is not valid email address';
    public const EMAIL_ALREADY_TAKEN     = 'Email already taken';
    public const EMPTY_REQUEST_DATA      = 'Empty request data';

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