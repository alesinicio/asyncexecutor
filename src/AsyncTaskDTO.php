<?php

namespace alesinicio\AsyncExecutor;

readonly class AsyncTaskDTO {
	public function __construct(
		public string $class,
		public array  $args = [],
	) {}
	public static function fromArray(array $input) : ?self {
		$class = $input['class'] ?? null;
		if (!is_string($class) || !class_exists($class)) return null;

		$args = $input['args'] ?? [];
		if (!is_array($args)) return null;

		return new self($class, $args);
	}
	public function asArray() : array {
		return (array)$this;
	}
}