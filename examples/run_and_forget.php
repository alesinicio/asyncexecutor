<?php
use alesinicio\AsyncExecutor\AsyncExecutor;

require __DIR__.'/../vendor/autoload.php';

//INITIALIZE THE EXECUTOR
$async = new AsyncExecutor('/usr/bin/php');

//RUN THE PROCESS
$pid = $async->runProcess(__DIR__.'/bg_process.php', ['param01', 'param02']);

//CHECK IF PROCESS IS RUNNING BY CHECKING ITS PID
var_dump($async->isProcessRunning($pid));