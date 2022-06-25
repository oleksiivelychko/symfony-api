<?php

namespace App\Dto\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws \ReflectionException
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        try {
            $reflection = new \ReflectionClass($argument->getType());
            if ($reflection->implementsInterface(RequestDTOInterface::class)) {
                return true;
            }
        } catch (\ReflectionException $e) {
            return false;
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // creating new instance of custom request DTO
        $class = $argument->getType();
        $dto = new $class($request);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }

        yield $dto;
    }
}