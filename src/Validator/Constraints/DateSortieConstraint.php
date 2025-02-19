<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute; // Importez l'attribut

/**
 * @Annotation
 */
#[Attribute] // Déclarez cette classe comme un attribut
class DateSortieConstraint extends Constraint
{
    public $message = 'La date de sortie doit être postérieure ou égale à la date d\'entrée.';
}