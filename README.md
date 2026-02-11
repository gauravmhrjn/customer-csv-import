## CSV Customer Name Command & API

This is a small Laravel application that reads customer names from a CSV file and exposes the parsed / normalised data via:

- **HTTP API endpoint**: returns formatted customer name data as JSON.
- **Artisan console command**: imports the same CSV and prints formatted data to the terminal.

### Project setup

- **Clone & install dependencies**

```bash
git clone <this-repo-url>
cd street-group-task
composer install
```

- **Environment & app key**

```bash
cp .env.example .env
php artisan key:generate
```

### CSV source file location

Both the API endpoint and the console command expect an `examples.csv` file to be present on the default Laravel storage disk:

- **Expected path**: `storage/app/examples.csv`
- **File name**: exactly `examples.csv`

If the file is missing, both entry points will report a `Csv file not found. Please add the csv file into storage/private directory.` style error (via JSON for the API and an error message in the console).

### HTTP API endpoint

- **Method**: `GET`
- **Path**: `/api/convert/csv`

#### How to run and call the endpoint

1. **Start the HTTP server**

```bash
php artisan serve
```

By default Laravel serves the app at `http://127.0.0.1:8000`.

2. **Ensure `examples.csv` is in place**

Place your CSV file at `storage/app/examples.csv`.

3. **Call the endpoint via browser or postman**

```bash
http://127.0.0.1:8000/api/convert/csv
```

#### Expected responses

- **200 OK**: JSON array of formatted customer name records parsed from `examples.csv`.
- **404 Not Found**: JSON payload with `status: "failed"` and an `error` message when the CSV file is not found.

### Artisan import command

The command reads the same `examples.csv` file from storage, parses and normalises the customer names, and prints the resulting data structure as pretty-printed JSON to the console.

#### How to run the command

1. **Ensure `examples.csv` exists**

Place `examples.csv` at `storage/app/examples.csv`.

2. **Run the command**

```bash
php artisan app:import-csv
```

#### Expected output

- **On success**: the console prints a JSON array of formatted customer name data (pretty-printed).
- **On failure**: the console prints an error message if the CSV file cannot be found, and the command exits with a non-zero status code.

### Running tests

This project includes feature and unit tests around the CSV parsing and formatting logic.

- **Run the test suite**

```bash
php artisan test
```
