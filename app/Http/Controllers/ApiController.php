<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    /**
     * Returns the Report for a single URL.
     *
     * @param Request $request (GET parameter "site")
     * @return Report
     */
    public function report(Request $request) {
        $this->validate($request, [
            'site' => 'required|url'
        ]);

        return new Report($request->input('site'));
    }


    /**
     * Returns a very simple and report for a single URL.
     */
    public function siwecosReport(Request $request) {
        $this->validate($request, [
            'site' => 'required|url'
        ]);

        $report = new Report($request->input('site'));

        $customReport = [
            'checks' => [
                'Content-Type' => [
                    'result' => !(strpos($report->ContentTypeRating->getRating(), 'C') !== false),
                    'comment' => $report->ContentTypeRating->getComment(),
                    'header' => $report->ContentTypeRating->getHeader()
                ],
                'Content-Security-Policy' => [
                    'result' => !(strpos($report->ContentSecurityPolicyRating->getRating(), 'C') !== false),
                    'comment' => $report->ContentSecurityPolicyRating->getComment(),
                    'header' => $report->ContentSecurityPolicyRating->getHeader()
                ],
                'Public-Key-Pins' => [
                    'result' => !(strpos($report->HttpPublicKeyPinningRating->getRating(), 'C') !== false),
                    'comment' => $report->HttpPublicKeyPinningRating->getComment(),
                    'header' => $report->HttpPublicKeyPinningRating->getHeader()
                ],
                'Strict-Transport-Security' => [
                    'result' => !(strpos($report->HttpStrictTransportSecurityRating->getRating(), 'C') !== false),
                    'comment' => $report->HttpStrictTransportSecurityRating->getComment(),
                    'header' => $report->HttpStrictTransportSecurityRating->getHeader()
                ],
                'X-Content-Type-Options' => [
                    'result' => !(strpos($report->XContentTypeOptionsRating->getRating(), 'C') !== false),
                    'comment' => $report->XContentTypeOptionsRating->getComment(),
                    'header' => $report->XContentTypeOptionsRating->getHeader()
                ],
                'X-Frame-Options' => [
                    'result' => !(strpos($report->XFrameOptionsRating->getRating(), 'C') !== false),
                    'comment' => $report->XFrameOptionsRating->getComment(),
                    'header' => $report->XFrameOptionsRating->getHeader()
                ],
                'X-Xss-Protection' => [
                    'result' => !(strpos($report->XXSSProtectionRating->getRating(), 'C') !== false),
                    'comment' => $report->XXSSProtectionRating->getComment(),
                    'header' => $report->XXSSProtectionRating->getHeader()
                ]
            ]
        ];

        return $customReport;
    }

    /**
     * Returns an array with the crawled links.
     *
     * @param Request $request
     */
    public function links(Request $request) {
        // TODO: Implement crawler return
    }

}