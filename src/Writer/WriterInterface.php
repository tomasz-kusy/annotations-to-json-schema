<?php

namespace TKusy\JSchema\Writer;

interface WriterInterface
{
    public function write(array $jsonSchema): array;
}
