<?php

namespace App\ArgumentResolver\DTO;

use App\Exceptions\DeserializationFailedException;
use App\Interfaces\DTO\JsonPayloadObject;
use Throwable;

/**
 * Specific ArgumentResolver to handle automatic resolving of DTOs with JsonPayloadObject interface
 */
class JsonPayloadDTOResolver extends AbstractDTOResolver
{

    /**
     * @psalm-pure
     * @return class-string
     */
    protected function getInterfaceName(): string
    {
        return JsonPayloadObject::class;
    }

    /**
     * @param class-string $className
     * @param string $payload
     *
     * @return object[]|object
     * @throws DeserializationFailedException
     */
    protected function parseTargetDTO(string $className, string $payload): array|object
    {
        try {
            return $this->serializer->deserialize($payload, $className, 'json');
        } catch (Throwable $error) {
            $this->logger->critical('Error occurred during deserialization of request payload', [
                'message' => $error->getMessage(),
                'payload' => $payload
            ]);
        }

        throw new DeserializationFailedException('Error happened during deserialization', [
            'target_class' => $className,
            'payload' => $payload
        ]);
    }
}
