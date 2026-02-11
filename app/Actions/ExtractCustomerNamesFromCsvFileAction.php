<?php

namespace App\Actions;

use App\Exceptions\CsvFileNotFoundException;
use Illuminate\Support\Facades\Storage;

final readonly class ExtractCustomerNamesFromCsvFileAction implements ExtractCustomerNamesFromCsvFileInterface
{
    /**
     * @return array<int,string>
     */
    public function handle(string $csvFilename): array
    {
        if (! Storage::exists($csvFilename)) {
            throw new CsvFileNotFoundException(
                CsvFileNotFoundException::ERROR_MESSAGE
            );
        }

        $extractedData = str_getcsv(Storage::get($csvFilename));

        // remove csv header
        array_shift($extractedData);

        return $extractedData;
    }
}
