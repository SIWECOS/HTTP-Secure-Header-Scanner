<?php

namespace App\Ratings;

use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;
use App\HTTPResponse;
use App\DOMXSSCheck;
use App\TranslateableMessage;

class SinksRating extends Rating
{

    public function __construct(HTTPResponse $response)
    {
        parent::__construct($response);

        $this->name = "SINKS";
        $this->scoreType = "info";
    }

    protected function rate()
    {
        /**
         * var $html voku\helper\SimpleHtmlDom;
         */
        $html = $this->getBody();

        if ($html->getIsDOMDocumentCreatedWithoutHtml()) {
            $this->hasError = true;
            $this->errorMessage = TranslateableMessage::get('NO_CONTENT');

        } else {

            $scriptTags = $html->find('script');

            if (count($scriptTags) == 0) {
                $this->score = 100;
                $this->testDetails->push(TranslateableMessage::get('NO_SCRIPT_TAGS'));

            } else {

                $this->score = 100;

                // Search for Sinks and Sources
                $sinkCounter = 0;
                foreach ($scriptTags as $scriptTag) {
                    if ($amountSinks = DOMXSSCheck::hasSinks($scriptTag->innertext, true))
                        $sinkCounter += $amountSinks;
                }

                if ($sinkCounter > 0) {
                    $this->score = 0;
                    $this->testDetails->push(TranslateableMessage::get('SINKSS_FOUND', ['AMOUNT' => $sinkCounter]));
                } else {
                    $this->testDetails->push(TranslateableMessage::get('NO_SINKS_FOUND'));
                }
            }
        }
    }
}
