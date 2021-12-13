<?php

namespace App\Tests\Controller;

use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ChartControllerTest extends WebTestCase
{
    private const TEST_CATEGORY_COUNT = 5;

    public function testGetCategories()
    {
        $client = static::createClient();
        $client->request('GET', '/settings/category/data');
        $content = $this->responseIsJson($client->getResponse());
        $data = $this->checkResponseContentData($content);
        $this->assertCount(self::TEST_CATEGORY_COUNT, $data, 'Category count differs from expected amount of categories which should be available via the fixtures. Please check the test database!');

        // create an assoc array, name as index, id as value
        $categories = [];
        array_map(function ($category) use (&$categories) {
            $categories[$category['name']] = $category['id'];
        }, $data);

        return $categories;
    }

    public function testGetChartDataWithInvalidGrouping()
    {
        $client = static::createClient();
        $client->request('GET', '/chart/data?grouping=-1&categories=1');

        $this->assertResponseStatusCodeSame(200);

        $content = $this->responseIsJson($client->getResponse());

        $this->assertEquals(false, $content['success']);
        $this->assertEquals('Unsupported grouping type: -1', $content['message']);
    }

    /**
     * @depends testGetCategories
     */
    public function testGetChartDataWithGroupingByYear(array $categories)
    {
        $this->assertCount(self::TEST_CATEGORY_COUNT, $categories, 'Category count differs from expected amount of categories which should be available via the fixtures. Please check the test database!');

        $client = static::createClient();
        $categoryName = 'category-1-1';
        $categoryId = $categories[$categoryName] ?? 0;
        $client->request('GET', sprintf('/chart/data?grouping=%d&categories=%d', Transaction::GROUPING_YEARLY, $categoryId));
        $this->assertResponseStatusCodeSame(200);

        $content = $this->responseIsJson($client->getResponse());

        $this->assertArrayHasKey('success', $content);
        $this->assertIsBool($content['success']);
        $this->assertEquals(true, $content['success']);

        $expectedLabels = ['2020'];
        $labels = $this->checkResponseContentLabels($content);
        $this->assertIsArray($labels);
        $this->assertEquals($expectedLabels, $labels);

        $expectedData = [
            [
                'name' => $categoryName,
                'id' => $categoryId,
                'data' => [293.01],
            ],
        ];
        $data = $this->checkResponseContentData($content);
        $this->assertIsArray($data);
        $this->assertEquals($expectedData, $data);
    }

    /**
     * @depends testGetCategories
     */
    public function testGetChartDataWithGroupingByMonth(array $categories)
    {
        $this->assertCount(self::TEST_CATEGORY_COUNT, $categories, 'Category count differs from expected amount of categories which should be available via the fixtures. Please check the test database!');

        $client = static::createClient();
        $categoryName = 'category-1-1';
        $categoryId = $categories[$categoryName] ?? 0;
        $client->request('GET', sprintf('/chart/data?grouping=%d&categories=%d', Transaction::GROUPING_MONTHLY, $categoryId));
        $this->assertResponseStatusCodeSame(200);

        $content = $this->responseIsJson($client->getResponse());

        $this->assertArrayHasKey('success', $content);
        $this->assertIsBool($content['success']);
        $this->assertEquals(true, $content['success']);

        $expectedLabels = ['2020-05', '2020-06', '2020-07', '2020-08', '2020-09'];
        $labels = $this->checkResponseContentLabels($content);
        $this->assertIsArray($labels);
        $this->assertEquals($expectedLabels, $labels);

        $expectedData = [
            [
                'name' => $categoryName,
                'id' => $categoryId,
                'data' => [13, 157.59, 13.19, 46.64, 62.59],
            ],
        ];
        $data = $this->checkResponseContentData($content);
        $this->assertIsArray($data);
        $this->assertEquals($expectedData, $data);
    }

    /**
     * @depends testGetCategories
     */
    public function testGetChartDataWithGroupingByYearAndMultipleCategories(array $categories)
    {
        $this->assertCount(self::TEST_CATEGORY_COUNT, $categories, 'Category count differs from expected amount of categories which should be available via the fixtures. Please check the test database!');

        $client = static::createClient();
        $categoryName01 = 'category-1-1';
        $categoryId01 = $categories[$categoryName01] ?? 0;
        $categoryName02 = 'category-3';
        $categoryId02 = $categories[$categoryName02] ?? 0;
        $client->request('GET', sprintf('/chart/data?grouping=%d&categories=%d,%d', Transaction::GROUPING_YEARLY, $categoryId01, $categoryId02));
        $this->assertResponseStatusCodeSame(200);

        $content = $this->responseIsJson($client->getResponse());

        $this->assertArrayHasKey('success', $content);
        $this->assertIsBool($content['success']);
        $this->assertEquals(true, $content['success']);

        $expectedLabels = ['2020'];
        $labels = $this->checkResponseContentLabels($content);
        $this->assertIsArray($labels);
        $this->assertEquals($expectedLabels, $labels);

        $expectedData = [
            [
                'name' => $categoryName01,
                'id' => $categoryId01,
                'data' => [293.01],
            ],
            [
                'name' => $categoryName02,
                'id' => $categoryId02,
                'data' => [29.67],
            ],
        ];
        $data = $this->checkResponseContentData($content);
        $this->assertIsArray($data);
        $this->assertEquals($expectedData, $data);
    }

    /**
     * @depends testGetCategories
     */
    public function testGetChartDataWithGroupingByMonthAndMultipleCategories(array $categories)
    {
        $this->assertCount(self::TEST_CATEGORY_COUNT, $categories, 'Category count differs from expected amount of categories which should be available via the fixtures. Please check the test database!');

        $client = static::createClient();
        $categoryName01 = 'category-1-1';
        $categoryId01 = $categories[$categoryName01] ?? 0;
        $categoryName02 = 'category-3';
        $categoryId02 = $categories[$categoryName02] ?? 0;
        $client->request('GET', sprintf('/chart/data?grouping=%d&categories=%d,%d', Transaction::GROUPING_MONTHLY, $categoryId01, $categoryId02));
        $this->assertResponseStatusCodeSame(200);

        $content = $this->responseIsJson($client->getResponse());

        $this->assertArrayHasKey('success', $content);
        $this->assertIsBool($content['success']);
        $this->assertEquals(true, $content['success']);

        $expectedLabels = ['2020-05', '2020-06', '2020-07', '2020-08', '2020-09'];
        $labels = $this->checkResponseContentLabels($content);
        $this->assertIsArray($labels);
        $this->assertEquals($expectedLabels, $labels);

        $expectedData = [
            [
                'name' => $categoryName01,
                'id' => $categoryId01,
                'data' => [13, 157.59, 13.19, 46.64, 62.59],
            ],
            [
                'name' => $categoryName02,
                'id' => $categoryId02,
                'data' => [0, 0, 9.44, 11.98, 8.25],
            ],
        ];

        $data = $this->checkResponseContentData($content);
        $this->assertIsArray($data);
        $this->assertEquals($expectedData, $data);
    }

    private function responseIsJson(Response $response): array
    {
        $this->assertJson($response->getContent());

        return json_decode($response->getContent(), true);
    }

    private function checkResponseContentData(array $content): array
    {
        $this->assertArrayHasKey('data', $content);

        return $content['data'];
    }

    private function checkResponseContentLabels(array $content): array
    {
        $this->assertArrayHasKey('labels', $content);

        return $content['labels'];
    }
}
