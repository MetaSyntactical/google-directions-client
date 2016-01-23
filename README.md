# README

[![Build Status](https://travis-ci.org/MetaSyntactical/google-directions-client.svg?branch=master)](https://travis-ci.org/MetaSyntactical/google-directions-client)
[![Downloads this Month](https://img.shields.io/packagist/dm/metasyntactical/google-directions-client.svg?style=flat)](https://packagist.org/packages/metasyntactical/google-directions-client)
[![Latest stable](https://img.shields.io/packagist/v/metasyntactical/google-directions-client.svg?style=flat&label=stable)](https://packagist.org/packages/metasyntactical/google-directions-client)
[![Latest dev](https://img.shields.io/packagist/vpre/metasyntactical/google-directions-client.svg?style=flat&label=unstable)](https://packagist.org/packages/metasyntactical/google-directions-client)
[![License](https://img.shields.io/packagist/l/metasyntactical/google-directions-client.svg?style=flat&label=license)](https://packagist.org/packages/metasyntactical/google-directions-client)
[![Code Climate](https://codeclimate.com/github/MetaSyntactical/google-directions-client/badges/gpa.svg)](https://codeclimate.com/github/MetaSyntactical/google-directions-client)

## What is MetaSyntactical/GoogleDirections?

MetaSyntactical/GoogleDirections is a PHP client library for the
[Google Maps Directions API](https://developers.google.com/maps/documentation/directions).
Its purpose is to calculate a route between given geographical
coordinates.

## Prerequisites for using the Google Directions API

To use this library you will need a Google API key which can be
obtained from Google. Usage of the Google API is subject to Terms and
Conditions set out by Google. Depending on the way of you want to use
Google's API and the amount of traffic/requests Google may charge
fees. Please refer to
[Google Maps Directions API](https://developers.google.com/maps/documentation/directions)
for further details.

## Installation

The easiest way to install the library is adding it as a dependency to your
project's composer.json file.

    $ composer require metasyntactical/google-directions "~1.0"
    
## Usage

To use the library create an instance of the client class:

```
use MetaSyntactical\GoogleDirections\Client as GoogleApiClient;
use MetaSyntactical\GoogleDirections\Polyline as Polyline;
use MetaSyntactical\GoogleDirections\RouteFactory;
use GuzzleHttp\Client as HttpClient;
use MetaSyntactical\Http\Transport\Guzzle\GuzzleTransport;

$guzzleTransport = new GuzzleTransport(new HttpClient());

$polylineDecoder = new Polyline();

$googleApiClient = new GoogleApiClient(
    'string', // <= your Google API key
    $polylineDecoder,
    $guzzleTransport
);
```

The client class has a few dependencies that must be passed to the constructor:

- the GuzzleTransport object, which contains the HTTP client and is used by the
  Google API client to handle the HTTP communication
- the Polyline object, which has a method to decode
  [Google Polylines](https://developers.google.com/maps/documentation/utilities/polylinealgorithm)
  (kudos to Peter Chng)

You can optionally set a logger on the Polyline object to collect messages. The
logger object you pass must implement the [PSR-3 logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md).

    polylineDecoder->setLogger($logger);

The client uses a Route object as a value object to handle the geographical
coordinates. The easiest way to obtain a route object is via the factory, which
will take an array of comma-separated coordinates (lat,long) for which you want
to calculate a route:

```
$coordinates = array(
    '50.1109756,8.6824697',
    '50.1131057,8.6935646',
    '50.1114651,8.704576',
    '50.1128467,8.7049644'
);
$routeFactory = new RouteFactory();
$route = $routeFactory->createRoute($coordinates);
```

Note that the order of coordinates is relevant, meaning the route will
start at the first given coordinate and pass through any additional coordinate
in the given order up to the last given coordinate.

As with the Polyline object, you can pass a PSR-3 logger to the route
factory to collect messages:

    $routeFactory->setLogger($logger);

To get an interpolated route call the getDirections() method of the Google API
client and pass the route object as an argument:

    $resultRoute = $googleApiClient->getDirections($route);

The return value will be a Route object. Depending on the size of the route
(number of coordinates), one API call (i.e. one call of the getDirections()
method will only yield a partial result. This is due to the fact that the
Google API handles only a limited number of waypoints per request.

You can check if there are coordinates left in the route for which additional
calls to the Google API need to be made. In order to do so use the
getRemainingCoordinateCount() method.

    $resultRoute->getRemainingCoordinateCount();

If the count is greater than zero, just call the getDirections() method again
with the result route as an argument and the Google API is called again yielding
another portion of the route:

    $resultRoute = $googleApiClient->getDirections($route);

Once there are no more remaining coordinates, all coordinates that make up the
interpolated route can be retrieved by calling the getInterpolatedRoute()
method on the result route object.

    $resultRoute->getInterpolatedRoute();

Note that the getInterpolatedRoute() method returns an array of Coordinate
objects, that have methods to get the values for latitude and longitude:

```
$coordinate->getLatitude();
$coordinate->getLongitude();
```
