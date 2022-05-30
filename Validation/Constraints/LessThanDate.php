<?php

namespace App\Validation\Constraints;

use DateTimeInterface;
use Mixasmix\ValidationBundle\Validation\Constraints\AbstractConstraint;

class LessThanDate extends AbstractConstraint
{
    /**
     * @var string
     */
    public string $message = 'Дата должна быть меньше {{ string }}';

    /**
     * @var DateTimeInterface
     */
    public DateTimeInterface $value;

    /**
     * @return string
     */
    public function getDefaultOption(): string
    {
        return 'value';
    }
}
