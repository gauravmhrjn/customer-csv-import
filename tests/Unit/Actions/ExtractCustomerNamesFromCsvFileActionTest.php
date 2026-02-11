<?php

namespace Tests\Unit\Actions;

use App\Actions\ExtractCustomerNamesFromCsvFileAction;
use App\Exceptions\CsvFileNotFoundException;
use Tests\TestCase;

final class ExtractCustomerNamesFromCsvFileActionTest extends TestCase
{
    public function test_it_extract_data_into_array_from_csv_file(): void
    {
        // Arrange
        $csvFilename = 'examples.csv';

        // Act
        $extractedData = resolve(ExtractCustomerNamesFromCsvFileAction::class)->handle($csvFilename);

        // Assert
        $this->assertIsArray($extractedData);
    }

    public function test_it_doesnt_include_the_csv_headers_in_the_extract_data(): void
    {
        // Arrange
        $csvFilename = 'examples.csv';
        $csvHeader = 'homeowner';

        // Act
        $extractedData = resolve(ExtractCustomerNamesFromCsvFileAction::class)->handle($csvFilename);

        // Assert
        $this->assertIsArray($extractedData);
        $this->assertNotContains($csvHeader, $extractedData);
    }

    public function test_it_throws_exception_when_csv_file_is_missing(): void
    {
        // Arrange
        $csvFilename = 'csv-file-missing.csv';

        // Assert for exception
        $this->expectException(CsvFileNotFoundException::class);

        // Act
        resolve(ExtractCustomerNamesFromCsvFileAction::class)->handle($csvFilename);
    }
}
