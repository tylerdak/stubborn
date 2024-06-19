<?php

namespace Dakin\Stubborn;

use Dakin\Stubborn\Support\Str;
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
    protected ?string $to = null;

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
    protected array $replaces = [];

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

    protected static ?string $stubFolder = null;
    protected static ?string $contextFolder = null;

    public static array $modFunctions = [];

    /**
     * Set stub path.
     */
    public static function from(string $path): static
    {
        $stub = new self();
        if (static::$stubFolder) {
            $stub->from = static::$stubFolder . DIRECTORY_SEPARATOR . $path;
        }
        else {
            $stub->from = $path;
        }

        return $stub->setImpliedProperties($path);
    }

    protected static function applyModifier(string $mod, string $value): string
    {
        $mod = strtolower($mod);
        // first, try to find a mod in static::modFunctions
        if ($todo = static::$modFunctions[$mod] ?? null) {
            return $todo($value);
        }
        // if that doesn't work, fallback to Str
        if (method_exists(Str::class,$mod)) {
            return Str::{$mod}($value);
        }
        return $value;
    }

    /**
     * Set stub destination path.
     */
    public function to(?string $to = null): static
    {
        if (static::contextFolder()) {
            if (!$this->to) {
                $this->to = static::contextFolder();
            }
            $this->to = static::contextFolder() . DIRECTORY_SEPARATOR . $to;
        }
        else {
            $this->to = $to;
        }

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
    public function ext(?string $ext = null): static
    {
        if (!$ext) {
            $this->ext = null;
        }
        else {
            $this->ext = $ext;
        }
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
            throw new RuntimeException('The given folder path is not valid. ' . "({$this->to})");
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
                        $value = static::applyModifier($mod,$value);
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
        $path = $this->to . DIRECTORY_SEPARATOR . $this->name;

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

    public function setImpliedProperties(string $path) {
        if (static::contextFolder()) {
            $extension = null;
            $parts = explode('.',$path,2);
            $path = $parts[0];
            if (count($parts) > 1) { $extension = $parts[1]; }
            $toFolder = static::contextFolder() . DIRECTORY_SEPARATOR . $path;
            if (!is_dir($toFolder)) {
                $path = null;
            }
            return $this->to($path)->ext($extension);
        }
        return $this;
    }

    /**
     * Sets the statically stored stub folder to make ::from calls less verbose.
     *
     * @param string $path The path where Stubborn should expect your stubs to be.
     * @return bool Success/Failure flag
     */
    public static function setStubFolder($path, bool $safe = true): bool {
        if ($safe && ! is_dir($path)) {
            return false;
        }
        static::$stubFolder = $path;
        return (bool)(static::$stubFolder);
    }

    /**
     * Resets the statically stored stub folder.
     *
     * @return bool Success/Failure flag
     */
    public static function resetStubFolder(): bool {
        return (static::$stubFolder = null) === null;
    }

    /**
     * Returns the statically stored stub folder.
     *
     * @return ?string
     */
    public static function stubFolder(): ?string {
        return static::$stubFolder;
    }

    /**
     * Sets the statically stored stub folder to make ::from calls less verbose.
     *
     * @param string $path The path where Stubborn should expect your stubs to be.
     * @return bool Success/Failure flag
     */
    public static function setContextFolder($path, bool $safe = true): bool {
        if ($safe && !is_dir($path)) {
            return false;
        }
        static::$contextFolder = $path;
        return (bool)(static::$contextFolder);
    }

    /**
     * Resets the statically stored context folder.
     *
     * @return bool Success/Failure flag
     */
    public static function resetContextFolder(): bool {
        return (static::$contextFolder = null) === null;
    }

    /**
     * Returns the statically stored context folder.
     *
     * @return ?string
     */
    public static function contextFolder(): ?string {
        return static::$contextFolder;
    }

}
