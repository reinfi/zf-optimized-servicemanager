<?php

namespace Reinfi\OptimizedServiceManager\Model;

/**
 * @package Reinfi\OptimizedServiceManager\Model
 */
class InstantiationMethod
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var array
     */
    protected $methodBody = [];

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @param string $body
     *
     * @return InstantiationMethod
     */
    public function addBodyPart(string $body)
    {
        $this->methodBody[] = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     *
     * @return InstantiationMethod
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return array
     */
    public function getMethodBody(): array
    {
        return $this->methodBody;
    }
}