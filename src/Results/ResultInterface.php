<?php

namespace Swis\McpClient\Results;

interface ResultInterface extends \JsonSerializable
{
    /**
     * Get the request ID associated with this result
     *
     * @return string
     */
    public function getRequestId(): string;

    public function toArray(): array;

    public static function fromArray(array $data, string $requestId): self;
}
