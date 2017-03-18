<?php declare(strict_types = 1);

namespace PHPStan\Type;

use PhpParser\Node\Expr\Assign;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;

interface DynamicMethodReturnTypeExtension
{

	public static function getClass(): string;

	public function isMethodSupported(MethodReflection $methodReflection): bool;

	public function getType(PropertyReflection $propertyReflection, Assign $assign, Scope $scope): Type;

}
