<?php

namespace Mixasmix\ValidationBundle\Service;

use CurrencyValidationException;
use DivisionPassportException;
use EmptyValidationException;
use Mixasmix\ValidationBundle\Enum\Currency;
use IssuePassportException;
use LengthValidationException;
use DateTimeImmutable;
use NumberPassportException;
use PassportData;
use PatternValidationException;

class ValidationService
{
    /**
     * Массив коэффициентов для десятизначного ИНН
     */
    private const COEFFICIENT_INN10 = [2, 4, 10, 3, 5, 9, 4, 6, 8];

    /**
     * Массив коэффициентов для вычисления одинадцатого контрольного числа двенадцатизначного ИНН
     */
    private const COEFFICIENT_INN11 = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];

    /**
     * Массив коэффициентов для вычисления двенадцатого контрольного числа двенадцатизначного ИНН
     */
    private const COEFFICIENT_INN12 = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];

    /**
     * Массив коэффициентов для проверки корреспондентского счета
     */
    private const COEFFICIENT_KS_RS = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];

    /**
     * @param string $inn
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateInn(string $inn): bool
    {
        $this->isEmpty($inn);
        $this->isPattern($inn);

        $innLength = strlen($inn);

        $checkDigit = function($inn, $coefficients): int {
            $number = 0;
            foreach ($coefficients as $index => $coefficient) {
                $number += $coefficient * $inn[$index];
            }
            return $number % 11 % 10;
        };

        switch ($innLength) {
            case 10:
                if ($checkDigit($inn, self::COEFFICIENT_INN10) == $inn[9]) {
                    return true;
                }

                break;
            case 12:
                $isCorrectFirstDigit = $checkDigit($inn, self::COEFFICIENT_INN11) == $inn[10];
                $isCorrectSecondDigit = $checkDigit($inn, self::COEFFICIENT_INN12) == $inn[11] ;

                if ($isCorrectFirstDigit && $isCorrectSecondDigit) {
                    return true;
                }

                break;
            default:
                throw new LengthValidationException('ИНН может состоять только из 10 или 12 цифр');
        }

        return false;
    }

    /**
     * @param string $bik
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateBik(string $bik): bool
    {
        $this->isEmpty($bik);
        $this->isPattern($bik);
        $this->isCorrectLength($bik, 9);

        return true;
    }

    /**
     * @param string $snils
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateSnils(string $snils): bool
    {
        $this->isEmpty($snils);
        $this->isPattern($snils);
        $this->isCorrectLength($snils, 11);

        $checkSum = 0;

        //высчитываем контрольную сумму
        for ($i = 0; $i < 9; $i++) {
            $checkSum += (int) $snils[$i] * (9 - $i);
        }

        $checkDigit = 0;

        if ($checkSum < 100) {//если контрольная сумма меньше 100, то контрольное число равно этой сумме
            $checkDigit = $checkSum;
        } elseif ($checkSum > 101) { //если больше 100, то вычислить остаток от деления на 101 и далее
            $checkDigit = $checkSum % 101;

            if ($checkDigit === 100) { //если остаток от деления равен 100, то контрольное число равно 0
                $checkDigit = 0;
            }
        }

        if ($checkDigit === (int) substr($snils, -2)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $ks
     * @param string $bik
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateKs(string $ks, string $bik): bool
    {
        $this->validateBik($bik);

        $this->isEmpty($ks);
        $this->isPattern($ks);
        $this->isCorrectLength($ks, 20);

        //Составляем 23-значное число из нуля, 5-й и 6-й цифр БИК и корреспондентского счета.
        $bikKs = '0' . substr($bik, -5, 2) . $ks;

        if ($this->checkSumRsKs($ks, $bikKs) % 10 === 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $ogrn
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateOGRN(string $ogrn): bool
    {
        $this->isEmpty($ogrn);
        $this->isPattern($ogrn);
        $this->isCorrectLength($ogrn, 13);

        if ($this->ogrnCheckSum($ogrn) == $ogrn[12]) {
            return true;
        }

        return false;
    }

    /**
     * @param string $ogrn
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateOgrnip(string $ogrn): bool
    {
        $this->isEmpty($ogrn);
        $this->isPattern($ogrn);
        $this->isCorrectLength($ogrn, 15);

        if ($this->ogrnCheckSum($ogrn) == $ogrn[14]) {
            return true;
        }

        return false;
    }

    /**
     * @param string        $rs
     * @param string        $bik
     * @param string | null $currency
     *
     * @return bool
     *
     * @throws CurrencyValidationException
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateRs(string $rs, string $bik, ?string $currency = null): bool
    {
        $this->validateBik($bik);

        $this->isEmpty($rs);
        $this->isPattern($rs);
        $this->isCorrectLength($rs, 20);

        //Составляем 23-значное число из 3-х последних цифр БИК и расчетного счета
        $bikKs = substr($bik, -3) . $rs;

        if (!is_null($currency)) {
            $this->checkCurrency($rs, $currency);
        }

        if ($this->checkSumRsKs($rs, $bikKs) % 10 === 0) {
            return true;
        }

        return false;
    }

    /**
     * @param PassportData $passportData
     *
     * @return bool
     *
     * @throws DivisionPassportException
     * @throws IssuePassportException
     * @throws NumberPassportException
     * @throws PatternValidationException
     */
    public function validatePassport(PassportData $passportData): bool
    {
        //ФИО не может содержать цифр и символов
        $this->isPattern($passportData->getFullName(), '/^[а-яА-ЯёЁ\s]+$/');

        $seriesYear = DateTimeImmutable::createFromFormat(
            'y',
            substr($passportData->getSeries(), -2),//год выдачи - две последних цифры серии
        );

        //разница между печатью бланка и датой выдачи не должна быть +/-5 лет
        $maxIssueYear = $passportData->getIssueDate()->modify('+ 5 year');
        $minIssueYear = $passportData->getIssueDate()->modify('- 5 year');

        if ($maxIssueYear > $seriesYear || $seriesYear < $minIssueYear) {
            throw new IssuePassportException(
                sprintf(
                    'Недействительный паспорт: год выдачи %s значительно отличается от годы %s печати бланка',
                    $passportData->getIssueDate()->format('Y'),
                    $seriesYear->format('Y'),
                ),
            );
        }

        //начало даты выдачи паспортов - начали выдавать в 97 году
        $startIssuesDate = DateTimeImmutable::createFromFormat('y', 97);
        $endIssuesDate = new DateTimeImmutable();

        if ($seriesYear > $endIssuesDate || $seriesYear < $startIssuesDate) {
            throw new IssuePassportException(
                sprintf(
                    'Недействительный паспорт: год выдачи %s меньше или больше допустимого диапазона %s - %s',
                    $seriesYear->format('Y'),
                    $startIssuesDate->format('Y'),
                    $endIssuesDate->format('Y'),
                ),
            );
        }

        if ($passportData->getNumber() > 999999 || $passportData->getNumber() < 101) {
            throw new NumberPassportException(
                sprintf(
                    'Недействительный паспорт с номером %s: номер паспорта не может быть больше 999999 и меньше 000101',
                    $passportData->getNumber(),
                ),
            );
        }

        //уровень подразделения - третья цифра в коде подразделения
        $levelDivision = substr($passportData->getDivisionCode(), 2, 1);

        //максимум четыре уровня
        if (!in_array($levelDivision, [0,1,2,3])) {
            throw new DivisionPassportException(
                sprintf(
                    'Недействительный паспорт: уровень подразделения (%s) не соответствует допустимым значениям',
                    $passportData->getDivisionCode(),
                ),
            );
        }

        //высчитываем разницу между датой рождения и датой выдачи паспорта
        $issieBirthDayDiff = $passportData->getIssueDate()->format('Y') - $passportData->getBirthDay()->format('Y');

        //разница между датой рождения и датой выдачи не должна быть меньше 14 лет
        if ($issieBirthDayDiff < 14) {
            throw new IssuePassportException(
                    'Недействительный паспорт: Возраст владельца на момент выдачи меньше 14 лет',
            );
        }

        return true;
    }

    /**
     * @param string $requisite
     * @param string $currency
     *
     * @throws CurrencyValidationException
     */
    private function checkCurrency(string $requisite, string $currency): void
    {
        $currency = new Currency($currency);

        $rsCurrencyCode = substr($requisite,5, 3);

        if ($rsCurrencyCode != $currency->getCurrencyCode()) {
            throw new CurrencyValidationException(
                sprintf(
                    'Счет %s должен иметь валюту в %s. Имеет валюту в %s',
                    $currency,
                    $currency->getCurrencyName(),
                    Currency::getCurrencyNameByCode($rsCurrencyCode),
                ),
            );
        }
    }

    /**
     * Возвращает контрольную сумму огрн
     *
     * @see http://www.kholenkov.ru/data-validation/ogrn/
     * @see http://www.kholenkov.ru/data-validation/ogrnip/
     *
     * @param string $ogrn
     *
     * @return string
     *
     * @throws LengthValidationException
     */
    private function ogrnCheckSum(string $ogrn): string
    {
        switch (strlen($ogrn)) {
            case 13:
                $coefficient = '11';

                break;
            case 15:
                $coefficient = '13';

                break;
            default:
                throw new LengthValidationException('Некорректная длина ОГРН');
        }

        $subOgrn = substr($ogrn, 0, -1); //выбираем числа с 1 по 11/14

        return substr(
            bcsub(
                $subOgrn,
                bcmul(
                    bcdiv($subOgrn, $coefficient),
                    $coefficient,
                ),
            ),
            -1,
        );
    }

    /**
     * @param string $requisite
     * @param string $bikKey
     *
     * @return int
     */
    private function checkSumRsKs(string $requisite, string $bikKey): int
    {
        $checkSum = 0;

        //вычисляем контрольное число
        foreach (self::COEFFICIENT_KS_RS as $item => $key) {
            $checkSum += $key * $bikKey[$item] % 10;
        }

        return $checkSum;
    }

    /**
     * @param string $requisite
     *
     * @throws EmptyValidationException
     */
    private function isEmpty(string $requisite): void
    {
        if (empty($requisite)) {
            throw new EmptyValidationException('Значение валидируемого элемента пусто!');
        }
    }

    /**
     * @param string $requisite
     * @param int    $length
     *
     * @throws LengthValidationException
     */
    private function isCorrectLength(string $requisite, int $length): void
    {
        $requisiteLength = strlen($requisite);

        if ($requisiteLength !== $length) {
            throw new LengthValidationException(
                sprintf(
                    'Валидируемый элемент длиной %d должен иметь длину %d',
                    $requisiteLength,
                    $length,
                ),
            );
        }
    }

    /**
     * @param string $requisite Валидируемый реквизит
     * @param string $pattern   Паттерн по умолчанию цифровой
     *
     * @throws PatternValidationException
     */
    private function isPattern(string $requisite, string $pattern = '/[^0-9]/'): void
    {
        if (preg_match($pattern, $requisite)) {
            throw new PatternValidationException(
                sprintf(
                    'Валидируемый элемент %s не соответствует паттерну: %s',
                    $requisite,
                    $pattern,
                ),
            );
        }
    }
}
