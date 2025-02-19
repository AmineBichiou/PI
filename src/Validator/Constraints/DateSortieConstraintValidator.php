<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateSortieConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DateSortieConstraint) {
            throw new UnexpectedTypeException($constraint, DateSortieConstraint::class);
        }

        // Récupère l'objet Stock en cours de validation
        $stock = $this->context->getObject();

        // Vérifie si la date de sortie est définie et si elle est antérieure à la date d'entrée
        if ($stock->getDateSortie() !== null && $stock->getDateSortie() < $stock->getDateEntree()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('date_sortie') // Associe l'erreur au champ date_sortie
                ->addViolation();
        }
    }
}