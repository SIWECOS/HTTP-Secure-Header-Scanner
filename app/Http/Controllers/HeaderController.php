<?php

namespace App\Http\Controllers;

use function GuzzleHttp\json_decode;
use Illuminate\Http\Request;
use App\Http\Requests\ReportRequest;
use App\Jobs\AnalyzeSite;
use Illuminate\Support\Facades\Redis;

class HeaderController extends Controller
{

    /**
     * Return frontend.
     */
    public function index() {
        return view('enter');
    }

    /**
     * Requesting a Report.
     *
     * @param ReportRequest $request
     * @return array
     */
    public function requestReport(Request $request)
    {
        $url = $request->input('url');
        if (substr($url, -1) !== '/')
            $url = $url . '/';

        // whitelist
        $whiteList = collect(strtolower(parse_url($url, PHP_URL_HOST)));
        $whiteList->push(explode("\n", $request->input('whitelist')))->flatten();

        // Set options for crawler
        $options = collect([]);
        if ($request->has('proxy'))
            $options->put('proxy', $request->input('proxyAddress'));
        if ($request->has('ignoreTLS'))
            $options->push('ignoreTLS');
        if($request->has('scan'))
            foreach ($request->input('scan') as $type)
                $options->push($type);
        if ($request->has('doNotCrawl'))
            $options->push('doNotCrawl');

        $options->put('limit', $request->input('limit'));

        $id = str_random();

        $this->dispatch(new AnalyzeSite($id, $url, $whiteList, $options));

        return redirect()->to('/' . $id);
        return ["id" => $id];
    }

    // TODO: This... thing.

    /**
     * @param $id
     * @var \App\Report $report
     */
    public function displayReport($id) {

        $fullReport = unserialize(Redis::hget($id, "reports"));

        /**
         * @var \App\Report $report
         */
        foreach ($fullReport as $report)
            echo $report->getUrl() . "<br>";
            echo $report->getUrl() . "<br>";
            echo $report->getUrl() . "<br>";
            echo $report->getUrl() . "<br>";
            echo $report->getUrl() . "<br>";


    }

    /**
     * jsConfig for the frontend.
     *
     * @return array with settings.
     */
    public function jsConfig() {
        return [
            'LIMIT' => env("LIMIT", 1000),
            'HOST_IP' => exec("/sbin/ip route|awk '/default/ { print $3 }'"),
            'CUSTOM_JSON' => [
                "a"  => "href",
                "img" => "src",
                "link" => "href",
                "script" => "src",
                "video" => "src",
                "audio" => "src",
                "source" => "src",
                "area" => "href",
                "iframe" => "src",
                "frame" => "src"
            ]
        ];
    }
}