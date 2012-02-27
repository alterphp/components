<?php

namespace AlterPHP\Component\Form;

use Symfony\Component\Form\AbstractExtension;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;

class AlterPHPExtension extends AbstractExtension
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    protected function loadTypes()
    {
        return array(
            new Type\EntityBitType($this->registry),
        );
    }

    protected function loadTypeGuesser()
    {
        return new DoctrineOrmTypeGuesser($this->registry);
    }
}
