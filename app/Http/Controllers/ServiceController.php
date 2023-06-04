<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Topic;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function show()
    {
        return view('services.service', ['serviceList' => Service::all()]);
    }

    public function createView()
    {
        return view('services.createService');
    }

    public function create(Request $request)
    {
        Service::create([
            'kzName' => $request->kzName,
            'ruName' => $request->ruName
        ]);
        return redirect()->route('service.show');
    }

    public function editView($id)
    {
        return view('services.editService', ['service' => Service::findOrFail($id)]);
    }

    public function edit($id, Request $request)
    {
        Service::query()->find($id)->update(
            [
                'ruName' => $request->ruName,
                'kzName' => $request->kzName
            ]
        );
        return redirect(route('service.show'));
    }

    public function deleteService($id)
    {
        Service::query()->find($id)->delete();
        return redirect()->route('service.show');
    }

    public function topicList($serviceId)
    {
        return view('services.topics.topic', ['serviceId' => $serviceId, 'topics' => Topic::query()->where('service_id', $serviceId)->get()]);
    }

    public function createTopicView($serviceId)
    {
        return view('services.topics.createTopic', ['serviceId' => $serviceId]);
    }

    public function createTopic($serviceId, Request $request)
    {
        Topic::query()->create([
            'service_id' => $serviceId,
            'kzName' => $request->kzName,
            'ruName' => $request->ruName
        ]);

        return redirect()->route('service.topics', [$serviceId]);
    }

    public function editTopicView($serviceId, $topicId)
    {
        return view('services.topics.editTopic', ['topic' => Topic::query()->find($topicId)]);
    }

    public function editTopic($serviceId, $topicId, Request $request)
    {
        Topic::query()->find($topicId)->update([
            'ruName' => $request->ruName,
            'kzName' => $request->kzName
        ]);

        return redirect()->route('service.topics', [$serviceId]);
    }

    public function deleteTopic($serviceId, $topicId)
    {
        Topic::query()->find($topicId)->delete();
        return redirect()->route('service.topics', [$serviceId]);
    }
}