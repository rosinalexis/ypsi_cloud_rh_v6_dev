<?php

namespace App\Entity\Enums;

enum Gender:string{
    case Monsieur  = 'M';
    case Madame ='Mme';
    case Mademoiselle ='Mlle';
    case Autre ='Autre';
}