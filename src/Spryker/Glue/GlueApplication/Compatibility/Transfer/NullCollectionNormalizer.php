<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\GlueApplication\Compatibility\Transfer;

use ArrayObject;
use ReflectionMethod;
use ReflectionNamedType;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

/**
 * `AbstractTransfer::fromArray()` reads a `null` like an absent key, except for a collection
 * property, which it iterates and fails on. The generated setters are the public record of a
 * property's type: `ArrayObject` for a collection, whose element class the `@param` generic names,
 * or the nested transfer class.
 */
class NullCollectionNormalizer implements NullCollectionNormalizerInterface
{
    protected const string SETTER_PREFIX = 'set';

    protected const string PATTERN_COLLECTION_ELEMENT_CLASS = '/@param\s+\\\\ArrayObject<\\\\([\w\\\\]+)>/';

    /**
     * @param class-string<\Spryker\Shared\Kernel\Transfer\AbstractTransfer> $transferClass
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    public function normalize(string $transferClass, array $data): array
    {
        foreach ($data as $key => $value) {
            $setter = $this->findSetter($transferClass, (string)$key);

            if ($setter === null) {
                continue;
            }

            $parameterClass = $this->findParameterClass($setter);

            if ($parameterClass === ArrayObject::class) {
                $data[$key] = $this->normalizeCollection($setter, $value);

                continue;
            }

            if (is_array($value) && $parameterClass !== null && is_subclass_of($parameterClass, AbstractTransfer::class)) {
                $data[$key] = $this->normalize($parameterClass, $value);
            }
        }

        return $data;
    }

    protected function normalizeCollection(ReflectionMethod $setter, mixed $value): mixed
    {
        if ($value === null) {
            return [];
        }

        $elementClass = $this->findCollectionElementClass($setter);

        if (!is_array($value) || $elementClass === null) {
            return $value;
        }

        foreach ($value as $index => $element) {
            if (is_array($element)) {
                $value[$index] = $this->normalize($elementClass, $element);
            }
        }

        return $value;
    }

    /**
     * @param class-string $transferClass
     */
    protected function findSetter(string $transferClass, string $propertyName): ?ReflectionMethod
    {
        $setterName = static::SETTER_PREFIX . ucfirst($propertyName);

        if ($propertyName === '' || !method_exists($transferClass, $setterName)) {
            return null;
        }

        return new ReflectionMethod($transferClass, $setterName);
    }

    protected function findParameterClass(ReflectionMethod $setter): ?string
    {
        $parameterType = ($setter->getParameters()[0] ?? null)?->getType();

        if (!$parameterType instanceof ReflectionNamedType || $parameterType->isBuiltin()) {
            return null;
        }

        return $parameterType->getName();
    }

    /**
     * @return class-string<\Spryker\Shared\Kernel\Transfer\AbstractTransfer>|null
     */
    protected function findCollectionElementClass(ReflectionMethod $setter): ?string
    {
        if (!preg_match(static::PATTERN_COLLECTION_ELEMENT_CLASS, (string)$setter->getDocComment(), $matches)) {
            return null;
        }

        $elementClass = $matches[1];

        return is_subclass_of($elementClass, AbstractTransfer::class) ? $elementClass : null;
    }
}
