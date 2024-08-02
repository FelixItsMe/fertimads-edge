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

    public function __construct($threshold = 1.1)
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

        return $distance <= $this->threshold;
    }

    public function getDistance($string1, $string2)
    {
        $hash1 = $this->calculateSimhash($string1);
        $hash2 = $this->calculateSimhash($string2);

        $distance = $this->comparator->compare($hash1, $hash2);

        return $distance;
    }
}
