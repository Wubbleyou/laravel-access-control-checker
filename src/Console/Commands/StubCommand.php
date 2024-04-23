<?php

namespace Wubbleyou\AccessControlChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class StubCommand extends Command {
    protected $path;
    protected $fileLocation;
    protected $fileName;
    protected $stubFileName;
    protected $stubVariables = [];

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->path = base_path() . '/' . $this->fileLocation;
    }

    /**
     * Run the command.
     */
    public function generateStub()
    {
        $this->makeDirectory();
        return $this->makeFile();
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @return string
     */
    protected function makeDirectory(): string
    {
        if (!$this->files->isDirectory($this->path)) {
            $this->files->makeDirectory($this->path, 0777, true, true);
        }

        return $this->path;
    }

    /**
     * Generate the file from the stub and save it to the directory
     *
     * @param  string  $path
     * @return string
     */
    public function makeFile(): bool
    {
        $contents = $this->getStubContents();

        if(!$this->files->exists($this->path . '/' . $this->fileName . '.php')) {
            $this->files->put($this->path . '/' . $this->fileName . '.php', $contents);
            return true;
        }

        return false;
    }

    public function getStubContents()
    {
        $contents = file_get_contents($this->getStubPath());

        foreach ($this->stubVariables as $search => $replace)
        {
            $contents = str_replace('$'.$search.'$' , $replace, $contents);
        }

        return $contents;
    }

    public function getStubPath(): string
    {
        return __DIR__ . '/../Stubs/' . $this->stubFileName . '.stub';
    }
}