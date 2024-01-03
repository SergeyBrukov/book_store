<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class CustomBookFilter extends AbstractFilter
{
    private const PROPERTY_NAME = 'name';

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (!$this->isPropertyEnabled($property, $resourceClass) || !$this->isPropertyMapped($property, $resourceClass)) {
            return;
        }

        switch ($property) {
            case (self::PROPERTY_NAME):
            {
                $parameterName = $queryNameGenerator->generateParameterName($property);
                $queryBuilder
                    ->andWhere(sprintf('o.%s LIKE :%s', $property, $parameterName))
                    ->setParameter($parameterName, '%' . $value . '%'); // Додати % для пошуку часткових відповідностей
            }
            break;
            default:
            {
                $parameterName = $queryNameGenerator->generateParameterName($property);
                $queryBuilder
                    ->andWhere(sprintf('o.%s = :%s', $property, $parameterName))
                    ->setParameter($parameterName, $value);
            }
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}