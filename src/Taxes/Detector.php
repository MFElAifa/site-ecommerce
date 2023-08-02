<?php 

namespace App\Taxes;

class Detector
{

    public function __construct(protected float $seuil)
    {
        
    }

    public function detect(float $prix): bool
    {
        return $prix > $this->seuil;
    }
}