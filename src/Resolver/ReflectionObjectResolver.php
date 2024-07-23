<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Reflection\AnnotationReflection;
use DataMapper\Reflection\ObjectReflection;
use DataMapper\Reflection\ParameterReflection;
use DataMapper\Reflection\PropertyReflection;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

final readonly class ReflectionObjectResolver
{
    public function parseAnnotation(string $docComment): AnnotationReflection
    {
        $parameterReflections = [];
        $variables = [];
        $docComment = trim($docComment);

        if (! str_starts_with($docComment, '/**')) {
            return new AnnotationReflection([], []);
        }

        $docComment = substr($docComment, 3, -2);

        $pattern = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

        if (preg_match_all($pattern, $docComment, $matches)) {
            $parameters = [];

            /**
             * pre-process for annotation types
             */
            foreach ($matches['name'] ?? [] as $key => $name) {
                if (strtolower($name) === 'param') {
                    $parameters[] = $matches['value'][$key];
                }

                if (strtolower($name) === 'var') {
                    $variables[] = $matches['value'][$key];
                }
            }

            foreach ($parameters as $param) {
                list($parameterType, $parameter) = explode(' ', $param);

                $parameterTypes = explode('|', $parameterType);

                foreach ($parameterTypes as $key => $parameterType) {
                    if (str_starts_with($parameterType, '?')) {
                        $parameterTypes[$key] = 'null';
                        $parameterTypes[] = substr($parameterType, 1);
                    }
                }

                $parameterReflections[] = new ParameterReflection(
                    $parameter,
                    $parameterTypes,
                );
            }

            if ($variables !== []) {
                $variables = explode('|', array_pop($variables));
                foreach ($variables as $key => $variable) {
                    if (str_starts_with($variable, '?')) {
                        $variables[$key] = 'null';
                        $variables[] = substr($variable, 1);
                    }
                }
            }
        }

        return new AnnotationReflection(
            $parameterReflections,
            $variables,
        );
    }

    public function classString(ReflectionProperty|ReflectionFunctionAbstract $property): ?string
    {
        $docComment = $property->getDocComment();

        if ($docComment === false) {
            return null;
        }

        // $annotation = $this->parseAnnotation($docComment);
        // dump($annotation);

        $classString = null;
        // // dump($annotations);
        // foreach ($annotation->getParameterReflections() as $parameterReflection) {
        //
        //     $find = array_filter($parameterReflection->getTypes(), function ($type) use ($property) {
        //         return str_contains($type, '$' . $property->getName());
        //     });
        //
        //     if (str_contains($parameterReflection->getParameter(), '$' . $property->getName())) {
        //         list($type) = explode(' ', $parameterReflection->getParameter());
        //         $classString = $type;
        //         break;
        //     }
        // }

        return $classString;
    }

    /**
     * @return string[]
     */
    public function types(null|ReflectionType $type): array
    {
        $types = [];

        if ($type instanceof ReflectionNamedType) {
            $types[] = $type->getName();
            if ($type->allowsNull() && $type->getName() !== 'null') {
                $types[] = 'null';
            }
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if (! $unionType instanceof ReflectionNamedType) {
                    continue;
                }

                $types[] = $unionType->getName();
            }
        }

        return $types;
    }

    /**
     * @throws Exception
     */
    public function resolve(string|object $object, ?LoggerInterface $logger = null): ObjectReflection
    {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw new InvalidArgumentException(sprintf('object %s does not exist', $object));
        }

        $constructor = [];
        $properties = [];
        $setters = [];

        $reflectionClass = new ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $properties[] = new PropertyReflection(
                $reflectionProperty->getName(),
                $this->types($reflectionProperty->getType()),
                $this->classString($reflectionProperty),
            );
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {

            if (str_starts_with($reflectionMethod->getName(), '__construct')) {
                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    $constructor[] = new PropertyReflection(
                        $reflectionParameter->getName(),
                        $this->types($reflectionParameter->getType()),
                        $this->classString($reflectionMethod),
                    );
                }
            }

            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                if (count($reflectionMethod->getParameters()) !== 1) {
                    if ($logger instanceof LoggerInterface) {
                        $logger->warning(
                            sprintf(
                                'Method %s has more than one parameter',
                                $reflectionMethod->getName(),
                            ),
                        );
                    }

                    continue;
                }

                $setters[] = new PropertyReflection(
                    $reflectionMethod->getName(),
                    $this->types($reflectionMethod->getParameters()[0]->getType()),
                    $this->classString($reflectionMethod),
                );
            }
        }

        return new ObjectReflection(
            $constructor,
            $properties,
            $setters,
        );
    }
}
