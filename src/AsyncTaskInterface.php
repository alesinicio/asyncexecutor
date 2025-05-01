<?php

namespace alesinicio\AsyncExecutor;

interface AsyncTaskInterface {
	public function handle(AsyncTaskDTO $dto) : void;
}