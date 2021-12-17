<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SplitTransactionControllerTest extends WebTestCase
{
    public function testParentTransactionNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/split-transaction?idTransaction=99999999');

        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(false, $contentDecoded['success']);
    }

    public function testGetSplitTransactionData()
    {
        // first we have to fetch all the transactions and filter the one with split-transactions
        $client = static::createClient();
        $client->request('GET', '/grid/get-data?year=2020&month=06');

        // some basic tests if the results are ok
        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();
        $contentDecoded = json_decode($content, true);
        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $data = $contentDecoded['data'] ?? [];
        $key = array_search(true, array_column($data, 'split'));

        $idTransaction = $data[$key]['id_transaction'] ?? 0;
        $this->assertNotEquals(0, $idTransaction);

        // now that we have the corresponding id - let's fetch explicit the split transactions
        $client->request('GET', sprintf('/split-transaction?idTransaction=%d', $idTransaction));

        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();
        $contentDecoded = json_decode($content, true);
        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $expectedData = [
            [
                "transaction" => $idTransaction,
                "description" => "Test",
                "amount" => 10,
                "category_name" => "category-1-1",
                "valuta_date" => "2020-06-10",
            ], [
                "transaction" => $idTransaction,
                "description" => "Test",
                "amount" => 26,
                "category_name" => "category-1-1",
                "valuta_date" => "2020-06-04",
            ], [
                "transaction" => $idTransaction,
                "description" => "Test",
                "amount" => 54,
                "category_name" => "category-1-1",
                "valuta_date" => "2020-06-09",
            ], [
                "transaction" => $idTransaction,
                "description" => "Test",
                "amount" => 13,
                "category_name" => "category-1-1",
                "valuta_date" => "2020-05-28",
            ]
        ];

        $this->assertArrayHasKey('data', $contentDecoded);
        $data = $contentDecoded['data'];
        $this->assertIsArray($data);
        $this->assertCount(count($expectedData), $data);

        // we have to remove the primary id's from the response
        array_walk($data, function (&$item) {
            unset($item['idSplitTransaction']);
            unset($item['category_id']);
        });
        $this->assertEquals($expectedData, $data);

        // check rest amount
        $this->assertEquals(7, $contentDecoded['transaction']['amount'] ?? 0);
    }
}