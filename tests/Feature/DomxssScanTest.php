<?php

namespace Tests\Feature;

use Tests\TestCase;

class DomxssScanTest extends TestCase
{
    /** @test */
    public function if_there_is_an_http_error_the_correct_formatted_error_message_will_be_send()
    {
        $response = $this->json('POST', '/api/v1/domxss', [
            'url' => 'https://url-but-not-available.info',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name'         => 'DOMXSS',
            'hasError'     => true,
            'errorMessage' => [
                'translationStringId' => 'NO_HTTP_RESPONSE',
                'placeholders' => [],
            ],
            'score' => 0,
            'tests' => [],
        ]);
    }
}
