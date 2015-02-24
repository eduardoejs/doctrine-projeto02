<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once '../config/bootstrap.php';

$entityManager = $em;
return ConsoleRunner::createHelperSet($entityManager);