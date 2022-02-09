<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueApplication\Executor;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class ResourceExecutor implements ResourceExecutorInterface
{
    /**
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function executeResource(
        ResourceInterface $resource,
        GlueRequestTransfer $glueRequestTransfer
    ): GlueResponseTransfer {
        $glueResponseTransfer = new GlueResponseTransfer();

        $executableResource = $resource->getResource($glueRequestTransfer);

        if ($glueRequestTransfer->getContent()) {
            $attributesTransfer = $this->getAttributesTransfer($executableResource);

            if (!$attributesTransfer) {
                return call_user_func($executableResource, $glueRequestTransfer, $glueResponseTransfer);
            }

            $attributesTransfer->fromArray($glueRequestTransfer->getAttributes(), true);
            $glueRequestTransfer->getResource()->setAttributes($attributesTransfer);

            return call_user_func($executableResource, $attributesTransfer, $glueRequestTransfer, $glueResponseTransfer);
        }

        if ($glueRequestTransfer->getResource()->getId()) {
            return call_user_func($executableResource, $glueRequestTransfer->getResource()->getId(), $glueRequestTransfer, $glueResponseTransfer);
        }

        return call_user_func($executableResource, $glueRequestTransfer, $glueResponseTransfer);
    }

    /**
     * @param callable $executableResource
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null
     */
    protected function getAttributesTransfer(callable $executableResource): ?AbstractTransfer
    {
        if (is_array($executableResource) && count($executableResource) === 2) {
            $reflectedMethod = new ReflectionMethod($executableResource[0], $executableResource[1]);

            return $this->getFirstParameterType($reflectedMethod);
        }

        $reflectedFunction = new ReflectionFunction($executableResource);

        return $this->getFirstParameterType($reflectedFunction);
    }

    /**
     * @param \ReflectionFunctionAbstract $reflectionFunction
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null
     */
    protected function getFirstParameterType(ReflectionFunctionAbstract $reflectionFunction): ?AbstractTransfer
    {
        $firstParameterType = current($reflectionFunction->getParameters())->getType()->getName();

        if (
            is_subclass_of($firstParameterType, AbstractTransfer::class) &&
            !$firstParameterType instanceof GlueRequestTransfer
        ) {
            return new $firstParameterType();
        }

        return null;
    }
}
