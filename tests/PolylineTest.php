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

use Gamez\Psr\Log\TestLogger;
use PHPUnit_Framework_TestCase;

class PolylineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Polyline
     */
    protected $object;

    /**
     * @var TestLogger
     */
    protected $logger;

    protected function setUp()
    {
        $this->object = new Polyline();
        $this->logger = new TestLogger();
    }

    /**
     * @dataProvider decodeProvider
     */
    public function testDecode($polyline, $coordinates, $loggerMsg)
    {
        $this->object->setLogger($this->logger);

        $decodeResult = $this->object->decode($polyline);

        for ($i = 0; $i < count($coordinates); $i++)
        {
            $coordinateArray = explode(',', $coordinates[$i]);
            self::assertEquals($coordinateArray[0], $decodeResult[$i]->getLatitude());
            self::assertEquals($coordinateArray[1], $decodeResult[$i]->getLongitude());
        }

        if ($loggerMsg)
        {
            self::assertTrue($this->logger->hasRecord($loggerMsg));
        }
    }

    public function decodeProvider()
    {
        return [
            ['i}p`Im__qA', ['52.68453,13.43495'], false],
            ['`~oia@', [], 'Given polyline (`~oia@) yielded invalid coordinate (-179.98321, -1.0E-5). Latitude -179.98321 lesser than allowed (-85).'],
            [
                'o}u_IukfpAcBBqDGUCALGpEQfQG~NGxJO~UC`ECjDBd@EbBCbBg@hz@]fq@Mlc@CjGIzP@`BFlBTfI|Cx`Ax@`UXlFBvAANx@MpA[dAOvBA~@Pv@`@hBvANNp@f@Il@QrBEpAP`D`A`GjE~VdCzNhDnST|@Zt@l@~@^x@LxACr@P?j@El@?z@H`Ct@Vt@\\p@|AbCTf@Pn@PpAxDrh@TxCZfFBzCKfCW|B[vAiB~GQp@i@vDe@tDWjBQlBKhB@zBLtDRbFCvAQjDWxEIr@O`AWbAoD`NS|@e@xAi@`AIAYDULOLQXIZGn@@l@MnBStA[nAcArCkAfC_CbFsAfCiE`IkEfIaAtAe@h@m@jAK`@YnAiAlJsBrRsA~LgClUaCvTu@lGm@zFu@lH@',
                [
                    "52.54632, 13.30891",
                    "52.54682, 13.30889",
                    "52.54771, 13.30893",
                    "52.54782, 13.30895",
                    "52.54783, 13.30888",
                    "52.54787, 13.30783",
                    "52.54796, 13.30491",
                    "52.548, 13.30235",
                    "52.54804, 13.30046",
                    "52.54812, 13.29678",
                    "52.54814, 13.29581",
                    "52.54816, 13.29495",
                    "52.54814, 13.29476",
                    "52.54817, 13.29426",
                    "52.54819, 13.29376",
                    "52.54839, 13.28427",
                    "52.54854, 13.27623",
                    "52.54861, 13.2704",
                    "52.54863, 13.26906",
                    "52.54868, 13.2662",
                    "52.54867, 13.26571",
                    "52.54863, 13.26516",
                    "52.54852, 13.26352",
                    "52.54773, 13.25299",
                    "52.54744, 13.24946",
                    "52.54731, 13.24827",
                    "52.54729, 13.24783",
                    "52.5473, 13.24775",
                    "52.54701, 13.24782",
                    "52.5466, 13.24796",
                    "52.54625, 13.24804",
                    "52.54565, 13.24805",
                    "52.54533, 13.24796",
                    "52.54505, 13.24779",
                    "52.54452, 13.24735",
                    "52.54444, 13.24727",
                    "52.54419, 13.24707",
                    "52.54424, 13.24684",
                    "52.54433, 13.24626",
                    "52.54436, 13.24585",
                    "52.54427, 13.24504",
                    "52.54394, 13.24375",
                    "52.54292, 13.23991",
                    "52.54225, 13.23737",
                    "52.5414, 13.23409",
                    "52.54129, 13.23378",
                    "52.54115, 13.23351",
                    "52.54092, 13.23319",
                    "52.54076, 13.2329",
                    "52.54069, 13.23245",
                    "52.54071, 13.23219",
                    "52.54062, 13.23219",
                    "52.5404, 13.23222",
                    "52.54017, 13.23222",
                    "52.53987, 13.23217",
                    "52.53922, 13.2319",
                    "52.5391, 13.23163",
                    "52.53895, 13.23138",
                    "52.53848, 13.23072",
                    "52.53837, 13.23052",
                    "52.53828, 13.23028",
                    "52.53819, 13.22987",
                    "52.53726, 13.22321",
                    "52.53715, 13.22244",
                    "52.53701, 13.22128",
                    "52.53699, 13.2205",
                    "52.53705, 13.21982",
                    "52.53717, 13.21919",
                    "52.53731, 13.21875",
                    "52.53784, 13.21731",
                    "52.53793, 13.21706",
                    "52.53814, 13.21614",
                    "52.53833, 13.21523",
                    "52.53845, 13.21469",
                    "52.53854, 13.21414",
                    "52.5386, 13.21361",
                    "52.53859, 13.21299",
                    "52.53852, 13.21208",
                    "52.53842, 13.21094",
                    "52.53844, 13.2105",
                    "52.53853, 13.20964",
                    "52.53865, 13.20855",
                    "52.5387, 13.20829",
                    "52.53878, 13.20796",
                    "52.5389, 13.20762",
                    "52.53978, 13.20521",
                    "52.53988, 13.2049",
                    "52.54007, 13.20445",
                    "52.54028, 13.20412",
                    "52.54033, 13.20413",
                    "52.54046, 13.2041",
                    "52.54057, 13.20403",
                    "52.54065, 13.20396",
                    "52.54074, 13.20383",
                    "52.54079, 13.20369",
                    "52.54083, 13.20345",
                    "52.54082, 13.20322",
                    "52.54089, 13.20266",
                    "52.54099, 13.20223",
                    "52.54113, 13.20183",
                    "52.54147, 13.20109",
                    "52.54185, 13.20041",
                    "52.54249, 13.19927",
                    "52.54291, 13.19859",
                    "52.54392, 13.19698",
                    "52.54494, 13.19534",
                    "52.54527, 13.19491",
                    "52.54546, 13.1947",
                    "52.54569, 13.19432",
                    "52.54575, 13.19415",
                    "52.54588, 13.19375",
                    "52.54625, 13.19192",
                    "52.54683, 13.18878",
                    "52.54725, 13.18654",
                    "52.54793, 13.18295",
                    "52.54858, 13.17947",
                    "52.54885, 13.17812",
                    "52.54908, 13.17686",
                    "52.54935, 13.17535",
                    "52.54934, 13.17534"
                ],
                false
            ]
        ];
    }

}
