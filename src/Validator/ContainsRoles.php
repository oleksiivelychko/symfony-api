<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ContainsRoles extends Constraint
{
    public string $message = 'assert.roles.invalid';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}