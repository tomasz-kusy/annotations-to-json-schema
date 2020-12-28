<?php

namespace TKusy\JSchema\Writer;

class ArrayWriter implements WriterInterface
{
    public function write(array $jsonSchema): array
    {
        return [$jsonSchema['$id'], json_encode($jsonSchema)];
    }
}
