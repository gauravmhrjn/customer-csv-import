<?php

namespace App\Exceptions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

final class CsvFileNotFoundException extends FileNotFoundException
{
    public const string ERROR_MESSAGE = 'Csv file is not present in /storage/app/private directory. Please add the csv file and try again.';
}
