<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GridControllerTest extends WebTestCase
{
    public function testGetGridDataAction()
    {
        $client = static::createClient();
        $client->request('GET', '/grid/get-data?year=2020&month=06');

        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $expectedData = [
            [
                'amount' => 384.29,
                'name' => null,
                'description' => null,
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-21',
                'category_name' => 'group-1:category-1-2',
                'split' => false,
            ], [
                'amount' => 13,
                'name' => null,
                'description' => 'Test',
                'description_raw' => 'Test',
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-05-28',
                'category_name' => 'group-1:category-1-1',
                'split' => true,
            ], [
                'amount' => 54,
                'name' => null,
                'description' => 'Test',
                'description_raw' => 'Test',
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-09',
                'category_name' => 'group-1:category-1-1',
                'split' => true,
            ], [
                'amount' => 26,
                'name' => null,
                'description' => 'Test',
                'description_raw' => 'Test',
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-04',
                'category_name' => 'group-1:category-1-1',
                'split' => true,
            ], [
                'amount' => 10,
                'name' => null,
                'description' => 'Test',
                'description_raw' => 'Test',
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-10',
                'category_name' => 'group-1:category-1-1',
                'split' => true,
            ], [
                'amount' => 247.28,
                'name' => 'MUENCHEN HOTEL XY\\MUENCHEN\\DE',
                'description' => 'test1234',
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-13',
                'category_name' => '',
                'split' => false,
            ], [
                'amount' => 67.59,
                'name' => null,
                'description' => null,
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-11',
                'category_name' => 'group-1:category-1-1',
                'split' => false,
            ], [
                'amount' => 12.53,
                'name' => null,
                'description' => null,
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-03',
                'category_name' => '',
                'split' => false,
            ],
        ];
        $this->assertArrayHasKey('data', $contentDecoded);
        $data = $contentDecoded['data'];
        $this->assertIsArray($data);
        $this->assertCount(count($expectedData), $data);

        // we have to remove the primary id's from the response
        array_walk($data, function (&$item) {
            unset($item['id_transaction']);
            unset($item['id_splittransaction']);
            unset($item['category_id']);
        });
        $this->assertEquals($expectedData, $data);
    }

    public function testGetGridDataActionUnknownTransactions()
    {
        $client = static::createClient();

        $client->request('GET', '/grid/get-data?year=2020&month=06&onlyunassigned=1');

        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $expectedData = [
            [
                'amount' => '247.28',
                'name' => 'MUENCHEN HOTEL XY\\MUENCHEN\\DE',
                'description' => 'test1234',
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-13',
                'category_name' => '',
                'split' => false,
            ], [
                'amount' => '12.53',
                'name' => null,
                'description' => null,
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-03',
                'category_name' => '',
                'split' => false,
            ],
        ];
        $this->assertArrayHasKey('data', $contentDecoded);
        $data = $contentDecoded['data'];
        $this->assertIsArray($data);
        $this->assertCount(count($expectedData), $data);

        // we have to remove the primary id's from the response
        array_walk($data, function (&$item) {
            unset($item['id_transaction']);
            unset($item['id_splittransaction']);
            unset($item['category_id']);
        });
        $this->assertEquals($expectedData, $data);
    }

    public function testGetGridDataActionFilterTransactions()
    {
        $client = static::createClient();

        $client->request('GET', '/grid/get-data?year=2020&month=06&name=hotel');

        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $expectedData = [
            [
                'amount' => '247.28',
                'name' => 'MUENCHEN HOTEL XY\\MUENCHEN\\DE',
                'description' => 'test1234',
                'description_raw' => null,
                'booking_text' => 'LASTSCHRIFT',
                'credit_debit' => 'debit',
                'valuta_date' => '2020-06-13',
                'category_name' => '',
                'split' => false,
            ],
        ];
        $this->assertArrayHasKey('data', $contentDecoded);
        $data = $contentDecoded['data'];
        $this->assertIsArray($data);
        $this->assertCount(count($expectedData), $data);

        // we have to remove the primary id's from the response
        array_walk($data, function (&$item) {
            unset($item['id_transaction']);
            unset($item['id_splittransaction']);
            unset($item['category_id']);
        });
        $this->assertEquals($expectedData, $data);
    }

    public function testGetCategoryDataAction()
    {
        $client = static::createClient();
        $client->request('GET', '/grid/get-category-data');

        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);
    }
}
