<?php

namespace Mixasmix\ValidationBundle\Helper;

use DateTimeImmutable;
use Mixasmix\ValidationBundle\DTO\PassportData;
use Mixasmix\ValidationBundle\Enum\Currency;
use Mixasmix\ValidationBundle\Exception\CheckSumValidationException;
use Mixasmix\ValidationBundle\Exception\CurrencyValidationException;
use Mixasmix\ValidationBundle\Exception\DivisionPassportException;
use Mixasmix\ValidationBundle\Exception\EmptyValidationException;
use Mixasmix\ValidationBundle\Exception\IssuePassportException;
use Mixasmix\ValidationBundle\Exception\KeyNotFoundException;
use Mixasmix\ValidationBundle\Exception\LengthValidationException;
use Mixasmix\ValidationBundle\Exception\NumberPassportException;
use Mixasmix\ValidationBundle\Exception\PatternValidationException;

abstract class ValidationHelper
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
     * @param string $value
     *
     * @throws EmptyValidationException
     */
    public static function isEmpty(string $value): void
    {
        if (empty($value)) {
            throw new EmptyValidationException('Значение валидируемого элемента пусто!');
        }
    }

    /**
     * @param string $value
     * @param int    $length
     *
     * @throws LengthValidationException
     */
    public static function isCorrectLength(string $value, int $length): void
    {
        $requisiteLength = strlen($value);

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
     * @param string $value   Валидируемый параметр
     * @param string $pattern Паттерн по умолчанию цифровой
     *
     * @throws PatternValidationException
     */
    public static function isPattern(string $value, string $pattern = '/^[0-9]+$/'): void
    {
        if (!preg_match($pattern, $value)) {
            throw new PatternValidationException(
                sprintf(
                    'Валидируемый элемент %s не соответствует паттерну: %s',
                    $value,
                    $pattern,
                ),
            );
        }
    }

    /**
     * @param string $value
     * @param string $currency
     *
     * @throws CurrencyValidationException
     */
    public static function checkCurrency(string $value, string $currency): void
    {
        $currency = new Currency($currency);

        $rsCurrencyCode = substr($value,5, 3);

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
     */
    public static function ogrnCheckSum(string $ogrn): string
    {
        $coefficient = strlen($ogrn) - 2;

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
     * @param string $bikKey
     *
     * @return int
     */
    public static function checkSumRsKs(string $bikKey): int
    {
        $checkSum = 0;

        //вычисляем контрольное число
        foreach (self::COEFFICIENT_KS_RS as $item => $key) {
            $checkSum += $key * $bikKey[$item] % 10;
        }

        return $checkSum;
    }

    /**
     * Рассчет конрольной суммы снилс
     *
     * @param string $snils
     *
     * @return int
     */
    public static function snilsCheckSum(string $snils): int
    {
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

        return $checkDigit;
    }

    /**
     * @param string $inn
     *
     * @throws CheckSumValidationException
     * @throws LengthValidationException
     */
    public static function innCheckSum(string $inn): void
    {
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
                if ($checkDigit($inn, self::COEFFICIENT_INN10) != $inn[9]) {
                    throw new CheckSumValidationException('Контрольная сумма не совпадает!');
                }

                break;
            case 12:
                $isCorrectFirstDigit = $checkDigit($inn, self::COEFFICIENT_INN11) == $inn[10];
                $isCorrectSecondDigit = $checkDigit($inn, self::COEFFICIENT_INN12) == $inn[11] ;

                if (!$isCorrectFirstDigit || !$isCorrectSecondDigit) {
                    throw new CheckSumValidationException('Контрольная сумма не совпадает!');
                }

                break;
            default:
                throw new LengthValidationException('ИНН может состоять только из 10 или 12 цифр');
        }
    }

    /**
     * @param array  $structure
     * @param string $searchKey
     * @param string $path
     *
     * @return string
     *
     * @throws KeyNotFoundException
     */
    public static function getValueByKey(array $structure, string $searchKey, string $path): string
    {
        //получаем из строки вида [a][b][c] массив
        $contextPathArray = explode('][', trim($path,'[]'));

        foreach ($contextPathArray as $key => $value) {
            if ($key ===  array_key_last($contextPathArray)) {
                if (!array_key_exists($searchKey, $structure)) {
                    throw new KeyNotFoundException(
                        sprintf('Ключ %s не найден в структуре', $searchKey),
                    );
                }

                $result = $structure[$searchKey];
            }

            $structure = $structure[$value];
        }

        return $result;
    }

    /**
     * @param PassportData $passportData
     *
     * @throws DivisionPassportException
     * @throws IssuePassportException
     * @throws NumberPassportException
     * @throws PatternValidationException
     */
    public static function passportValidation(PassportData $passportData): void
    {
        if (!is_null($passportData->getFullName())) {
            //ФИО не может содержать цифр и символов
            ValidationHelper::isPattern($passportData->getFullName(), '/^[а-яёА-ЯЁ\s\-]+$/u');
        }

        if (!is_null($passportData->getSeries())) {

            $seriesYear = DateTimeImmutable::createFromFormat(
                'y',
                substr($passportData->getSeries(), -2),//год выдачи - две последних цифры серии
            );

            //разница между печатью бланка и датой выдачи не должна быть +/-5 лет
            $maxIssueYear = $passportData->getIssueDate()->modify('+ 5 year');
            $minIssueYear = $passportData->getIssueDate()->modify('- 5 year');

            if ($maxIssueYear > $seriesYear && $seriesYear < $minIssueYear) {
                throw new IssuePassportException(
                    sprintf(
                        'Недействительный паспорт: год выдачи %s значительно отличается от года %s печати бланка',
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
        }

        if (!is_null($passportData->getNumber())) {
            if ($passportData->getNumber() > 999999 || $passportData->getNumber() < 101) {
                throw new NumberPassportException(
                    sprintf(
                        'Недействительный паспорт с номером %s: номер паспорта не может быть больше 999999 и меньше 000101',
                        $passportData->getNumber(),
                    ),
                );
            }
        }

        if (!is_null($passportData->getDivisionCode())) {
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
        }

        if (!is_null($passportData->getIssueDate()) && !is_null($passportData->getBirthDay())) {
            //высчитываем разницу между датой рождения и датой выдачи паспорта
            $issieBirthDayDiff = $passportData->getIssueDate()->format('Y') - $passportData->getBirthDay()->format('Y');

            //разница между датой рождения и датой выдачи не должна быть меньше 14 лет
            if ($issieBirthDayDiff < 14) {
                throw new IssuePassportException(
                    'Недействительный паспорт: Возраст владельца на момент выдачи меньше 14 лет',
                );
            }

            //высчитываем дату между выдачей и сегодняшним днем
            $todayIssueDiff = (new DateTimeImmutable())->format('Y') - $passportData->getIssueDate()->format('Y');

            //Если разница больше 20 лет, то паспорт просрочен
            if ($todayIssueDiff > 20) {
                throw new IssuePassportException(
                    sprintf('Недействительный паспорт: паспорт просрочен на %d лет', $todayIssueDiff),
                );
            }
        }
    }
}
