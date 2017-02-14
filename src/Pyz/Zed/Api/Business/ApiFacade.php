<?php

namespace Pyz\Zed\Api\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Pyz\Zed\Api\Business\ApiBusinessFactory getFactory()
 */
class ApiFacade extends AbstractFacade implements ApiFacadeInterface
{

    /**
     * @param string $bundle
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function callBundleMethod($bundle, $method, array $arguments)
    {
        return $this->getFactory()->createFacadeProxy($bundle)->forwardCall($method, $arguments);
    }

    /**
     * @param string $bundle
     *
     * @return array
     */
    public function getAnnotations($bundle)
    {
        return $this->getFactory()->createFacadeProxy($bundle)->getAnnotations();
    }

    /**
     * @param string $transfer
     *
     * @return array
     */
    public function getTransferAnnotations($transfer)
    {
        return $this->getFactory()->createTransferAnnotator()->annotate($transfer);
    }

}