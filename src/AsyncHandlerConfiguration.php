<?php
namespace Alesinicio\AsyncExecutor;

readonly class AsyncHandlerConfiguration {
	public function __construct(
		public string $handlerPath = 'php',
	) {}
}