<?php

namespace FSVendor\Octolize\Csat;

class CsatCodeFromFile implements CsatCode
{
    private string $file_path;
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
    }
    public function get_csat_code(): string
    {
        if (!file_exists($this->file_path)) {
            throw new \RuntimeException('The file does not exist: ' . $this->file_path);
        }
        return file_get_contents($this->file_path);
    }
}
