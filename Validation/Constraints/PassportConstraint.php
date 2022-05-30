<?php

namespace Mixasmix\ValidationBundle\Validation\Constraints;

class PassportConstraint extends AbstractConstraint
{
    /**
     * Название паратмера
     */
    protected const NAME = 'Паспорт';

    /**
     * @var string | null
     */
    public ?string $series;

    /**
     * @var string | null
     */
    public ?string $issueDate;

    /**
     * @var string | null
     */
    public ?string $birthDay;

    /**
     * @var string | null
     */
    public ?string $divisionCode;

    /**
     * @var string | null
     */
    public ?string $divisionName;

    /**
     * @var string | null
     */
    public ?string $fullName;

    /**
     * @var string | null
     */
    public ?string $number;

    /**
     * Название полей по умолчанию:
     * ФИО - full_name
     * Дата выдачи - issue_date
     * День рождения - birth_day
     * Код подразделения - division_code
     * Наименование подразделения - division_name
     * Серия - series
     * Номер - number
     * Правило назначается к любому филду структуры паспорта
     *
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        if (empty($options)) {
            $this->fullName = null;
            $this->issueDate = null;
            $this->birthDay = null;
            $this->divisionCode = null;
            $this->divisionName = null;
            $this->series = null;
            $this->number = null;
        } else {
            $this->fullName = $options['full_name'] ?? null;
            $this->issueDate = $options['issue_date'] ?? null;
            $this->birthDay = $options['birth_day'] ?? null;
            $this->divisionCode = $options['division_code'] ?? null;
            $this->divisionName = $options['division_name'] ?? null;
            $this->series = $options['series'] ?? null;
            $this->number = $options['number'] ?? null;
        }

        parent::__construct($options);
    }
}
