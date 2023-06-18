<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeatureStoreRequest;
use App\Http\Requests\FeatureUpdateRequest;
use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function create()
    {
        return inertia('Settings/Features/Create');
    }

    public function store(FeatureStoreRequest $request): RedirectResponse
    {
        $feature = $request->user()->currentTeam->features()->create($request->validated());

        return redirect()->route('features.edit', $feature->id);
    }

    public function edit(Feature $feature)
    {
        $this->authorize('update', $feature);

        return inertia('Settings/Features/Edit', [
            'feature' => FeatureResource::make($feature),
        ]);
    }

    public function update(FeatureUpdateRequest $request, Feature $feature): RedirectResponse
    {
        $feature->update($request->safe()->all());

        return redirect()->back();
    }

    public function destroy(Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()->route('settings');
    }
}
