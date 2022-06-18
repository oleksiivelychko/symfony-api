<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class RestfulController extends AbstractController
{
    public const ENTITY_NOT_FOUND        = 'Entity not found';
    public const ENTITY_HAS_BEEN_CREATED = 'Entity has been created';
    public const ENTITY_HAS_BEEN_UPDATED = 'Entity has been updated';
    public const ENTITY_HAS_BEEN_DELETED = 'Entity has been deleted';
    public const UNPROCESSABLE_ENTITY    = 'Unprocessable entity';
    public const EMAIL_ALREADY_TAKEN     = 'Email already taken';

    protected function unprocessableExceptionMessage(\Exception $e): string
    {
        return self::UNPROCESSABLE_ENTITY.', '.$e->getMessage();
    }
}