<?php

require "FileSystem.php";

$fs = new FileSystem();

$fs->mkdir('usr');
$fs->cd('usr');
$fs->mkdir('local');
$fs->cd('local');
echo $fs->pwd() . PHP_EOL;

$fs->cd('..');
$fs->mkdir('share');
$fs->mkdir('share/info');
$fs->cd('share/info');
echo $fs->pwd() . PHP_EOL;


$fs->mkdir('/usr/local/log');
$fs->cd('/usr/local/log');
echo $fs->pwd() . PHP_EOL;

$fs->mkdir('some/folder');

$fs->cd('/usr/unknown/folder');
// $fs->printPaths();