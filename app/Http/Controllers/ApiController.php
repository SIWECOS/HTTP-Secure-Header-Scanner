<?php

namespace App\Http\Controllers;

use App\DOMXSSCheck;
use App\HeaderCheck;
use App\Http\Requests\ScanStartRequest;
use App\Jobs\DomxssScanJob;
use App\Jobs\HeaderScanJob;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function headerReport(ScanStartRequest $request)
    {
        if ($request->json('callbackurls')) {
            HeaderScanJob::dispatch($request->all());

            return 'OK';
        }

        return response()->json((new HeaderCheck($request))->report());
    }

    public function domxssReport(ScanStartRequest $request)
    {
        if ($request->json('callbackurls')) {
            DomxssScanJob::dispatch($request->all());

            return 'OK';
        }

        return response()->json((new DOMXSSCheck($request))->report());
    }

    public static function notifyCallbacks(array $callbackurls, $report)
    {
        foreach ($callbackurls as $url) {
            try {
                $client = new Client();
                $client->request('POST', $url, [
                    'http_errors' => false,
                    'timeout'     => 60,
                    'json'        => $report,
                ]);
            } catch (\Exception $e) {
                Log::warning('Could not send the report to the following callback url: ' . $url);
            }
        }
    }
}
