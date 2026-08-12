<?php

/*
 * This file is part of the UX SDC Bundle
 *
 * (c) Jozef Môstka <https://github.com/tito10047/ux-sdc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tito10047\UX\Sdc\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class SdcAsset
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public ?string $path = null,
        public ?string $type = null,
        public int $priority = 0,
        public array $attributes = [],
    ) {
    }
}
