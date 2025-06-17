<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

final readonly class ToArray
{
    public function __construct(
        private string $string,
    ) {
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'string' => $this->string,
        ];
    }
}
