<?php

namespace App\Http\Controllers;

use App\Actions\ExtractCustomerNamesFromCsvFileInterface;
use App\Actions\ParseCustomerNamesAction;
use App\Exceptions\CsvFileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ConvertCsvController extends Controller
{
    public const string CSV_FILENAME = 'examples.csv';

    public function __invoke(
        ExtractCustomerNamesFromCsvFileInterface $extractFullNameFromCsvFileAction,
        ParseCustomerNamesAction $parseCustomerNamesAction
    ): JsonResponse {
        try {
            $customerNames = $extractFullNameFromCsvFileAction->handle(self::CSV_FILENAME);

            $formattedCustomerNames = $parseCustomerNamesAction->handle($customerNames);

            return response()->json($formattedCustomerNames, Response::HTTP_OK);

        } catch (CsvFileNotFoundException $exception) {
            return response()->json([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
