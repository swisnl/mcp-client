<?php

namespace Swis\McpClient\Requests;

interface RequestInterface extends \JsonSerializable
{
    public function getMethod(): string;

    public function getId(): string;
}
