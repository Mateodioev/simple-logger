<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;

use function SimpleLogger\pathJoin;

class HelpersTest extends TestCase
{
    public function testPathJoinCombinesTwoParts(): void
    {
        $result = pathJoin('/foo', 'bar');
        $this->assertSame('/foo/bar', $result);
    }

    public function testPathJoinCombinesMultipleParts(): void
    {
        $result = pathJoin('/a', 'b', 'c', 'd');
        $this->assertSame('/a/b/c/d', $result);
    }

    public function testPathJoinCollapsesDuplicateSlashes(): void
    {
        $result = pathJoin('/foo/', '/bar/');
        $this->assertSame('/foo/bar/', $result);
    }

    public function testPathJoinWithSinglePart(): void
    {
        $result = pathJoin('/only');
        $this->assertSame('/only', $result);
    }

    public function testPathJoinWithEmptyParts(): void
    {
        $result = pathJoin('', 'foo', 'bar');
        $this->assertSame('/foo/bar', $result);
    }

    public function testPathJoinWithTrailingSlash(): void
    {
        $result = pathJoin('/base/', 'file.log');
        $this->assertSame('/base/file.log', $result);
    }
}
