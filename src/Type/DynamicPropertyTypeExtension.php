<?php declare(strict_types = 1);

namespace PHPStan\Type;

use PHPStan\Reflection\PropertyReflection;

interface DynamicPropertyTypeExtension
{

    public function getClass(): string;

    public function isPropertySupported(PropertyReflection $propertyReflection): bool;

    public function getPropertyTypeFrom

}
