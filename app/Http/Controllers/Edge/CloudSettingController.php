<?php

namespace App\Http\Controllers\Edge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Edge\CloudSetting\UpdateCloudSettingRequest;
use App\Models\CloudSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CloudSettingController extends Controller
{
    public function index() : View {
        $cloudSetting = CloudSetting::first();

        return view('pages.setting.cloud.index', compact('cloudSetting'));
    }

    public function update(UpdateCloudSettingRequest $request) {
        $cloudSetting = CloudSetting::first();

        $headers = collect($request->safe()->headers)
            ->filter(function($header){
                return (isset($header['key']) && $header['key']) ? true : false;
            })
            ->map(function($header){
                return [
                    $header['key'] => $header['value']
                ];
            })->flatMap(function (array $values) {
                return $values;
            })
            ->all();

        $cloudSetting->update([
            'url' => $request->safe()->url,
            'headers' => (object) $headers,
        ]);

        return redirect()
            ->back()
            ->with('cloud-setting-success', 'Berhasil disimpan!');
    }
}
