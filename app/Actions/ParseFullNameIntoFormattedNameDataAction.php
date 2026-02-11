<?php

namespace App\Actions;

final readonly class ParseFullNameIntoFormattedNameDataAction
{
    private const string PATTERN_WITH_FIRST_NAME = "/(?<title>\w+) (?<firstName>\w+[\.-]*) (?<lastName>\w+[-]*\w+)/";

    private const string PATTERN_WITHOUT_FIRST_NAME = "/(?<title>\w+) (?<lastName>\w+[-]*\w+)/";

    /**
     * @return array<int,array>
     */
    public function handle(string $fullName): array
    {
        $fullName = trim($fullName);

        $connector = $this->detectIfFullNameContainsConnectorWords($fullName);

        if ($connector) {
            // parse for multiple full names
            return $this->extractMultipleFullNames($fullName, $connector);
        }

        return [$this->parse($fullName)];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function extractMultipleFullNames(string $fullNames, string $connector = 'and'): array
    {
        $fullNameArray = explode(sprintf(' %s ', $connector), $fullNames);

        $fullNameArray = array_reverse($fullNameArray);

        $parsedFullNameArray = [];

        foreach ($fullNameArray as $key => $fullName) {
            if ($this->checkIfOnlyTitleIsPresnetInFullName($fullName)) {

                // check if second full name has already been parsed
                if (array_key_exists($key - 1, $parsedFullNameArray)) {
                    $previousFullNameData = $parsedFullNameArray[$key - 1];

                    // copy over the second customer's name details into first customer
                    $parsedFullNameArray[$key] = [
                        'title' => $fullName,
                        'first_name' => $previousFullNameData['first_name'],
                        'last_name' => $previousFullNameData['last_name'],
                        'initial' => $previousFullNameData['initial'],
                    ];

                    continue;
                }
            }

            $parsedFullNameArray[$key] = $this->parse($fullName);
        }

        return array_reverse($parsedFullNameArray);
    }

    private function detectIfFullNameContainsConnectorWords(string $fullName): ?string
    {
        preg_match('( and | & )', $fullName, $nameConnectors);

        return $nameConnectors ? trim($nameConnectors[0]) : null;
    }

    private function isFirstNamePresentInFullName(string $fullName): bool
    {
        $numberOfWordsInFullName = str_word_count($fullName);

        return $numberOfWordsInFullName === 3;
    }

    private function checkIfOnlyTitleIsPresnetInFullName(string $fullName): bool
    {
        $numberOfWordsInFullName = str_word_count($fullName);

        return $numberOfWordsInFullName === 1;
    }

    private function isFirstNameOnlyAnInitial(string $firstName): bool
    {
        $numberOfCharInFirstName = strlen($firstName);

        return $numberOfCharInFirstName === 1;
    }

    private function sanitizeString(string $string): string
    {
        // removes dot (.) from the end of the string if any
        // can be expanded to add new sanitization rules if need
        return rtrim($string, '.');
    }

    /**
     * @return array<string,string|null>
     */
    private function parse(string $fullName): array
    {
        $isFirstNamePresentInFullName = $this->isFirstNamePresentInFullName($fullName);

        $pattern = $isFirstNamePresentInFullName
            ? self::PATTERN_WITH_FIRST_NAME
            : self::PATTERN_WITHOUT_FIRST_NAME;

        preg_match($pattern, $fullName, $fullNameData);

        if (! $isFirstNamePresentInFullName) {
            [$originalFullName, $title, $lastName] = $fullNameData;

            return [
                'title' => $title,
                'first_name' => null,
                'last_name' => $lastName,
                'initial' => null,
            ];
        }

        [$originalFullName, $title, $firstName, $lastName] = $fullNameData;

        $sanitizedFirstName = $this->sanitizeString($firstName);
        $isFirstNameOnlyAnInitial = $this->isFirstNameOnlyAnInitial($sanitizedFirstName);

        return [
            'title' => $title,
            'first_name' => ! $isFirstNameOnlyAnInitial ? $sanitizedFirstName : null,
            'last_name' => $lastName,
            'initial' => $isFirstNameOnlyAnInitial ? $sanitizedFirstName : null,
        ];
    }
}
