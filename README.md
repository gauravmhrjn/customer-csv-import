## CSV Customer Name Command & API

This is a small Laravel application that reads customer names from a CSV file and exposes the parsed / normalised data via:

- **Artisan console command**: imports the same CSV and prints formatted data to the terminal.
- **HTTP API endpoint**: returns formatted customer name data as JSON.

### Project setup

- **Clone & install dependencies**

```bash
git clone https://github.com/gauravmhrjn/customer-csv-import.git
cd customer-csv-import
composer install
```

- **Environment & app key**

```bash
cp .env.example .env
php artisan key:generate
```

### CSV source file location

Both the API endpoint and the console command expect an `examples.csv` file to be present on the default Laravel storage disk:

- **Expected path**: `storage/app/private/examples.csv`
- **File name**: exactly `examples.csv`

If the file is missing, both entry points will report a `Csv file not found` error.

### How to run the command

```bash
php artisan app:import-csv
```

#### Expected output

- **On success**: the console prints a JSON array of formatted customer name data (pretty-printed).
- **On failure**: the console prints an error message if the CSV file cannot be found, and the command exits with a non-zero status code.

### How to run and call the endpoint

1. **Start the HTTP server**

```bash
php artisan serve
```

By default Laravel serves the app at `http://127.0.0.1:8000`.

2. **Call the endpoint via browser or postman**

- **Method**: `GET`
- **Path**: `/api/convert/csv`

```bash
http://127.0.0.1:8000/api/convert/csv
```

#### Expected responses

- **200 OK**: JSON array of formatted customer name records parsed from `examples.csv`.
- **404 Not Found**: JSON payload with `status: "failed"` and an `error` message when the CSV file is not found.

### Running tests

This project includes feature and unit tests around the CSV parsing and formatting logic.

- **Run the test suite**

```bash
php artisan test
```
