<?php

namespace FSVendor\Octolize\Csat;

class CsatCodeFromString implements CsatCode
{
    private string $code;
    public function __construct(string $code)
    {
        $this->code = $code;
    }
    public function get_csat_code()
    {
        return $this->code;
    }
}
