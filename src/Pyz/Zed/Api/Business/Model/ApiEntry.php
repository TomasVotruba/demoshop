<?php

namespace Pyz\Zed\Api\Business\Model;

use Spryker\Zed\Kernel\Business\AbstractFacade;
use ReflectionClass;
use ReflectionMethod;
use ReflectionType;

class ApiEntry implements ApiEntryInterface
{

    /**
     * @var \Spryker\Zed\Kernel\Business\AbstractFacade
     */
    protected $wrappedFacade;

    /**
     * @param \Spryker\Zed\Kernel\Business\AbstractFacade $wrappedFacade
     */
    public function __construct(AbstractFacade $wrappedFacade)
    {
        $this->wrappedFacade = $wrappedFacade;
    }

    /**
     * @return array
     */
    public function getAnnotations()
    {
        $className = get_class($this->wrappedFacade);
        $reflection = new ReflectionClass($className);
        return $this->getPublicInterfaceAnnotations($reflection);
    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    protected function getPublicInterfaceAnnotations(ReflectionClass $reflection)
    {
        $result = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Docstring lookup in interfaces.
            $docDomment = $method->getDocComment();
            if (stripos($docDomment, '@inheritdoc') !== false) {
                foreach ($reflection->getInterfaces() as $interface) {
                    if ($interface->hasMethod($method->getName())) {
                        $docDomment = $interface->getMethod($method->getName())->getDocComment();
                        break;
                    }
                }
            }

            $result[$method->getName()] = [
                'docString' => $docDomment,
                'parameters' => $this->annotateIncomingParameters($method),
            ];
        }

        return $result;
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return array
     */
    protected function annotateIncomingParameters(ReflectionMethod $method)
    {
        $result = [];

        foreach ($method->getParameters() as $parameter) {
            $result[$parameter->getName()] = $this->annotateType($parameter->getType(), $parameter->getClass());
        }

        return $result;
    }

    /**
     * @param \ReflectionType $type
     * @param \ReflectionClass|null $class
     *
     * @return array
     */
    protected function annotateType(ReflectionType $type = null, ReflectionClass $class = null)
    {
        return [
            'type' => $class !== null ? $class->getName() : $type,
            'isTransfer' => $class !== null ? $class->isSubclassOf('Spryker\Shared\Transfer\AbstractTransfer') : false,
        ];
    }

}
