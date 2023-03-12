<?php

namespace App\Validator;

use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IngredientExistValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint IngredientExist */
        // On laisse NotBlank, NotNull, etc...s'occuper de valider ce type d'erreur
        if (null === $value || '' === $value) {
            return;
        }
        if ($this->entityManager->getRepository(Ingredient::class)->count(['name' => $value]))
            $this->context->buildViolation($constraint->message)->addViolation();
    }
}