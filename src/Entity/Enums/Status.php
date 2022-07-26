<?php

namespace App\Entity\Enums;

enum Status:string
{
    case Pending = 'Pending';
    case Succeed ='Succeed';
    case Failed ='Failed';
    case Completed='Completed';
}