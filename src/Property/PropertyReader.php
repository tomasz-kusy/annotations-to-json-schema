<?php

namespace TKusy\JSchema\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use TKusy\JSchema\Metadata\PropertyMetadata;

class PropertyReader
{
    private $properties = [];

    /**
     * @var PropertyInfoExtractor
     */
    private $propertyInfoExtractor;

    public function __construct()
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $this->propertyInfoExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor],
            [$phpDocExtractor],
            [$reflectionExtractor],
            [$reflectionExtractor]
        );
    }

    /**
     * @param $className
     * @return array
     * @throws \ReflectionException
     */
    public function getPropertiesTree(string $className)
    {
        return $this->readRecursive(
            $this->getClassMetadata($className)
        );
    }

    /**
     * @param string $className
     * @return PropertyMetadata[]
     * @throws \ReflectionException
     */
    public function getClassMetadata(string $className)
    {
        if (isset($this->properties[$className])) {
            return $this->properties[$className];
        }

//        $this->validatorMetadata($className, $this->properties[$className] = []);

//        $reader = new AnnotationReader();

        $reflectedClass = new \ReflectionClass($className);

        foreach ($reflectedClass->getProperties() as $property) {
            $this->properties[$className][$property->getName()] = $metadata = new PropertyMetadata();

            //$reader->getPropertyAnnotations($property);

            $metadata->setThisClass($className);
            $metadata->setName($property->getName());
            $metadata->setType($this->propertyInfoExtractor->getTypes($className, $property->getName()));
        }

        return $this->properties[$className];
    }

    /**
     * @param PropertyMetadata[] $metaData
     * @return array
     * @throws \ReflectionException
     */
    private function readRecursive(array $metaData)
    {
        foreach ($metaData as $propertyMetadata) {
            foreach ($propertyMetadata->getType() as $type) {
                if (
                    isset($this->properties[$type->getClassName()])
                    || ($type->getBuiltinType() !== 'object' && $type->getBuiltinType() !== 'array')
                ) {
                    continue;
                }
                if ($type->getBuiltinType() === 'array') {
                    if (
                        !$type->isCollection()
                        || $type->getCollectionValueType() === null
                        || $type->getCollectionValueType()->getBuiltinType() !== 'object'
                    ) {
                        continue;
                    }
                    $meta = $this->getClassMetadata($type->getCollectionValueType()->getClassName());
                } else {
                    $meta = $this->getClassMetadata($type->getClassName());
                }
                $this->readRecursive($meta);
            }
        }
        return $this->properties;
    }
    /**
     * @param string $className
     * @param PropertyMetadata[] $metadata
     */
    private function validatorMetadata(string $className, array $metadata): void
    {
        $validatorData = (new LazyLoadingMetadataFactory(new AnnotationLoader(new AnnotationReader())))
            ->getMetadataFor($className);

        foreach ($metadata as $property => $meta) {
            $meta->setConstraints($validatorData->getPropertyMetadata($property));
        }
    }
}
