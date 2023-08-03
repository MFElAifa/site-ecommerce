<?php 

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransformer implements DataTransformerInterface
{
    public function transform(mixed $value){
        return $value ? $value/100 : null;
    }

    public function reverseTransform(mixed $value){
        return $value ? $value*100 : null;
    }
}