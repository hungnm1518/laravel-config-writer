<?php

namespace HungNM\LCW\Config\DataWriter;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use HungNM\LCW\Config\DataWriter\Rewrite;

class FileWriter
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The default configuration path.
     *
     * @var string
     */
    protected $defaultPath;

    /**
     * The config rewriter object.
     *
     * @var \HungNM\LCW\Config\DataWriter\Rewrite
     */
    protected $rewriter;

    /**
     * Create a new file configuration loader.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  string $defaultPath
     * @return void
     */
    public function __construct(Filesystem $files, string $defaultPath)
    {
        $this->files = $files;
        $this->defaultPath = $defaultPath;
        $this->rewriter = new Rewrite;
    }

    /**
     * Write an item value in a file.
     *
     * @param  string $item
     * @param  mixed $value
     * @param  string $filename
     * @return bool
     */
    public function write(string $key, $value, string $path = '', string $fileExtension = '.php'): bool
    {
        [$item, $file] = $this->splitFileFromItem($key, $path, $fileExtension);

        if (!$file) return false;

        $contents = $this->files->get($file);
        $contents = $this->rewriter->toContent($contents, [$item => $value]);

        return !($this->files->put($file, $contents) === false);
    }

    private function splitFileFromItem(string $item, string $filename, string $ext = '.php'): array
    {

        $file = "{$this->defaultPath}/{$filename}{$ext}";

        if ($this->files->exists($file) && $this->hasKey($file, $item)) {
            return [$item, $file];
        }

        if($this->files->isDirectory("{$this->defaultPath}/{$filename}")){
            list($newFilename, $newItem) = $this->parseKey($item);
            return $this->splitFileFromItem($newItem, $filename . '/' . $newFilename, $ext);
        }

        throw new FileNotFoundException('Config file does not exists!');
    }

    private function hasKey(string $path, string $key): bool
    {
        $contents = file_get_contents($path);
        $vars = eval('?>'.$contents);

        $keys = explode('.', $key);

        $isset = false;
        while ($key = array_shift($keys)) {
            $isset = isset($vars[$key]);
            if (is_array($vars[$key])) $vars = $vars[$key];
        }

        return $isset;
    }

    /**
     * Split key into 2 parts. The first part will be the filename
     *
     * @param string $key
     * @return array
     */
    private function parseKey(string $key): array
    {
        return preg_split('/\./', $key, 2);
    }
}