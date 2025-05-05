<?php

namespace Alesinicio\AsyncExecutor;

use DateInterval;

readonly class AsyncTaskDTO {
	public function __construct(
		public string        $class,
		public array         $args = [],
		public ?DateInterval $ttl = null,
	) {}
	public static function fromArray(array $input) : ?self {
		$class = $input['class'] ?? null;
		if (!is_string($class) || !class_exists($class)) return null;

		$args = $input['args'] ?? [];
		if (!is_array($args)) return null;

		$ttl = $input['ttl'] ?? null;
		if ($ttl && (!$ttl instanceof DateInterval)) return null;

		return new self($class, $args, $ttl);
	}
	public function asArray() : array {
		return (array)$this;
	}
}