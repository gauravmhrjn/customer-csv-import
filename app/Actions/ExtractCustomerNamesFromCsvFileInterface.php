<?php

namespace App\Actions;

interface ExtractCustomerNamesFromCsvFileInterface
{
    /**
     * @return array<int,string>
     */
    public function handle(string $csvFilename): array;
}
