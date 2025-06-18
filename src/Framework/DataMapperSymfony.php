<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Framework;

use Symfony\Component\HttpFoundation\Request;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;

/**
 * @template T of object
 * @extends DataMapper<T>
 */
class DataMapperSymfony extends DataMapper
{
    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function request(
        Request $request,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        $content = $request->getContent();
        $sourceTypeEnum = match ($request->headers->get('Content-Type')) {
            'application/json' => SourceTypeEnum::JSON,
            'application/neon', 'text/neon' => SourceTypeEnum::NEON,
            'application/xml', 'text/xml' => SourceTypeEnum::XML,
            'application/yaml', 'text/yaml' => SourceTypeEnum::YAML,
            default => throw DataMapperException::InvalidArgument('Unsupported content type.'),
        };

        return $this->map($sourceTypeEnum, $content, $object, $rootElementTree, $forceInstance);
    }
}
