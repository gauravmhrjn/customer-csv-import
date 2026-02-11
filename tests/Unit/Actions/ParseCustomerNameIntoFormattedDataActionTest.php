<?php

namespace Tests\Unit\Actions;

use App\Actions\ParseCustomerNameIntoFormattedDataAction;
use Tests\TestCase;

final class ParseCustomerNameIntoFormattedDataActionTest extends TestCase
{
    public function test_it_parse_customer_full_name(): void
    {
        // Arrange
        $fullName = 'Mr John Smith';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);

        // Assert
        $this->assertEquals('Mr', $firstParsedData['title']);
        $this->assertEquals('John', $firstParsedData['first_name']);
        $this->assertEquals('Smith', $firstParsedData['last_name']);
        $this->assertNull($firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_without_first_name_or_initial(): void
    {
        // Arrange
        $fullName = 'Mr Smith';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);

        // Assert
        $this->assertEquals('Mr', $firstParsedData['title']);
        $this->assertNull($firstParsedData['first_name']);
        $this->assertEquals('Smith', $firstParsedData['last_name']);
        $this->assertNull($firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_with_hyphen_symbol(): void
    {
        // Arrange
        $fullName = 'Mrs Faye Hughes-Eastwood';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);

        // Assert
        $this->assertEquals('Mrs', $firstParsedData['title']);
        $this->assertEquals('Faye', $firstParsedData['first_name']);
        $this->assertEquals('Hughes-Eastwood', $firstParsedData['last_name']);
        $this->assertNull($firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_with_first_name_initial(): void
    {
        // Arrange
        $fullName = 'Mr M Mackie';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);

        // Assert
        $this->assertEquals('Mr', $firstParsedData['title']);
        $this->assertNull($firstParsedData['first_name']);
        $this->assertEquals('Mackie', $firstParsedData['last_name']);
        $this->assertEquals('M', $firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_with_first_name_initial_and_a_dot(): void
    {
        // Arrange
        $fullName = 'Mr F. Fredrickson';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);

        // Assert
        $this->assertEquals('Mr', $firstParsedData['title']);
        $this->assertNull($firstParsedData['first_name']);
        $this->assertEquals('Fredrickson', $firstParsedData['last_name']);
        $this->assertEquals('F', $firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_into_multiple_name_where_full_name_contains_and_conjunction_word(): void
    {
        // Arrange
        $fullName = 'Mr Tom Staff and Mr John Doe';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);
        $lastParsedData = array_last($parsedData);

        // Assert
        $this->assertEquals('Mr', $lastParsedData['title']);
        $this->assertEquals('Tom', $lastParsedData['first_name']);
        $this->assertEquals('Staff', $lastParsedData['last_name']);
        $this->assertNull($lastParsedData['initial']);

        $this->assertEquals('Mr', $firstParsedData['title']);
        $this->assertEquals('John', $firstParsedData['first_name']);
        $this->assertEquals('Doe', $firstParsedData['last_name']);
        $this->assertNull($firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_into_multiple_name_where_full_name_contains_amphersand_conjunction_char(): void
    {
        // Arrange
        $fullName = 'Mr T. Staff & Mr John Doe';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);
        $lastParsedData = array_last($parsedData);

        // Assert
        $this->assertEquals('Mr', $lastParsedData['title']);
        $this->assertNull($lastParsedData['first_name']);
        $this->assertEquals('Staff', $lastParsedData['last_name']);
        $this->assertEquals('T', $lastParsedData['initial']);

        $this->assertEquals('Mr', $firstParsedData['title']);
        $this->assertEquals('John', $firstParsedData['first_name']);
        $this->assertEquals('Doe', $firstParsedData['last_name']);
        $this->assertNull($firstParsedData['initial']);
    }

    public function test_it_parse_customer_full_name_into_multiple_name_where_full_name_contains_any_conjunction_word_and_first_full_name_doesnt_have_first_name(): void
    {
        // Arrange
        $fullName = 'Mr and Mrs Smith';

        // Act
        $parsedData = resolve(ParseCustomerNameIntoFormattedDataAction::class)->handle($fullName);

        $firstParsedData = array_first($parsedData);
        $lastParsedData = array_last($parsedData);

        // Assert
        $this->assertEquals('Mr', $lastParsedData['title']);
        $this->assertNull($lastParsedData['first_name']);
        $this->assertEquals('Smith', $lastParsedData['last_name']);
        $this->assertNull($lastParsedData['initial']);

        $this->assertEquals('Mrs', $firstParsedData['title']);
        $this->assertNull($firstParsedData['first_name']);
        $this->assertEquals('Smith', $firstParsedData['last_name']);
        $this->assertNull($firstParsedData['initial']);
    }
}
