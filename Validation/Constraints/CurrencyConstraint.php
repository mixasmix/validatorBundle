<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

final class CurrencyConstraint extends AbstractConstraint
{
    /**
     * @var string | null
     */
    public ?string $currency;

    /**
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $this->currency = $options;
        } elseif (is_array($options)) {
            $this->currency = $options['currency'] ?? null;
        } else {
            $this->currency = null;
        }

        parent::__construct($options);
    }
}
