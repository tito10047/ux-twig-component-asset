<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\Tests\Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Tito10047\UX\Sdc\Attribute\AsSdcComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

class AsSdcComponentTest extends TestCase
{
    public function testInstantiate(): void
    {
        $attribute = new AsSdcComponent(
            path: 'css/style.css',
            type: 'css',
            priority: 5,
            attributes: ['defer' => true],
        );

        $this->assertSame('css/style.css', $attribute->path);
        $this->assertSame('css', $attribute->type);
        $this->assertSame(5, $attribute->priority);
        $this->assertSame(['defer' => true], $attribute->attributes);
    }

    public function testDefaultValues(): void
    {
        $attribute = new AsSdcComponent();

        $this->assertNull($attribute->path);
        $this->assertNull($attribute->type);
        $this->assertSame(0, $attribute->priority);
        $this->assertSame([], $attribute->attributes);
    }

    public function testIsRepeatable(): void
    {
        $reflection = new \ReflectionClass(AsSdcComponent::class);
        $phpAttributes = $reflection->getAttributes(\Attribute::class);
        $this->assertNotEmpty($phpAttributes);
        $attrInstance = $phpAttributes[0]->newInstance();
        $this->assertTrue((bool) ($attrInstance->flags & \Attribute::IS_REPEATABLE));
    }

    public function testDoesNotExtendAsTwigComponent(): void
    {
        $this->assertNotInstanceOf(AsTwigComponent::class, new AsSdcComponent());
    }
}
