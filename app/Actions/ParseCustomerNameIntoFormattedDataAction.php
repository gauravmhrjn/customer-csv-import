<?php

namespace App\Actions;

use App\Enum\NameType;
use Exception;

final class ParseCustomerNameIntoFormattedDataAction
{
    private const string PATTERN_WITH_FIRST_NAME = "/(?<title>\w+) (?<firstName>\w+[\.-]*) (?<lastName>\w+[-]*\w+)/";

    private const string PATTERN_WITHOUT_FIRST_NAME = "/(?<title>\w+) (?<lastName>\w+[-]*\w+)/";

    private const array NAME_CONNECTORS = ['and', '&'];

    private array $parsedCustomerNames;

    /**
     * @return array<int,array>
     */
    public function handle(string $customerName): array
    {
        $this->parsedCustomerNames = [];

        $customerName = trim($customerName);

        [$isMultiName, $nameConnectorUsed] = $this->detectIfCustomerNameContainsMultipleNames($customerName);

        if ($isMultiName) {
            $customerNamesList = $this->extractMultipleNamesFromCustomerName($customerName, $nameConnectorUsed);
            $this->parseCustomerNameWithMultipleNames($customerNamesList);
        } else {
            $this->parseCustomerName($customerName);
        }

        return $this->parsedCustomerNames;
    }

    /**
     * @return list<string>
     */
    private function extractMultipleNamesFromCustomerName(string $customerNames, string $nameConnectorUsed): array
    {
        return explode(sprintf(' %s ', $nameConnectorUsed), $customerNames);
    }

    private function parseCustomerNameWithMultipleNames(array $customerNamesList): void
    {
        // reverse the array order so that if the customer with only-title exist then it will be handled on the last iteration
        $customerNamesList = array_reverse($customerNamesList);

        foreach ($customerNamesList as $customerName) {

            $customerNameType = NameType::byWordCount($customerName);

            match ($customerNameType) {
                NameType::ONLY_TITLE => $this->parseCustomerNameWithoutFirstNameAndLastName(
                    customerTitle: $customerName,
                    connectedCustomerData: array_last($this->parsedCustomerNames)
                ),
                default => $this->parseCustomerName($customerName),
            };
        }
    }

    private function parseCustomerNameWithoutFirstNameAndLastName(string $customerTitle, array $connectedCustomerData): void // maybe use customerDTO as an improvement
    {
        $this->setParsedCustomerNames([
            'title' => $customerTitle,
            'first_name' => $connectedCustomerData['first_name'],
            'last_name' => $connectedCustomerData['last_name'],
            'initial' => $connectedCustomerData['initial'],
        ]);
    }

    private function parseCustomerName(string $customerName): void
    {
        $customerNameType = NameType::byWordCount($customerName);

        match ($customerNameType) {
            NameType::TITLE_AND_LASTNAME => $this->parseCustomerNameWithoutFirstName($customerName),
            NameType::FULL_NAME => $this->parseCustomerFullName($customerName),
            default => throw new Exception('Unsupported customer name provided')
        };
    }

    private function parseCustomerFullName(string $customerName): void
    {
        preg_match(self::PATTERN_WITH_FIRST_NAME, $customerName, $customerData);

        [$originalData, $title, $firstName, $lastName] = $customerData;

        $sanitizedFirstName = $this->sanitizeString($firstName);
        $isFirstNameOnlyAnInitial = $this->isFirstNameOnlyAnInitial($sanitizedFirstName);

        $this->setParsedCustomerNames([
            'title' => $title,
            'first_name' => ! $isFirstNameOnlyAnInitial ? $sanitizedFirstName : null,
            'last_name' => $lastName,
            'initial' => $isFirstNameOnlyAnInitial ? $sanitizedFirstName : null,
        ]);
    }

    private function parseCustomerNameWithoutFirstName(string $customerName): void
    {
        preg_match(self::PATTERN_WITHOUT_FIRST_NAME, $customerName, $customerData);

        [$originalData, $title, $lastName] = $customerData;

        $this->setParsedCustomerNames([
            'title' => $title,
            'first_name' => null,
            'last_name' => $lastName,
            'initial' => null,
        ]);
    }

    private function setParsedCustomerNames(array $customerData): void
    {
        $this->parsedCustomerNames[] = $customerData;
    }

    /**
     * @return array<int,string|bool|null>
     */
    private function detectIfCustomerNameContainsMultipleNames(string $customerName): array
    {
        $pattern = implode(' | ', self::NAME_CONNECTORS);
        $pattern = sprintf('( %s )', $pattern);

        preg_match($pattern, $customerName, $nameConnectorUsed);

        return $nameConnectorUsed ? [true, trim($nameConnectorUsed[0])] : [false, null];
    }

    private function isFirstNameOnlyAnInitial(string $firstName): bool
    {
        return strlen($firstName) === 1;
    }

    private function sanitizeString(string $string): string
    {
        // removes dot (.) from the end of the string if any
        // can be expanded to add new sanitization rules if need
        return rtrim($string, '.');
    }
}
