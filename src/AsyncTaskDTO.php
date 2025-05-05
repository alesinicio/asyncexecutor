<?php

namespace Alesinicio\AsyncExecutor;

use DateInterval;
use DateMalformedIntervalStringException;

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
		if (is_array($ttl)) $ttl = static::createDateIntervalFromArray($ttl);

		return new self($class, $args, $ttl);
	}
	private static function createDateIntervalFromArray(array $data) : ?DateInterval {
		$y = $data['y'] ?? 0;
		$m = $data['m'] ?? 0;
		$d = $data['d'] ?? 0;
		$h = $data['h'] ?? 0;
		$i = $data['i'] ?? 0;
		$s = $data['s'] ?? 0;

		$invert       = $data['invert'] ?? 0;
		$intervalSpec = 'P' . $y . 'Y' . $m . 'M' . $d . 'DT' . $h . 'H' . $i . 'M' . $s . 'S';
		try {
			$interval = new DateInterval($intervalSpec);
		} catch (DateMalformedIntervalStringException) {
			return null;
		}

		$interval->invert = $invert;

		return $interval;
	}
	public function asArray() : array {
		return (array)$this;
	}
}