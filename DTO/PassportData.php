<?php

namespace Mixasmix\ValidationBundle\DTO;

use DateTimeImmutable;

class PassportData
{
    /**
     * @var string | null
     */
    private ?string $series;

    /**
     * @var string | null
     */
    private ?string $number;

    /**
     * @var DateTimeImmutable | null
     */
    private ?DateTimeImmutable $issueDate;

    /**
     * @var DateTimeImmutable | null
     */
    private ?DateTimeImmutable $birthDay;

    /**
     * @var string | null
     */
    private ?string $divisionCode;

    /**
     * @var string | null
     */
    private ?string $divisionName;

    /**
     * @var string | null
     */
    private ?string $fullName;

    /**
     * @param string | null            $series
     * @param string | null            $number
     * @param DateTimeImmutable | null $issueDate
     * @param DateTimeImmutable | null $birthDay
     * @param string | null            $divisionCode
     * @param string | null            $divisionName
     * @param string | null            $fullName
     */
    public function __construct(
        ?string $series,
        ?string $number,
        ?DateTimeImmutable $issueDate,
        ?DateTimeImmutable $birthDay,
        ?string $divisionCode,
        ?string $divisionName,
        ?string $fullName
    ) {
        $this->series = $series;
        $this->number = $number;
        $this->issueDate = $issueDate;
        $this->birthDay = $birthDay;
        $this->divisionCode = $divisionCode;
        $this->divisionName = $divisionName;
        $this->fullName = $fullName;
    }

    /**
     * @return string | null
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @return string | null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return DateTimeImmutable | null
     */
    public function getIssueDate(): ?DateTimeImmutable
    {
        return $this->issueDate;
    }

    /**
     * @return DateTimeImmutable | null
     */
    public function getBirthDay(): ?DateTimeImmutable
    {
        return $this->birthDay;
    }

    /**
     * @return string | null
     */
    public function getDivisionCode(): ?string
    {
        return $this->divisionCode;
    }

    /**
     * @return string | null
     */
    public function getDivisionName(): ?string
    {
        return $this->divisionName;
    }

    /**
     * @return string | null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }
}
