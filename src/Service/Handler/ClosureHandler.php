<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler;

use Nette\PhpGenerator\Closure as NetteClosure;
use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Service\TryAutowiringService;
use Reinfi\OptimizedServiceManager\Types\AutoWire;
use Reinfi\OptimizedServiceManager\Types\Closure;
use Reinfi\OptimizedServiceManager\Types\Invokable;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler
 */
class ClosureHandler implements HandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TryAutowiringService
     */
    private $tryAutowiringService;

    /**
     * @var InvokableHandler
     */
    private $invokableHandler;

    /**
     * @var AutowireHandler
     */
    private $autowireHandler;

    /**
     * @param ContainerInterface $container
     * @param TryAutowiringService $tryAutowiringService
     * @param InvokableHandler $invokableHandler
     * @param AutowireHandler $autowireHandler
     */
    public function __construct(
        ContainerInterface $container,
        TryAutowiringService $tryAutowiringService,
        InvokableHandler $invokableHandler, AutowireHandler $autowireHandler
    ) {
        $this->container = $container;
        $this->tryAutowiringService = $tryAutowiringService;
        $this->invokableHandler = $invokableHandler;
        $this->autowireHandler = $autowireHandler;
    }

    /**
     * @param Closure|TypeInterface $type
     *
     * @return InstantiationMethod
     */
    public function handle(TypeInterface $type): InstantiationMethod
    {
       $method = $this->tryAutoWiring($type);

       if ($method instanceof InstantiationMethod) {
           $method->setClassName($type->getService());

           return $method;
       }

       return $this->buildMethodFromClosure($type);
    }

    /**
     * @param Closure $type
     *
     * @return null|InstantiationMethod
     */
    private function tryAutoWiring(Closure $type)
    {
        $closure = $type->getCallable();

        try {
            $instance = $closure($this->container);

            $type = $this->tryAutowiringService->tryAutowiring(
                get_class($instance)
            );

            if ($type === null) {
                return null;
            }

            if ($type instanceof Invokable) {
                return $this->invokableHandler->handle($type);
            }

            if ($type instanceof AutoWire) {
                return $this->autowireHandler->handle($type);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param Closure $type
     *
     * @return InstantiationMethod
     */
    private function buildMethodFromClosure(Closure $type): InstantiationMethod
    {
        $closure = NetteClosure::from($type->getCallable());

        $r = new \ReflectionFunction($type->getCallable());

        $file = file($r->getFileName());

        $lines = implode(array_slice($file, $r->getStartLine(), $r->getEndLine() - $r->getStartLine()));

        $lines = substr($lines, strpos($lines, '{')+1);
        $lines = substr($lines, 0, strrpos($lines, '}'));
        $lines = explode('\\n', $lines);

        foreach ($lines as $line) {
            $closure->addBody($line);
        }

        $this->cleanupMethod($closure);

        $method = new InstantiationMethod($type->getService());

        $method->addBodyPart(
            sprintf(
                '$callable = %s;',
                (string) $closure
            )
        );
        $method->addBodyPart('return $callable($this);');

        return $method;
    }

    /**
     * Remove all typehints because they are not correctly resolved
     *
     * @param NetteClosure $closure
     */
    private function cleanupMethod(NetteClosure $closure)
    {
        $parameters = $closure->getParameters();

        foreach ($parameters as $parameter) {
            $parameter->setTypeHint(null);
        }

        $closure->setParameters($parameters);
    }
}