<?php
/**
 * This file is part of google-directions-client.
 *
 * (c) David Weichert <info@davidweichert.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Consoserv\GoogleDirections;


use Assert\Assertion;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use MetaSyntactical\Http\Transport\Guzzle\GuzzleTransport;
use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    private $container = [];

    /**
     * @dataProvider getDirectionsProvider
     */
    public function testGetDirections200($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        self::markTestIncomplete('Implement test with 200 HTTP response.');
    }

    /**
     * @dataProvider getDirectionsProvider
     */
    public function testGetDirections500($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        self::markTestIncomplete('Implement test with 500 HTTP response.');
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
                6
            ]
        ];
    }

    private function getSuccessMock()
    {
        $mock = new MockHandler([
                new Response(200, ["X-Foo" => "Bar"], "It worked!")
            ]);
            $stack = HandlerStack::create($mock);

            $history = Middleware::history($this->container);
            $stack->push($history);

            $client = new HttpClient(["handler" => $stack]);

            $this->object = new GuzzleTransport($client);
    }
}