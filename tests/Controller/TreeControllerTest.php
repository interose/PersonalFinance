<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TreeControllerTest extends WebTestCase
{
    public function testGetTreeDataFullYearAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tree/get-data-full-year?year=2020');
        $this->assertResponseStatusCodeSame(200);
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);
        $contentDecoded = json_decode($content, true);

        // assert tree structure
        $this->assertCount(3, $contentDecoded['children']);

        $this->assertEquals('group-1', $contentDecoded['children'][0]['name']);
        $group1 = $contentDecoded['children'][0];
        $this->assertEquals(0, $group1['month01']);
        $this->assertEquals(0, $group1['month02']);
        $this->assertEquals(0, $group1['month03']);
        $this->assertEquals(0, $group1['month04']);
        $this->assertEquals(-13, $group1['month05']);
        $this->assertEquals(-541.88, $group1['month06']);
        $this->assertEquals(-22.33, $group1['month07']);
        $this->assertEquals(-124.29, $group1['month08']);
        $this->assertEquals(-88.55, $group1['month09']);
        $this->assertEquals(0, $group1['month10']);
        $this->assertEquals(0, $group1['month11']);
        $this->assertEquals(0, $group1['month12']);

        $this->assertEquals('group-2', $contentDecoded['children'][1]['name']);
        $group2 = $contentDecoded['children'][1];
        $this->assertEquals(0, $group2['month01']);
        $this->assertEquals(0, $group2['month02']);
        $this->assertEquals(0, $group2['month03']);
        $this->assertEquals(0, $group2['month04']);
        $this->assertEquals(0, $group2['month05']);
        $this->assertEquals(0, $group2['month06']);
        $this->assertEquals(-72.1, $group2['month07']);
        $this->assertEquals(-131.7, $group2['month08']);
        $this->assertEquals(-95.26, $group2['month09']);
        $this->assertEquals(0, $group2['month10']);
        $this->assertEquals(0, $group2['month11']);
        $this->assertEquals(0, $group2['month12']);

        $this->assertEquals('category-1-1', $group1['children'][0]['name']);
        $cat11 = $group1['children'][0];
        $this->assertEquals(0, $cat11['month01']);
        $this->assertEquals(0, $cat11['month02']);
        $this->assertEquals(0, $cat11['month03']);
        $this->assertEquals(0, $cat11['month04']);
        $this->assertEquals(-13, $cat11['month05']);
        $this->assertEquals(-157.59, $cat11['month06']);
        $this->assertEquals(-13.19, $cat11['month07']);
        $this->assertEquals(-46.64, $cat11['month08']);
        $this->assertEquals(-62.59, $cat11['month09']);
        $this->assertEquals(0, $cat11['month10']);
        $this->assertEquals(0, $cat11['month11']);
        $this->assertEquals(0, $cat11['month12']);

        $this->assertEquals('category-1-2', $group1['children'][1]['name']);
        $cat12 = $group1['children'][1];
        $this->assertEquals(0, $cat12['month01']);
        $this->assertEquals(0, $cat12['month02']);
        $this->assertEquals(0, $cat12['month03']);
        $this->assertEquals(0, $cat12['month04']);
        $this->assertEquals(0, $cat12['month05']);
        $this->assertEquals(-384.29, $cat12['month06']);
        $this->assertEquals(-9.14, $cat12['month07']);
        $this->assertEquals(-77.65, $cat12['month08']);
        $this->assertEquals(-25.96, $cat12['month09']);
        $this->assertEquals(0, $cat12['month10']);
        $this->assertEquals(0, $cat12['month11']);
        $this->assertEquals(0, $cat12['month12']);

        $this->assertEquals('category-2-1', $group2['children'][0]['name']);
        $cat21 = $group2['children'][0];
        $this->assertEquals(0, $cat21['month01']);
        $this->assertEquals(0, $cat21['month02']);
        $this->assertEquals(0, $cat21['month03']);
        $this->assertEquals(0, $cat21['month04']);
        $this->assertEquals(0, $cat21['month05']);
        $this->assertEquals(0, $cat21['month06']);
        $this->assertEquals(-46.91, $cat21['month07']);
        $this->assertEquals(-97.27, $cat21['month08']);
        $this->assertEquals(-41.17, $cat21['month09']);
        $this->assertEquals(0, $cat21['month10']);
        $this->assertEquals(0, $cat21['month11']);
        $this->assertEquals(0, $cat21['month12']);

        $this->assertEquals('category-2-2', $group2['children'][1]['name']);
        $cat22 = $group2['children'][1];
        $this->assertEquals(0, $cat22['month01']);
        $this->assertEquals(0, $cat22['month02']);
        $this->assertEquals(0, $cat22['month03']);
        $this->assertEquals(0, $cat22['month04']);
        $this->assertEquals(0, $cat22['month05']);
        $this->assertEquals(0, $cat22['month06']);
        $this->assertEquals(-25.19, $cat22['month07']);
        $this->assertEquals(-34.43, $cat22['month08']);
        $this->assertEquals(-54.09, $cat22['month09']);
        $this->assertEquals(0, $cat22['month10']);
        $this->assertEquals(0, $cat22['month11']);
        $this->assertEquals(0, $cat22['month12']);


        $this->assertEquals('Gesamt', $contentDecoded['children'][2]['name']);
        $sum = $contentDecoded['children'][2];
        $this->assertEquals(0, $sum['month01']);
        $this->assertEquals(0, $sum['month02']);
        $this->assertEquals(0, $sum['month03']);
        $this->assertEquals(0, $sum['month04']);
        $this->assertEquals(-13, $sum['month05']);
        $this->assertEquals(-541.88, $sum['month06']);
        $this->assertEquals(-94.43, $sum['month07']);
        $this->assertEquals(-255.99, $sum['month08']);
        $this->assertEquals(-183.81, $sum['month09']);
        $this->assertEquals(0, $sum['month10']);
        $this->assertEquals(0, $sum['month11']);
        $this->assertEquals(0, $sum['month12']);
    }
}

