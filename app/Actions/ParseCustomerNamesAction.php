<?php

namespace App\Actions;

final readonly class ParseCustomerNamesAction
{
    public function __construct(
        private ParseCustomerNameIntoFormattedDataAction $parseCustomerNameIntoFormattedDataAction
    ) {}

    public function handle(array $customerNames): array
    {
        $parsedNames = [];

        foreach ($customerNames as $customerName) {
            $formattedCustomerData = $this->parseCustomerNameIntoFormattedDataAction->handle($customerName);

            array_push($parsedNames, ...$formattedCustomerData);
        }

        return $parsedNames;
    }
}
