<?php

namespace Dakin\Stubborn;

use RuntimeException;

class Stub
{
    /**
     * Stub path.
     *
     * @var string
     */
    protected string $from;

    /**
     * Stub destination path.
     *
     * @var string
     */
    protected string $to;

    /**
     * The new name of stub file.
     *
     * @var string
     */
    protected string $name;

    /**
     * The stub extension.
     *
     * @var string|null
     */
    protected string|null $ext;

    /**
     * The list of replaces.
     *
     * @var array
     */
    protected array $replaces;

    /**
     * Set stub path.
     */
    public static function from(string $path): static
    {
        $new = new self();
        $new->from = $path;

        return $new;
    }

    /**
     * Set stub destination path.
     */
    public function to(string $to): static
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Set new stub name.
     */
    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set stub extension.
     */
    public function ext(string $ext): static
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Set new replace with key and value.
     */
    public function replace(string $key, mixed $value): static
    {
        $this->replaces[$key] = $value;

        return $this;
    }

    /**
     * Set new replace with key and value.
     */
    public function replaces(array $replaces): static
    {
        foreach ($replaces as $key => $value) {
            $this->replaces[$key] = $value;
        }

        return $this;
    }

    /**
     * Generate stub file.
     */
    public function generate(): bool
    {
        // Check path is valid
        if (! file_exists($this->from)) {
            throw new RuntimeException('The stub file does not exist, please enter a valid path.');
        }

        // Check destination path is valid
        if (! is_dir($this->to)) {
            throw new RuntimeException('The given folder path is not valid.');
        }

        // Get file content
        $content = file_get_contents($this->from);

        // Replace variables
        foreach ($this->replaces as $search => $value) {
            $content = str_replace("{{ $search }}", $value, $content);
        }

        // Get correct path
        $path = $this->getPath();

        // Move file
        copy($this->from, $path);

        // Put content and write on file
        file_put_contents($path, $content);

        return true;
    }

    /**
     * Get final path.
     */
    private function getPath(): string
    {
        $path = "{$this->to}/{$this->name}";

        // Add extension
        if (! is_null($this->ext)) {
            $path .= ".$this->ext";
        }

        return $path;
    }
}
