<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

class Boolean extends AbstractConstraint
{
    /**
     * @var string
     */
    public string $message = 'Значение должно быть булева типа или конвертируемого в булев тип значения';
}
