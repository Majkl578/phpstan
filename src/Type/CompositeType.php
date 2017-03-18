<?php declare(strict_types = 1);

namespace PHPStan\Type;

interface CompositeType extends Type
{

    /**
     * @return \PHPStan\Type\Type[]
     */
    public function getTypes(): array;

}
