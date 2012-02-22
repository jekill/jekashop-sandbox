<?php
namespace Application\Vespolina\ProductBundle\Document;

use \Vespolina\ProductBundle\Model\Identifier\BaseIdentifier;

class S3Identifier extends BaseIdentifier{
    protected $name = 's3';
    /**
     * @var string $code
     */
    protected $code;


    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string $code
     */
    public function getCode()
    {
        return $this->code;
    }
}
