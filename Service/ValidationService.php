<?php

namespace Mixasmix\ValidationBundle\Service;

use Mixasmix\ValidationBundle\DTO\PassportData;
use DateTimeImmutable;
use Mixasmix\ValidationBundle\Exception\CheckSumValidationException;
use Mixasmix\ValidationBundle\Exception\CurrencyValidationException;
use Mixasmix\ValidationBundle\Exception\DivisionPassportException;
use Mixasmix\ValidationBundle\Exception\EmptyValidationException;
use Mixasmix\ValidationBundle\Exception\IssuePassportException;
use Mixasmix\ValidationBundle\Exception\LengthValidationException;
use Mixasmix\ValidationBundle\Exception\NumberPassportException;
use Mixasmix\ValidationBundle\Exception\PatternValidationException;
use Mixasmix\ValidationBundle\Helper\ValidationHelper;

class ValidationService
{
    /**
     * @param string $inn
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     * @throws CheckSumValidationException
     */
    public function validateInn(string $inn): bool
    {
        ValidationHelper::isEmpty($inn);
        ValidationHelper::isPattern($inn);
        ValidationHelper::innCheckSum($inn);

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
        ValidationHelper::isEmpty($bik);
        ValidationHelper::isPattern($bik);
        ValidationHelper::isCorrectLength($bik, 9);

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
        ValidationHelper::isEmpty($snils);
        ValidationHelper::isPattern($snils);
        ValidationHelper::isCorrectLength($snils, 11);

        if (ValidationHelper::snilsCheckSum($snils) === (int) substr($snils, -2)) {
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

        ValidationHelper::isEmpty($ks);
        ValidationHelper::isPattern($ks);
        ValidationHelper::isCorrectLength($ks, 20);

        //Составляем 23-значное число из нуля, 5-й и 6-й цифр БИК и корреспондентского счета.
        $bikKs = '0' . substr($bik, -5, 2) . $ks;

        if (ValidationHelper::checkSumRsKs($bikKs) % 10 === 0) {
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
        ValidationHelper::isEmpty($ogrn);
        ValidationHelper::isPattern($ogrn);
        ValidationHelper::isCorrectLength($ogrn, 13);

        if (ValidationHelper::ogrnCheckSum($ogrn) == $ogrn[12]) {
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
        ValidationHelper::isEmpty($ogrn);
        ValidationHelper::isPattern($ogrn);
        ValidationHelper::isCorrectLength($ogrn, 15);

        if (ValidationHelper::ogrnCheckSum($ogrn) == $ogrn[14]) {
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

        ValidationHelper::isEmpty($rs);
        ValidationHelper::isPattern($rs);
        ValidationHelper::isCorrectLength($rs, 20);

        //Составляем 23-значное число из 3-х последних цифр БИК и расчетного счета
        $bikKs = substr($bik, -3) . $rs;

        if (!is_null($currency)) {
            ValidationHelper::checkCurrency($rs, $currency);
        }

        if (ValidationHelper::checkSumRsKs($bikKs) % 10 === 0) {
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
        ValidationHelper::passportValidation($passportData);

        return true;
    }

    /**
     * @param string $kpp
     *
     * @return bool
     *
     * @throws EmptyValidationException
     * @throws LengthValidationException
     * @throws PatternValidationException
     */
    public function validateKpp(string $kpp): bool
    {
        ValidationHelper::isEmpty($kpp);
        ValidationHelper::isPattern($kpp);
        ValidationHelper::isCorrectLength($kpp, 9);

        return true;
    }
}
