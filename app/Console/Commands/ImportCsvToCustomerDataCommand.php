<?php

namespace App\Console\Commands;

use App\Actions\ExtractCustomerNamesFromCsvFileInterface;
use App\Actions\ParseCustomerNamesAction;
use App\Exceptions\CsvFileNotFoundException;
use Illuminate\Console\Command;

class ImportCsvToCustomerDataCommand extends Command
{
    /**
     * @var string
     */
    public const string CSV_FILENAME = 'examples.csv';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import examples.csv file content and extract the customer data';

    /**
     * Execute the console command.
     */
    public function handle(
        ExtractCustomerNamesFromCsvFileInterface $extractFullNameFromCsvFileAction,
        ParseCustomerNamesAction $parseCustomerNamesAction
    ) {
        try {
            $customerNames = $extractFullNameFromCsvFileAction->handle(self::CSV_FILENAME);

            $formattedCustomerNames = $parseCustomerNamesAction->handle($customerNames);

            $this->info(
                collect($formattedCustomerNames)->toJson(JSON_PRETTY_PRINT)
            );

            return self::SUCCESS;

        } catch (CsvFileNotFoundException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
