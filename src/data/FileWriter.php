<?php

declare (strict_types=1);

namespace SimpleLogger\data;

interface FileWriter
{
    public function write(string $content): void;
}
