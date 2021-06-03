<?php
use alesinicio\AsyncExecutor\AsyncExecutor;
use alesinicio\AsyncExecutor\AsyncMultiProcess;
use alesinicio\AsyncExecutor\AsyncProcess;

require __DIR__.'/../vendor/autoload.php';

//INITIALIZE THE EXECUTOR
$async = new AsyncExecutor('/usr/bin/php');

//INITIALIZE THE MULTI-INSTANCE WRAPPER
$multiAsync	= new AsyncMultiProcess($async);

//ADD PROCESSES TO BE MANAGED BY THE MULTI-INSTANCE HANDLER
$multiAsync->addProcess(new AsyncProcess('instance_01', __DIR__.'/bg_process.php', ['param01']));
$multiAsync->addProcess(new AsyncProcess('instance_02', __DIR__.'/bg_process.php', ['param02', 'param03']));

//RUN THE SCRIPTS FOREVER
$multiAsync->keepRunningProcesses();