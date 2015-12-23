<?php
/**
 * This file is part of google-directions-client.
 *
 * (c) David Weichert <info@davidweichert.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!file_exists(__DIR__ . '/../vendor/autoload.php'))
{
    throw new Exception('Vendor dependencies missing.');
}
require_once __DIR__ . '/../vendor/autoload.php';
