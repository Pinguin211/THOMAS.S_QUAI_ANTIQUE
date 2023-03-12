<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class IngredientExist extends Constraint
{
    public string $message = 'Un ingredient avec se nom existe deja';
}