<?php

namespace TKusy\JSchema\JsonSchema;

use Symfony\Component\PropertyInfo\Type;
use TKusy\JSchema\Metadata\PropertyMetadata;
use TKusy\JSchema\Writer\FileWriter;
use TKusy\JSchema\Writer\WriterInterface;

class Generator
{
    private $idPrefix;

    private $schemas;
    /**
     * @var string
     */
    private $rootNamespace;
    /**
     * @var string
     */
    private $pathTemplate;
    /**
     * @var FileWriter
     */
    private $writer;

    public function __construct(WriterInterface $writer, $config)
    {
        $this->writer = $writer;
        $this->rootNamespace = $config['rootNamespace'];
        $this->idPrefix = $config['idPrefix'] ?? '';
        $this->pathTemplate = $config['destination']['pathTemplate'] ?? '%s.schema.json';
    }

    public function generate(array $allMetadata)
    {
        $result = null;
        $this->initSchemas($allMetadata);

        foreach ($allMetadata as $className => $metadata) {
            $jsonSchema = $this->processSingleClass($className, $metadata);
            [$schemaId, $schema] = $this->writer->write($jsonSchema);
            if ($schemaId) {
                $result[$schemaId] = $schema;
            }
        }
        return $result;
    }

    private function initSchemas(array $allMetadata)
    {
        foreach (array_keys($allMetadata) as $key) {
            $name = preg_replace('/^' . preg_quote($this->rootNamespace, '/') . '/', '', $key);
            $name = str_replace('\\', '.', $name);
            $this->schemas[$key] = $name;
        }
    }

    /**
     * @param string $className
     * @param PropertyMetadata[] $metadata
     * @return string[]
     */
    private function processSingleClass(string $className, array $metadata): array
    {
        $jsonSchema = $this->getHeader($className);
        foreach ($metadata as $property => $propertyMetadata) {
            $jsonSchema['properties'][$property] = $this->getType($propertyMetadata->getType());
            if ($propertyMetadata->getType()[0]->isNullable()) {
                continue;
            }
            $required[] = $property;
        }
        if (!empty($required)) {
            $jsonSchema['required'] = $required;
        }
        return $jsonSchema;
    }

    private function getType(array $types): array
    {
        if (count($types) === 1) {
            return $this->getFinallType(current($types));
        }

        foreach ($types as $type) {
            $possibleTypes[] = $this->getFinallType($type);
        }

        return ['anyOf' => $possibleTypes];
    }

    private function getFinallType(Type $type)
    {
        if ($type->getBuiltinType() === 'object' && !empty($type->getClassName())) {
            return ['$ref' => $this->className2Id($type->getClassName())];
        }

        if ($type->getBuiltinType() === 'array' && $type->getCollectionValueType() !== null) {
            return [
                'type' => 'array',
                'items' => $this->getFinallType($type->getCollectionValueType())
            ];
        }

        switch ($type->getBuiltinType()) {
            case 'bool':
                return ['type' => 'boolean'];
            case 'int':
                return ['type' => 'integer'];
            case 'float':
            case 'double':
                return ['type' => 'number'];
            default:
                return ['type' => $type->getBuiltinType()];
        }
    }

    private function getHeader(string $className): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            '$id' => $this->className2Id($className),
            '$comment' => 'Generated with tomasz-kusy/annotations-to-json-schema',
            'type' => 'object',
        ];
    }

    public function className2Id(string $className): string
    {
        $relative = preg_replace(
            ['#' . preg_quote($this->rootNamespace, '#') . '#', '#\\\\\\\\#', '#\\\\#'],
            ['', '\\', '/'],
            $className
        );
        return $this->idPrefix . sprintf($this->pathTemplate, $relative);
    }
}
