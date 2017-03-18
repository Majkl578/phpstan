<?php declare(strict_types = 1);

namespace PHPStan\Type;

class CommonBoundedType implements BoundedType
{
    /** @var Type[] */
    private $types;

    /** @var bool */
    private $nullable;

    /**
     * @param Type[] $types
     * @param bool $nullable
     */
    public function __construct(array $types, bool $nullable)
    {
        $this->types = $types;
        $this->nullable = $nullable;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferencedClasses(): array
    {
        return UnionTypeHelper::getReferencedClasses($this->types);
    }

    /**
     * {@inheritdoc}
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * {@inheritdoc}
     */
    public function combineWith(Type $otherType): Type
    {
        if ($otherType instanceof NullType) {
            return $this->makeNullable();
        }

        if ($otherType instanceof BoundedType) {
            $otherTypesTemp = [];
            foreach ($this->getTypes() as $otherOtherType) {
                $otherTypesTemp[$otherOtherType->describe()] = $otherOtherType;
            }
            foreach ($otherType->getTypes() as $otherOtherType) {
                $otherTypesTemp[$otherOtherType->describe()] = $otherOtherType;
            }

            $types = array_values($otherTypesTemp);
        } elseif ($otherType instanceof UnionType) {
            $ourTypesTemp = [];
            foreach ($this->getTypes() as $otherOtherType) {
                $ourTypesTemp[$otherOtherType->describe()] = $otherOtherType;
            }

            foreach ($otherType->getTypes() as $otherOtherType) {
                if (!isset($ourTypesTemp[$otherOtherType->describe()])) {
                    return new MixedType();
                    break;
                }
            }

            $types = array_values($ourTypesTemp);
        } else {
            return new MixedType();
        }

        return new CommonUnionType($types, $this->nullable && $otherType->isNullable());
    }

    /**
     * {@inheritdoc}
     */
    public function makeNullable(): Type
    {
        return new self($this->types, true);
    }

    /**
     * {@inheritdoc}
     */
    public function accepts(Type $type): bool
    {
        if ($type instanceof BoundedType) {
            foreach ($this->types as $ourType) {
                foreach ($type->getTypes() as $theirType) {
                    if ($ourType->accepts($theirType)) {
                        continue 2;
                    }
                }

                return false;
            }

            return true;
        }

        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $theirType) {
                foreach ($this->types as $ourType) {
                    if ($ourType->accepts($theirType)) {
                        continue 2;
                    }
                }

                return false;
            }

            return true;
        }

        foreach ($this->types as $subType) {
            if (!$subType->accepts($type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function describe(): string
    {
        $description = implode(
            ' & ',
            array_map(
                function (Type $type) : string {
                    return $type->describe();
                },
                $this->types
            )
        );

        if ($this->nullable) {
            $description = '(' . $description . ')|null';
        }

        return $description;
    }

    /**
     * {@inheritdoc}
     */
    public function canAccessProperties(): bool
    {
        foreach ($this->types as $subType) {
            if (!$subType->canAccessProperties()) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function canCallMethods(): bool
    {
        foreach ($this->types as $subType) {
            if (!$subType->canCallMethods()) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function isDocumentableNatively(): bool
    {
        return FALSE;
    }
}
