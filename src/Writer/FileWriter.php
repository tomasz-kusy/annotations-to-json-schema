<?php

namespace TKusy\JSchema\Writer;

use Symfony\Component\Filesystem\Filesystem;

class FileWriter implements WriterInterface
{
    /**
     * @var string
     */
    private $idPrefix;
    /**
     * @var string
     */
    private $destPath;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem, string $idPrefix, string $destPath)
    {
        $this->idPrefix = $idPrefix;
        $this->destPath = $destPath;
        $this->filesystem = $filesystem;
    }

    public function write(array $jsonSchema): array
    {
        $this->filesystem->dumpFile(
            $this->destPath . $this->getRelativePath($jsonSchema['$id']),
            json_encode($jsonSchema, JSON_PRETTY_PRINT)
        );
        return [null, null];
    }

    private function getRelativePath(string $schemaId): string
    {
        return preg_replace('#^' . preg_quote($this->idPrefix, '#') . '#', '', $schemaId);
    }
}
