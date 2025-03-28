<?php

namespace Swis\McpClient\Schema;

enum Role: string
{
    case ASSISTANT = 'assistant';
    case USER = 'user';
}
