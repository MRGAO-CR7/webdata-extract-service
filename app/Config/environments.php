<?php

CakePlugin::load('Environments');
App::uses('Environment', 'Environments.Lib');

include dirname(__FILE__).DS.'environments'.DS.'development.php';
include dirname(__FILE__).DS.'environments'.DS.'gcp.php';

Environment::start();
