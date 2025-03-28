<?php

namespace Swis\McpClient;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Simple implementation of the PSR-14 event dispatcher.
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string, array<callable>> Event listeners indexed by event type
     */
    private array $listeners = [];

    /**
     * Add an event listener
     *
     * @param string $eventType The event type (class name)
     * @param callable $listener The listener callback
     */
    public function addListener(string $eventType, callable $listener): void
    {
        if (! isset($this->listeners[$eventType])) {
            $this->listeners[$eventType] = [];
        }

        $this->listeners[$eventType][] = $listener;
    }

    /**
     * Remove an event listener
     *
     * @param string $eventType The event type (class name)
     * @param callable $listener The listener callback
     */
    public function removeListener(string $eventType, callable $listener): void
    {
        if (! isset($this->listeners[$eventType])) {
            return;
        }

        $this->listeners[$eventType] = array_filter(
            $this->listeners[$eventType],
            function ($existingListener) use ($listener) {
                return $existingListener !== $listener;
            }
        );
    }

    /**
     * Remove all listeners
     */
    public function removeAllListeners(): void
    {
        $this->listeners = [];
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event): object
    {
        $eventType = get_class($event);

        if (isset($this->listeners[$eventType])) {
            foreach ($this->listeners[$eventType] as $listener) {
                $listener($event);
            }
        }

        return $event;
    }
}
