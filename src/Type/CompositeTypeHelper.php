<?php declare(strict_types = 1);

namespace PHPStan\Type;

class CompositeTypeHelper
{

    public static function acceptsAll(Type $type, CompositeType $unionType): bool
    {
        foreach ($unionType->getTypes() as $otherType) {
            if (!$type->accepts($otherType)) {
                return false;
            }
        }

        return true;
    }

    public static function acceptsAny(Type $type, CompositeType $boundedType): bool
    {
        foreach ($boundedType->getTypes() as $otherType) {
            if ($type->accepts($otherType)) {
                return true;
            }
        }

        return false;
    }

    public static function accepts(Type $type, CompositeType $compositeType): bool
    {
        if ($compositeType instanceof UnionType) {
            return self::acceptsAll($type, $compositeType);
        } elseif ($compositeType instanceof BoundedType) {
            return self::acceptsAny($type, $compositeType);
        }

        return false;
    }

}
