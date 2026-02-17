<?php

namespace Swis\McpClient\Tests\Transporters;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Transporters\SseTransporter;

class SseTransporterHeadersTest extends TestCase
{
    public function testCustomHeadersAreIncludedInRequestAndSseConnectionHeaders(): void
    {
        $transporter = new class ('https://example.com/sse', null, null, ['Authorization' => 'Bearer test-token']) extends SseTransporter {
            public function requestHeaders(): array
            {
                return $this->getRequestHeaders();
            }

            public function sseConnectionHeaders(): array
            {
                return $this->getSseConnectionHeaders();
            }
        };

        $this->assertSame('Bearer test-token', $transporter->requestHeaders()['Authorization']);
        $this->assertSame('Bearer test-token', $transporter->sseConnectionHeaders()['Authorization']);
        $this->assertSame('application/json', $transporter->requestHeaders()['Content-Type']);
        $this->assertSame('application/json', $transporter->requestHeaders()['Accept']);
        $this->assertSame('text/event-stream', $transporter->sseConnectionHeaders()['Accept']);
        $this->assertSame('no-cache', $transporter->sseConnectionHeaders()['Cache-Control']);
    }

    public function testProtocolHeadersOverrideConflictingCustomHeaders(): void
    {
        $transporter = new class (
            'https://example.com/sse',
            null,
            null,
            [
                'Accept' => 'application/xml',
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'max-age=3600',
            ]
        ) extends SseTransporter {
            public function requestHeaders(): array
            {
                return $this->getDefaultHeaders();
            }

            public function sseConnectionHeaders(): array
            {
                return $this->getSseConnectionHeaders();
            }
        };

        $this->assertSame('application/json', $transporter->requestHeaders()['Accept']);
        $this->assertSame('application/json', $transporter->requestHeaders()['Content-Type']);
        $this->assertSame('text/event-stream', $transporter->sseConnectionHeaders()['Accept']);
        $this->assertSame('no-cache', $transporter->sseConnectionHeaders()['Cache-Control']);
    }
}
