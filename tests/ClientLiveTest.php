<?php
/**
 * This file is part of google-directions-client.
 *
 * (c) David Weichert <info@davidweichert.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MetaSyntactical\GoogleDirections;


use GuzzleHttp\Client as HttpClient;
use MetaSyntactical\Http\Transport\Guzzle\GuzzleTransport;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientLiveTest
 * @package MetaSyntactical\GoogleDirections
 *
 * This test demonstrates the usage of the client against the Google Directions
 * API. It will actually call the Google API using the API key read from the
 * environment variable API_KEY. If the environment variable is not present this
 * test is skipped.
 */
class ClientLiveTest extends TestCase
{
    /**
     * @var Client
     */
    protected $object;

    protected function setUp()
    {
        $googleApiKey = getenv('API_KEY');

        if ($googleApiKey)
        {
            $polylineDecoder = new Polyline();
            $this->object = new Client(
                $googleApiKey, $polylineDecoder, new GuzzleTransport(new HttpClient())
            );
        }
        else
        {
            self::markTestSkipped(
                'No Google API key present. Set $googleApiKey = "<Google Directions API Key>" in line 36 or set environment variable.'
            );
        }
    }

    /**
     * @dataProvider getDirectionsProvider()
     */
    public function testGetDirections(
        $coordinates, $remainingCountAfterFirstRequest, $initialRemainingCoordinates, $expectedNoRequests
    )
    {
        $actualNoRequests = 0;
        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        self::assertEquals($initialRemainingCoordinates, $route->getRemainingCoordinateCount());

        while ($route->getRemainingCoordinateCount())
        {
            $actual = $this->object->getDirections($route);
            $actualNoRequests++;
            if (1 == $actualNoRequests)
            {
                self::assertEquals($remainingCountAfterFirstRequest, $actual->getRemainingCoordinateCount());
            }
        }
        self::assertEquals($expectedNoRequests, $actualNoRequests);
        self::assertEquals(0, $actual->getRemainingCoordinateCount());
        self::assertTrue(is_array($route->getInterpolatedRoute()));
    }

    public function getDirectionsProvider()
    {
        return [
            [
                [
                    '50.1109756,8.6824697',  // Römer
                    '50.1131057,8.6935646',  // Allerheiligentor
                    '50.1114651,8.704576',   // "Frankfurter Küche"
                    '50.1128467,8.7049644',  // Ostbahnhof
                    '50.1173763,8.7084722',  // Habsburgerallee
                    '50.1292499,8.6924497',  // Nibelungenplatz/FH
                    '50.1320079,8.6829269',  // Deutsche Nationalbibliothek
                ],
                0,
                6,
                1
            ],
            [
                [
                    '50.1109756,8.6824697',  // Römer
                    '50.1131057,8.6935646',  // Allerheiligentor
                    '50.1114651,8.704576',   // "Frankfurter Küche"
                    '50.1128467,8.7049644',  // Ostbahnhof
                    '50.1173763,8.7084722',  // Habsburgerallee
                    '50.1292499,8.6924497',  // Nibelungenplatz/FH
                    '50.1320079,8.6829269',  // Deutsche Nationalbibliothek
                    '50.131774,8.672302',    // Adickesallee/Eschersheimer Landstraße
                    '50.128614, 8.672731',   // Holzhausenschule
                    '50.126392, 8.670925',   // Goethe-Universität
                    '50.1168304,8.6681944',  // Alte Oper
                    '50.110276, 8.662673',   // Westend Tower
                    '50.107456, 8.664766',   // Hauptbahnhof
                    '50.101777, 8.665700',   // Friedensbrücke
                    '50.099600, 8.673030',   // Rubensstraße/Kennedyallee
                    '50.101856, 8.676202',   // Morgensternstraße
                    '50.100451, 8.681827',   // Schweizer Straße
                    '50.101262, 8.685687',   // Textorstraße
                    '50.100623, 8.691451',   // Darmstädter Landstraße 79
                    '50.103538, 8.695242',   // Siemensstraße 18
                    '50.107623, 8.694887',   // Flößerbrücke
                    '50.110981, 8.703377',   // EZB
                    '50.111330, 8.711697',   // Osthafenplatz
                    '50.114860, 8.722239',   // Hanauer Landstraße 184
                    '50.118532, 8.731702',   // Hanauer Landstraße / A661
                    '50.121535, 8.728733',   // Ratsweg 10
                    '50.123178, 8.721735',   // Festplatz / Dippemess
                    '50.124605, 8.713721',   // Saalburgstraße / Wittelsbacher Allee
                    '50.126384, 8.708057',   // Bornheim Mitte
                    '50.127925, 8.702243',   // IGS Nordend
                    '50.130967, 8.698868',   // Wartburgkirche
                    '50.133372, 8.695542',   // Lidl Friedberger Landstraße
                    '50.140766, 8.698928',   // Friedberger Warte
                ],
                7,
                32,
                2
            ],
            [
                [
                    '52.54628, 13.30841',
                    '52.5477, 13.30894',
                    '52.54785, 13.3066',
                    '52.54804, 13.29709',
                    '52.54804, 13.2971',
                    '52.54804, 13.29709',
                    '52.54818, 13.29307',
                    '52.54843, 13.28076',
                    '52.54856, 13.27073',
                    '52.54836, 13.26295',
                    '52.54737, 13.25019',
                    '52.54712, 13.2479',
                    '52.54427, 13.24712',
                    '52.54027, 13.24761',
                    '52.53825, 13.24743',
                    '52.53951, 13.23448',
                    '52.53825, 13.23036',
                    '52.53693, 13.22073',
                    '52.5386, 13.20885',
                    '52.53998, 13.20451',
                    '52.54265, 13.19889',
                    '52.54623, 13.19186',
                    '52.54731, 13.18608',
                    '52.54939, 13.17537',
                    '52.55169, 13.16385',
                    '52.55349, 13.15461',
                    '52.55495, 13.1524',
                    '52.55491, 13.15202',
                    '52.55498, 13.152',
                    '52.55572, 13.14977',
                    '52.55792, 13.14958',
                    '52.55795, 13.14961',
                    '52.55848, 13.15446',
                    '52.55412, 13.15386',
                    '52.55177, 13.16241',
                    '52.55119, 13.1641',
                    '52.55117, 13.16406',
                    '52.55123, 13.16522',
                    '52.54932, 13.17544',
                    '52.54887, 13.1781',
                    '52.54907, 13.17862',
                    '52.54867, 13.17843',
                    '52.5508, 13.16724',
                    '52.55297, 13.15539',
                    '52.55478, 13.15246',
                    '52.55495, 13.15137',
                    '52.555, 13.15141',
                    '52.55743, 13.14943',
                    '52.55854, 13.15314',
                    '52.55799, 13.15584',
                    '52.55956, 13.15632',
                    '52.55955, 13.15638',
                    '52.56086, 13.15247',
                    '52.56085, 13.15229',
                    '52.55854, 13.15611',
                    '52.55491, 13.15205',
                    '52.55528, 13.14974',
                    '52.55492, 13.15188',
                    '52.55368, 13.15365',
                    '52.55183, 13.1616',
                    '52.55067, 13.16766',
                    '52.54879, 13.17764',
                    '52.54993, 13.17866',
                    '52.55255, 13.1791',
                    '52.55184, 13.1785',
                    '52.55039, 13.1778',
                    '52.54956, 13.17387',
                    '52.55176, 13.1625',
                    '52.55351, 13.15363',
                    '52.55995, 13.15545',
                    '52.56023, 13.15443',
                    '52.56006, 13.1549',
                    '52.55971, 13.1561',
                    '52.56349, 13.15916',
                    '52.56329, 13.16436',
                    '52.56085, 13.17199',
                    '52.56175, 13.16937',
                    '52.56309, 13.16512',
                    '52.56357, 13.16356',
                    '52.5612, 13.15768',
                    '52.55519, 13.1542',
                    '52.55545, 13.1526',
                    '52.55526, 13.15381',
                    '52.5533, 13.15387',
                    '52.55121, 13.1647',
                    '52.55269, 13.16603',
                    '52.55553, 13.16981',
                    '52.55358, 13.17776',
                    '52.55288, 13.1773',
                    '52.55293, 13.17726',
                    '52.5535, 13.17778',
                    '52.5552, 13.1711',
                    '52.55392, 13.16623',
                    '52.55147, 13.16538',
                    '52.54839, 13.1612',
                    '52.54921, 13.15464',
                    '52.55279, 13.15655',
                    '52.55156, 13.16197',
                    '52.55156, 13.16197',
                    '52.54847, 13.16124',
                    '52.54909, 13.15449',
                    '52.55269, 13.15331',
                    '52.55785, 13.1559',
                    '52.55597, 13.15824',
                    '52.55574, 13.15802',
                    '52.5547, 13.15747',
                    '52.55496, 13.1554',
                    '52.55497, 13.1554',
                    '52.55341, 13.15341',
                    '52.55481, 13.14647',
                    '52.55474, 13.14277',
                    '52.55558, 13.1433',
                    '52.5542, 13.14867',
                    '52.5532, 13.15166',
                    '52.55301, 13.15162',
                    '52.55229, 13.15309',
                    '52.55218, 13.1593',
                    '52.55115, 13.16453',
                    '52.54987, 13.17188',
                    '52.55413, 13.17457',
                    '52.55349, 13.17598',
                    '52.55357, 13.17593',
                    '52.55321, 13.17578',
                    '52.55244, 13.17901',
                    '52.54948, 13.17837',
                    '52.54945, 13.17837',
                    '52.54937, 13.1785',
                    '52.55159, 13.17832',
                    '52.55425, 13.17518',
                    '52.55407, 13.17442',
                    '52.55393, 13.17428',
                    '52.55185, 13.17298',
                    '52.55119, 13.16562',
                    '52.55209, 13.16083',
                    '52.55342, 13.15353',
                    '52.55208, 13.15343',
                    '52.55239, 13.1531',
                    '52.54963, 13.15093',
                    '52.54923, 13.1508',
                    '52.54917, 13.15082',
                    '52.54868, 13.15072',
                    '52.54684, 13.1518',
                    '52.54945, 13.15485',
                    '52.55227, 13.15972',
                    '52.55079, 13.16743',
                    '52.55267, 13.17359',
                    '52.55272, 13.1737',
                    '52.55272, 13.1737',
                    '52.55045, 13.17212',
                    '52.55117, 13.16569',
                    '52.55314, 13.15502',
                    '52.55206, 13.15312',
                    '52.54834, 13.15462',
                    '52.54684, 13.15213',
                    '52.54692, 13.15184',
                    '52.54716, 13.15051',
                    '52.5475, 13.1545',
                    '52.54929, 13.15432',
                    '52.54928, 13.15423',
                    '52.54898, 13.15076',
                    '52.54886, 13.15074',
                    '52.55028, 13.15112',
                    '52.55353, 13.15297',
                    '52.55135, 13.16418',
                    '52.54995, 13.172',
                    '52.54994, 13.17383',
                    '52.54994, 13.17381',
                    '52.55064, 13.1688',
                    '52.55217, 13.16062',
                    '52.55109, 13.15518',
                    '52.55064, 13.1565',
                    '52.55068, 13.15643',
                    '52.55079, 13.15521',
                    '52.54967, 13.15133',
                    '52.54729, 13.15038',
                    '52.54961, 13.14966',
                    '52.55149, 13.14863',
                    '52.55397, 13.14934',
                    '52.55316, 13.15523',
                    '52.55122, 13.1653',
                    '52.54999, 13.17202',
                    '52.54965, 13.17565',
                    '52.54965, 13.17553',
                    '52.55047, 13.16953',
                    '52.55183, 13.1624',
                    '52.55341, 13.15421',
                    '52.55353, 13.14902',
                    '52.55319, 13.14892',
                    '52.55359, 13.1527',
                    '52.55233, 13.15549',
                    '52.55061, 13.15661',
                    '52.55061, 13.15661',
                    '52.55062, 13.15661',
                    '52.55205, 13.15836',
                    '52.55126, 13.16512',
                    '52.55024, 13.1706',
                    '52.55259, 13.17295',
                    '52.55257, 13.17296',
                    '52.55049, 13.17217',
                    '52.54898, 13.17721',
                    '52.54884, 13.17798',
                    '52.54945, 13.17851',
                    '52.54945, 13.17849',
                    '52.54769, 13.18415',
                    '52.54608, 13.19281',
                    '52.54127, 13.20123',
                    '52.54046, 13.20262',
                    '52.53912, 13.2067',
                    '52.5384, 13.21179',
                    '52.53791, 13.21674',
                    '52.53781, 13.22786',
                    '52.53897, 13.23627',
                    '52.53824, 13.24567',
                    '52.54261, 13.24727',
                    '52.54667, 13.24808',
                    '52.54799, 13.2558',
                    '52.54869, 13.26833',
                    '52.54861, 13.2763',
                    '52.54843, 13.28716',
                    '52.54811, 13.29825',
                    '52.54664, 13.30901',
                    '52.54615, 13.30848',
                    '52.54617, 13.30847',
                    '52.54616, 13.3085',
                    '52.54796, 13.30745',
                    '52.54817, 13.29705',
                    '52.54817, 13.29706',
                    '52.54828, 13.29122',
                    '52.54853, 13.27933',
                    '52.54867, 13.26514',
                    '52.54757, 13.25076',
                    '52.54744, 13.24903',
                    '52.54735, 13.24835',
                    '52.54435, 13.24606',
                    '52.5439, 13.24356',
                    '52.54308, 13.24047',
                    '52.54226, 13.23707',
                    '52.54099, 13.23322',
                    '52.53957, 13.23206',
                    '52.53928, 13.23187',
                    '52.53701, 13.22051',
                    '52.53869, 13.20844',
                    '52.53998, 13.20466',
                    '52.54163, 13.20086',
                    '52.5442, 13.19645',
                    '52.5462, 13.19219',
                    '52.54661, 13.18968',
                    '52.54675, 13.18879',
                    '52.54695, 13.18783',
                    '52.54738, 13.18563',
                    '52.54757, 13.1846',
                    '52.54798, 13.18253',
                    '52.54825, 13.18103',
                    '52.54874, 13.1786',
                    '52.55078, 13.1677',
                    '52.55233, 13.15954',
                    '52.55199, 13.15839',
                    '52.55181, 13.15704',
                    '52.55115, 13.16073',
                    '52.55107, 13.1609',
                    '52.5507, 13.16153',
                    '52.55119, 13.16515',
                    '52.55463, 13.16657',
                    '52.55526, 13.17111',
                    '52.55474, 13.17298',
                    '52.55471, 13.17301',
                    '52.55581, 13.16852',
                    '52.55586, 13.16816',
                    '52.55653, 13.16426',
                    '52.55639, 13.16558',
                    '52.55335, 13.16618',
                    '52.55062, 13.16507',
                    '52.54908, 13.16145',
                    '52.55179, 13.15935',
                    '52.55067, 13.15528',
                    '52.54879, 13.15801',
                    '52.54858, 13.16004',
                    '52.54971, 13.16164',
                    '52.54971, 13.16164',
                    '52.55029, 13.16173',
                    '52.55123, 13.16557',
                    '52.55176, 13.16841',
                    '52.55557, 13.16936',
                    '52.55425, 13.17473',
                    '52.55585, 13.16831',
                    '52.55587, 13.16692',
                    '52.55589, 13.167',
                    '52.5558, 13.16838',
                    '52.55279, 13.16869',
                    '52.55109, 13.16632',
                    '52.55167, 13.16656',
                    '52.55184, 13.16586',
                    '52.55575, 13.16685',
                    '52.55577, 13.16685',
                    '52.55348, 13.16886',
                    '52.55168, 13.16838',
                    '52.55165, 13.1684',
                    '52.55111, 13.16822',
                    '52.55099, 13.16821',
                    '52.55079, 13.16781',
                    '52.55183, 13.16203',
                    '52.55069, 13.1618',
                    '52.54991, 13.16038',
                    '52.54915, 13.15898',
                    '52.54915, 13.15897',
                    '52.549, 13.15718',
                    '52.5491, 13.1557',
                    '52.54909, 13.15571',
                    '52.54858, 13.16037',
                    '52.55043, 13.16501',
                    '52.55235, 13.16602',
                    '52.55323, 13.16621',
                    '52.55547, 13.16675',
                    '52.55427, 13.16643',
                    '52.55137, 13.16494',
                    '52.55336, 13.15418',
                    '52.55505, 13.14619',
                    '52.55776, 13.13669',
                    '52.55951, 13.13045',
                    '52.56057, 13.12657',
                    '52.56313, 13.11746',
                    '52.56483, 13.11152',
                    '52.56695, 13.10371',
                    '52.56824, 13.09835',
                    '52.57209, 13.08879',
                    '52.57153, 13.08927',
                    '52.57153, 13.08925',
                    '52.57143, 13.08921',
                    '52.57166, 13.09005',
                    '52.56994, 13.096',
                    '52.56635, 13.10266',
                    '52.56248, 13.09489',
                    '52.56063, 13.09184',
                    '52.55739, 13.09193',
                    '52.55882, 13.08402',
                    '52.56183, 13.07749',
                    '52.56289, 13.07454',
                    '52.56292, 13.07428',
                    '52.56279, 13.074',
                    '52.56324, 13.06407',
                    '52.56227,13.05235'
                ],
                315,
                340,
                14
            ]
        ];
    }
}
