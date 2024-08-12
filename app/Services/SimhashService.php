<?php

namespace App\Services;

use Tga\SimHash\Comparator\GaussianComparator;
use Tga\SimHash\Extractor\SimpleTextExtractor;
use Tga\SimHash\SimHash;

class SimhashService
{
    protected $simhash;
    protected $extractor;
    protected $comparator;
    protected $threshold;

    public function __construct($threshold = 0.00000005)
    {
        $this->simhash = new SimHash();
        $this->threshold = $threshold;
        $this->extractor = new SimpleTextExtractor();
        $this->comparator = new GaussianComparator(3);
    }

    public function calculateSimhash($string)
    {
        return $this->simhash->hash($this->extractor->extract($string));
    }

    public function isSimilar($string1, $string2)
    {
        $hash1 = $this->calculateSimhash($string1);
        $hash2 = $this->calculateSimhash($string2);


        $distance = $this->comparator->compare($hash1, $hash2);
        $float1_str = sprintf('%.8f', $distance);
        $float2_str = sprintf('%.8f', $this->threshold);

        return bccomp($float1_str, $float2_str, 14) === 1;
    }

    public function getDistance($string1, $string2)
    {
        $hash1 = $this->calculateSimhash($string1);
        $hash2 = $this->calculateSimhash($string2);

        $distance = $this->comparator->compare($hash1, $hash2);

        return $distance;
    }
}
