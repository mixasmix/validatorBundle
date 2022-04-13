<?php

class PassportData
{
    /**
     * @var string
     */
    private string $series;

    /**
     * @var string
     */
    private string $number;

    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $issueDate;

    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $birthDay;

    /**
     * @var string
     */
    private string $divisionCode;

    /**
     * @var string
     */
    private string $divisionName;

    /**
     * @var string
     */
    private string $fullName;

    /**
     * @param string            $series
     * @param string            $number
     * @param DateTimeImmutable $issueDate
     * @param DateTimeImmutable $birthDay
     * @param string            $divisionCode
     * @param string            $divisionName
     * @param string            $fullName
     */
    public function __construct(
        string $series,
        string $number,
        DateTimeImmutable $issueDate,
        DateTimeImmutable $birthDay,
        string $divisionCode,
        string $divisionName,
        string $fullName
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
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getIssueDate(): DateTimeImmutable
    {
        return $this->issueDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getBirthDay(): DateTimeImmutable
    {
        return $this->birthDay;
    }

    /**
     * @return string
     */
    public function getDivisionCode(): string
    {
        return $this->divisionCode;
    }

    /**
     * @return string
     */
    public function getDivisionName(): string
    {
        return $this->divisionName;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }
}
