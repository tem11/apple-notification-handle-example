<?php

namespace App\Interfaces\DTO;

/**
 * Interface should be used to automatically resolve list of objects.
 * Container class/object should remain simple DTO and should not contain any extra functionality beside
 * getTargetClass method.
 */
interface JsonPayloadArray
{
    /**
     * JsonPayloadArray DTO should contain list of objects, otherwise it's existence doesn't make sense
     */
    public function __construct(array $objectsArray);

    /** @return class-string */
    public static function getTargetClass(): string;
}
