<?php

namespace App\ArgumentResolver\DTO;

use App\Exceptions\Api\UnprocessableEntityException;
use App\Exceptions\DeserializationFailedException;
use Generator;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDTOResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        private ValidatorInterface $validator
    ) {}

    /**
     * Function should return simple string
     * @psalm-pure
     * @return class-string - Class-string of target Interface which Resolver will be attached to
     */
    abstract protected function getInterfaceName(): string;

    /**
     * Implement this method to handle proper data deserialization
     *
     * @param class-string $className
     * @param string $payload
     *
     * @return object[]|object
     */
    abstract protected function parseTargetDTO(string $className, string $payload): array|object;

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return Generator
     * @throws DeserializationFailedException
     */
    final public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $class = $argument->getType();
        if ($class === null || !class_exists($class)) {
            throw new DeserializationFailedException('Can`t determine valid className', [
                'target_class' => $class
            ]);
        }

        $json = $request->getContent(false);
        if (!is_string($json) || false === json_decode($json)) {
            throw new DeserializationFailedException('Provided JSON is not valid', [
                'target_class' => $class,
                'payload' => $json
            ]);
        }
        $dtoResult = $this->parseTargetDTO($class, $json);

        // We need to make sure that system work with valid objects only,
        // in case if violation happens throw an exception
        $violations = $this->validator->validate($dtoResult);
        if ($violations->count() > 0) {
            throw new UnprocessableEntityException($violations);
        }

        yield $dtoResult;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     * @throws ReflectionException
     */
    final public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();
        if ($type !== null && class_exists($type)) {
            $reflection = new ReflectionClass($type);

            return $reflection->implementsInterface($this->getInterfaceName());
        }

        return false;
    }

}
