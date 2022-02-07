<?php

class FileSystem
{
	private const PARENT_DIRECTORY = '..';
	private const SELF_DIRECTORY = '.';
	private const DIR_SEPARATOR = '/';
	private $currentPath;
	private $paths;

	function __construct()
	{
		$this->currentPath = '';
		$this->paths =  ['' => ['parent' => '']];
	}

	private function isAbsolute($path)
	{
		return (strpos($path, '/') === 0);
	}

	private function getAbsolutePath($path)
	{
		$currentPath = strlen($this->currentPath) == 0 ? self::DIR_SEPARATOR : self::DIR_SEPARATOR . "{$this->currentPath}" . self::DIR_SEPARATOR;

		return $this->isAbsolute($path) ? $path : "{$currentPath}{$path}";
	}

	private function resolvePath($path)
	{

		$parts = [];

		foreach (explode('/', $path) as $part) {

			switch ($part) {

				case '':
				case self::SELF_DIRECTORY:
					break;

				case self::PARENT_DIRECTORY:
					array_pop($parts);
					break;

				default:
					$parts[] = $part;
					break;
			}
		}

		return implode('/', $parts);
	}

	private function getPath($path)
	{
		$absolutePath = $this->getAbsolutePath($path);

		return $this->resolvePath($absolutePath);
	}

	public function cd($path)
	{
		$requiredPath = $this->getPath($path);

		if (!isset($this->paths[$requiredPath])) {

			echo "cd: {$path} does not exist" . PHP_EOL;
			return false;
		}

		$this->currentPath = $requiredPath;

		return true;
	}

	public function pwd()
	{
		return (strlen($this->currentPath) === 0) ? self::DIR_SEPARATOR : self::DIR_SEPARATOR . "{$this->currentPath}";
	}

	private function hasDirectory($dirname)
	{
		return isset($this->paths[$dirname]);
	}

	private function isValidDirectoryName($name)
	{
		return preg_match('/^[a-zA-Z]+$/', $name);
	}

	private function isValidPath($path)
	{
		return preg_match('/^(\/([a-zA-Z]+|\.{1,2}))+\/?$/', $path) === 1;
	}

	public function mkdir($path)
	{

		$absolutePath = $this->getAbsolutePath($path);

		if (!$this->isValidPath($absolutePath)) {

			echo "mkdir: Invalid path name, cannot create directory" . PHP_EOL;
			return false;
		}

		$dirPath = $this->resolvePath($absolutePath);
		$parts = explode('/', $dirPath);
		$newDirectory = array_pop($parts);
		$parentDirectory = implode('/', $parts);

		if (!$this->isValidDirectoryName($newDirectory)) {

			echo "mkdir: Invalid name, cannot create directory" . PHP_EOL;
			return false;
		}

		if ($this->hasDirectory($dirPath)) {
			echo "mkdir: Directory exists, cannot create directory" . PHP_EOL;

			return false;
		}

		if (!$this->hasDirectory($parentDirectory)) {
			echo "mkdir: directory not found {$parentDirectory}, cannot create directory" . PHP_EOL;

			return false;
		}

		$this->paths[$dirPath] = ['parent' => $parentDirectory];
		echo "mkdir: Directory {$newDirectory} was created" . PHP_EOL;

		return true;
	}

	private function deleteDirectory($path)
	{
		unset($this->paths[$path]);
	}

	public function rmdir($path)
	{

		$dirPath = $this->getPath($path);

		if (empty($dirPath)) {
			echo "mkdir: Root folder, cannot be deleted" . PHP_EOL;

			return false;
		}

		if (!$this->hasDirectory($dirPath)) {
			echo "mkdir: Directory not found, cannot delete" . PHP_EOL;

			return false;
		}

		foreach ($this->paths as $path) {

			if ($path['parent'] == $dirPath) {
				echo "mkdir: Directory not empty, cannot delete" . PHP_EOL;

				return false;
			}
		}

		$this->deleteDirectory($dirPath);
		echo "mkdir: Directory  was deleted" . PHP_EOL;

		return true;
	}

	public function printPaths()
	{
		print_r($this->paths);
	}
}
