<?php

namespace Tests\Feature;

use App\Actions\ExtractCustomerNamesFromCsvFileInterface;
use App\Exceptions\CsvFileNotFoundException;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery\MockInterface;
use Tests\TestCase;

final class ConvertCsvControllerTest extends TestCase
{
    public function test_it_can_parse_the_csv_file_to_extract_and_parse_the_customer_names_into_expected_format(): void
    {
        // Arrange
        $customerNames = [
            'Mr John Smith',
            'Mr Smith',
            'Mr and Mrs Smith',
            'Mr M Mackie',
            'Mr Tom Staff and Mr John Doe',
            'Mrs Faye Hughes-Eastwood',
            'Mr F. Fredrickson',
        ];

        $this->mock(ExtractCustomerNamesFromCsvFileInterface::class, function (MockInterface $mock) use ($customerNames) {
            $mock->shouldReceive('handle')
                ->once()
                ->andReturn($customerNames);
        });

        // Act
        $response = $this->get('/api/convert/csv');

        // Assert
        $response->assertOk();

        $parsedData = $response->json();

        // Assert for 'Mr John Smith'
        $this->assertEquals('Mr', $parsedData[0]['title']);
        $this->assertEquals('John', $parsedData[0]['first_name']);
        $this->assertEquals('Smith', $parsedData[0]['last_name']);
        $this->assertNull($parsedData[0]['initial']);

        // Assert for 'Mr Smith'
        $this->assertEquals('Mr', $parsedData[1]['title']);
        $this->assertNull($parsedData[1]['first_name']);
        $this->assertEquals('Smith', $parsedData[1]['last_name']);
        $this->assertNull($parsedData[1]['initial']);

        // Assert for 'Mr and Mrs Smith'
        $this->assertEquals('Mrs', $parsedData[2]['title']);
        $this->assertNull($parsedData[2]['first_name']);
        $this->assertEquals('Smith', $parsedData[2]['last_name']);
        $this->assertNull($parsedData[2]['initial']);

        $this->assertEquals('Mr', $parsedData[3]['title']);
        $this->assertNull($parsedData[3]['first_name']);
        $this->assertEquals('Smith', $parsedData[3]['last_name']);
        $this->assertNull($parsedData[3]['initial']);

        // Assert for 'Mr M Mackie'
        $this->assertEquals('Mr', $parsedData[4]['title']);
        $this->assertNull($parsedData[4]['first_name']);
        $this->assertEquals('Mackie', $parsedData[4]['last_name']);
        $this->assertEquals('M', $parsedData[4]['initial']);

        // Assert for 'Mr Tom Staff and Mr John Doe'
        $this->assertEquals('Mr', $parsedData[5]['title']);
        $this->assertEquals('John', $parsedData[5]['first_name']);
        $this->assertEquals('Doe', $parsedData[5]['last_name']);
        $this->assertNull($parsedData[5]['initial']);

        $this->assertEquals('Mr', $parsedData[6]['title']);
        $this->assertEquals('Tom', $parsedData[6]['first_name']);
        $this->assertEquals('Staff', $parsedData[6]['last_name']);
        $this->assertNull($parsedData[6]['initial']);

        // Assert for 'Mrs Faye Hughes-Eastwood'
        $this->assertEquals('Mrs', $parsedData[7]['title']);
        $this->assertEquals('Faye', $parsedData[7]['first_name']);
        $this->assertEquals('Hughes-Eastwood', $parsedData[7]['last_name']);
        $this->assertNull($parsedData[7]['initial']);

        // Assert for 'Mr F. Fredrickson'
        $this->assertEquals('Mr', $parsedData[8]['title']);
        $this->assertNull($parsedData[8]['first_name']);
        $this->assertEquals('Fredrickson', $parsedData[8]['last_name']);
        $this->assertEquals('F', $parsedData[8]['initial']);
    }

    public function test_it_returns_error_when_csv_file_is_missing(): void
    {
        // Arrange
        $this->mock(ExtractCustomerNamesFromCsvFileInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(CsvFileNotFoundException::class, CsvFileNotFoundException::ERROR_MESSAGE);
        });

        // Act
        $response = $this->get('/api/convert/csv');

        // Assert
        $response->assertNotFound()
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 'failed')
                    ->where('error', CsvFileNotFoundException::ERROR_MESSAGE);
            });
    }
}

/*
[
    {
        "title": "Mr",
        "first_name": "John",
        "last_name": "Smith",
        "initial": null
    },
    {
        "title": "Mrs",
        "first_name": "Jane",
        "last_name": "Smith",
        "initial": null
    },
    {
        "title": "Mister",
        "first_name": "John",
        "last_name": "Doe",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": "Bob",
        "last_name": "Lawblaw",
        "initial": null
    },
    {
        "title": "Mrs",
        "first_name": null,
        "last_name": "Smith",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": null,
        "last_name": "Smith",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": "Craig",
        "last_name": "Charles",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": null,
        "last_name": "Mackie",
        "initial": "M"
    },
    {
        "title": "Mrs",
        "first_name": "Jane",
        "last_name": "McMaster",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": "John",
        "last_name": "Doe",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": "Tom",
        "last_name": "Staff",
        "initial": null
    },
    {
        "title": "Dr",
        "first_name": null,
        "last_name": "Gunn",
        "initial": "P"
    },
    {
        "title": "Mrs",
        "first_name": "Joe",
        "last_name": "Bloggs",
        "initial": null
    },
    {
        "title": "Dr",
        "first_name": "Joe",
        "last_name": "Bloggs",
        "initial": null
    },
    {
        "title": "Ms",
        "first_name": "Claire",
        "last_name": "Robbo",
        "initial": null
    },
    {
        "title": "Prof",
        "first_name": "Alex",
        "last_name": "Brogan",
        "initial": null
    },
    {
        "title": "Mrs",
        "first_name": "Faye",
        "last_name": "Hughes-Eastwood",
        "initial": null
    },
    {
        "title": "Mr",
        "first_name": null,
        "last_name": "Fredrickson",
        "initial": "F"
    }
]
*/
