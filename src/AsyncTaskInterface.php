<?php

namespace Alesinicio\AsyncExecutor;

interface AsyncTaskInterface {
	public function handle(AsyncTaskDTO $dto) : void;
}