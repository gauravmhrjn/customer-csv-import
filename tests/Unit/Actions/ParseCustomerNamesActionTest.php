<?php

namespace Tests\Unit\Actions;

use App\Actions\ParseCustomerNamesAction;
use Tests\TestCase;

final class ParseCustomerNamesActionTest extends TestCase
{
    public function test_it_parse_customer_names(): void
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

        // Act
        $parsedData = resolve(ParseCustomerNamesAction::class)->handle($customerNames);

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
}
