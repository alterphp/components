<?php

namespace AlterPHP\Component\Twig\Extension;

class Reflection extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('call_static', [$this, 'callStaticMethod']),
            new \Twig_SimpleFunction('get_static', [$this, 'getStaticProperty']),
        );
    }

    public function callStaticMethod($class, $method, array $args = [])
    {
        $refl = new \reflectionClass($class);

        // Check that method is static AND public
        if ($refl->hasMethod($method) && $refl->getMethod($method)->isStatic() && $refl->getMethod($method)->isPublic()) {
            return call_user_func($class.'::'.$method, $args);
        }

        throw new \RuntimeException(sprintf('Invalid static method call for class %s and method %s', $class, $method));
    }

    public function getStaticProperty($class, $property)
    {
        $refl = new \reflectionClass($class);

        // Check that property is static AND public
        if ($refl->hasProperty($property) && $refl->getProperty($property)->isStatic() && $refl->getProperty($property)->isPublic()) {
            return $refl->getProperty($property)->getValue();
        }

        throw new \RuntimeException(sprintf('Invalid static property get for class %s and property %s', $class, $property));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'reflection';
    }
}