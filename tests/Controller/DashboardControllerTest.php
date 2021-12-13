<?php

namespace App\Tests\Controller;

use App\Lib\MyDateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();
        $container = static::getContainer();

        $myDateTimeMock = $this->getMockBuilder(MyDateTime::class)
            ->disableOriginalConstructor()
            ->getMock();
        $myDateTimeMock->method('getToday')->willReturnCallback(function () {
            return new \DateTime('2020-11-30 00:00:00');
        });
        $container->set('App\Lib\MyDateTime', $myDateTimeMock);

        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextSame('span#data_subaccount_name', 'This is a Testaccount');
        $this->assertSelectorTextContains('span#data_subaccount_balance', '5.000,00');
        $this->assertSelectorTextSame('span#data_subaccount_description', 'This is a Testdescription');

        $this->assertSelectorTextContains('span#data_savings_rate', '625');
        $this->assertSelectorTextContains('span#data_luxury_rate', '1011');
    }

    public function testGetMonthlyRemaining()
    {
        $client = static::createClient();
        $container = static::getContainer();

        $myDateTimeMock = $this->getMockBuilder(MyDateTime::class)
            ->disableOriginalConstructor()
            ->getMock();
        $myDateTimeMock->method('getToday')->willReturnCallback(function () {
            return new \DateTime('2020-10-30 00:00:00');
        });
        $container->set('App\Lib\MyDateTime', $myDateTimeMock);

        $client->request('GET', '/dashboard/get-monthly-remaining');
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $expectedCategories = ['Mai', 'Jun', 'Jul', 'Aug', 'Sep'];
        $this->assertArrayHasKey('categories', $contentDecoded);
        $categories = $contentDecoded['categories'];
        $this->assertIsArray($categories);
        $this->assertCount(count($expectedCategories), $categories);
        $this->assertEquals($expectedCategories, $categories);

        $expectedData = [-13, -801.69, -103.87, -299.77, -72.06];
        $this->assertArrayHasKey('data', $contentDecoded);
        $data = $contentDecoded['data'];
        $this->assertIsArray($data);
        $this->assertCount(count($expectedData), $data);
        $this->assertEquals($expectedData, $data);
    }

    public function testGetAccountProgress()
    {
        $client = static::createClient();
        $container = static::getContainer();

        $myDateTimeMock = $this->getMockBuilder(MyDateTime::class)
            ->disableOriginalConstructor()
            ->getMock();
        $myDateTimeMock->method('getToday')->willReturnCallback(function () {
            return new \DateTime('2020-09-20 00:00:00');
        });
        $container->set('App\Lib\MyDateTime', $myDateTimeMock);

        $client->request('GET', '/dashboard/get-account-progress');
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        $this->assertArrayHasKey('success', $contentDecoded);
        $this->assertIsBool($contentDecoded['success']);
        $this->assertEquals(true, $contentDecoded['success']);

        $expectedCategories = ['01.', '02.', '03.', '04.', '05.', '06.', '07.', '08.', '09.', '10.', '11.', '12.', '13.', '14.', '15.', '16.', '17.', '18.', '19.', '20.', '21.', '22.', '23.', '24.', '25.', '26.', '27.', '28.', '29.', '30.', '31.'];
        $this->assertArrayHasKey('categories', $contentDecoded);
        $categories = $contentDecoded['categories'];
        $this->assertIsArray($categories);
        $this->assertCount(count($expectedCategories), $categories);
        $this->assertEquals($expectedCategories, $categories);

        $expectedSeriesCurrentMonth = [5072.06, 4983.51, 4888.25, 4888.25, 4880, 4880, 4880, 4880, 4880, 4880, 4880, 4880, 4880, 4880, 4880, 5000, 5000, 5000, 5000, 5000];
        $this->assertArrayHasKey('seriesCurrentMonth', $contentDecoded);
        $seriesCurrentMonth = $contentDecoded['seriesCurrentMonth'];
        $this->assertIsArray($seriesCurrentMonth);
        $this->assertCount(count($expectedSeriesCurrentMonth), $seriesCurrentMonth);
        $this->assertEquals($expectedSeriesCurrentMonth, $seriesCurrentMonth);

        $expectedSeriesPreviousMonth = [5371.83, 5371.83, 5319.44, 5319.44, 5319.44, 5271.97, 5271.97, 5271.97, 5264.95, 5264.95, 5264.95, 5233.15, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5148.92, 5143.96, 5143.96, 5072.06, 5072.06, 5072.06, 5072.06, 5072.06, 5072.06];
        $this->assertArrayHasKey('seriesPreviousMonth', $contentDecoded);
        $seriesPreviousMonth = $contentDecoded['seriesPreviousMonth'];
        $this->assertIsArray($seriesPreviousMonth);
        $this->assertCount(count($expectedSeriesPreviousMonth), $seriesPreviousMonth);
        $this->assertEquals($expectedSeriesPreviousMonth, $seriesPreviousMonth);
    }
}
