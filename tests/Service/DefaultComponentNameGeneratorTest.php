<?php

namespace Tito10047\UX\Sdc\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tito10047\UX\Sdc\Service\DefaultComponentNameGenerator;

class DefaultComponentNameGeneratorTest extends TestCase
{
    public function testGenerateNameWithDefaults(): void
    {
        $generator = new DefaultComponentNameGenerator('App\\Component\\');
        $this->assertEquals('layout:footer', $generator->generateName('App\\Component\\Layout\\Footer'));
    }

    public function testGenerateNameWithCustomSeparator(): void
    {
        $generator = new DefaultComponentNameGenerator('App\\Component\\', '/');
        $this->assertEquals('layout/footer', $generator->generateName('App\\Component\\Layout\\Footer'));
    }

    public function testGenerateNameWithoutLowercase(): void
    {
        $generator = new DefaultComponentNameGenerator('App\\Component\\', ':', false);
        $this->assertEquals('Layout:Footer', $generator->generateName('App\\Component\\Layout\\Footer'));
    }

    public function testGenerateNameWithNoNamespaceMatch(): void
    {
        $generator = new DefaultComponentNameGenerator('App\\Component\\');
        $this->assertNull($generator->generateName('Other\\Namespace\\Component'));
    }

    public function testGenerateNameWithNullNamespace(): void
    {
        $generator = new DefaultComponentNameGenerator(null);
        $this->assertNull($generator->generateName('App\\Component\\Layout\\Footer'));
    }
}
