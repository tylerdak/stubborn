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
     * The regex string that finds replace candidates.
     *
     * @var string
     */
    protected static string $_searchRegex = "/{{\s(?<key>[\S-]+?)(?<mod>(?:\:\:(?:\w+))+)?\s}}/m";

    /**
     * Holds content.
     *
     * @var string
     */
    protected ?string $contentBuffer = null;

    protected static function modFunctions() {
        return [
            'lower' => fn ($value) => strtolower($value),
        ];
    }

    /**
     * Set stub path.
     */
    public static function from(string $path): static
    {
        $new = new self();
        $new->from = $path;

        return $new;
    }

    protected static function tryModFunction(string $mod, string $value): string
    {
        $mod = strtolower($mod);
        if ($todo = static::modFunctions()[$mod] ?? null) {
            return $todo($value);
        }
        return $value;
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

    public function matches(): array
    {
        $this->loadContentFromSource();

        $matches = [];
        preg_match_all(static::$_searchRegex, $this->contentBuffer, $matches, PREG_SET_ORDER);

        $matchLib = [];

        foreach($matches as $index => $match) {
            $toAdd = [
                'match' => $match[0],
                'token' => $match['key'] . ($match['mod'] ?? null)
            ];

            if (! $key = $match['key'] ?? null) {
                continue;
            }
            if (! array_key_exists($key, $this->replaces)) {
                continue;
            }
            if (! is_array($matchLib[$key] ?? null)) {
                $matchLib[$key] = [$toAdd];
                continue;
            }
            $matchLib[$key][] = $toAdd;
        }

        return $matchLib;
    }

    public function loadContentFromSource(bool $cached = true): string
    {
        if ($this->contentBuffer && $cached) {
            return $this->contentBuffer;
        }

        $this->validateSource();

        return ($this->contentBuffer = file_get_contents($this->from));
    }

    /**
     * Generate stub file.
     */
    public function generate(): bool
    {
        // Check destination path is valid
        if (! is_dir($this->to)) {
            throw new RuntimeException('The given folder path is not valid.');
        }

        // Validates src path and reads content
        $this->loadContentFromSource(cached:false);

        $results = $this->matches();

        // Replace variables
        foreach ($this->replaces as $search => $value) {
            if ($matches = $results[$search] ?? null) {
                foreach($matches as $match) {
                    $toReplace = $match['match'];

                    $mods = array_slice(
                        explode('::',$match['token']),
                        1
                    );

                    foreach($mods as $mod) {
                        $value = static::tryModFunction($mod,$value);
                    }

                    $this->contentBuffer = str_replace($toReplace, $value, $this->contentBuffer);
                }
            }
        }

        // Get correct path
        $path = $this->getPath();

        // Move file
        copy($this->from, $path);

        // Put content and write on file
        file_put_contents($path, $this->contentBuffer);

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

    private function validateSource(): void {
        // Check path is valid
        if (! file_exists($this->from)) {
            throw new RuntimeException('The stub file does not exist, please enter a valid path.');
        }
    }

}
