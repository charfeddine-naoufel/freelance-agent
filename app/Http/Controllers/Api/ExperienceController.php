<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->freelancerProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil freelance introuvable.',
            ], 404);
        }

        $experiences = $profile->experiences()
            ->orderByDesc('is_current')
            ->orderByDesc('started_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $experiences,
        ]);
    }

    public function store(Request $request)
{
    $profile = $request->user()->freelancerProfile;

    if (!$profile) {
        return response()->json([
            'success' => false,
            'message' => 'Profil freelance introuvable.',
        ], 404);
    }

    $validated = $request->validate([
        'company' => [
            'nullable',
            'string',
            'max:255',
        ],

        'position' => [
            'required',
            'string',
            'max:255',
        ],

        'description' => [
            'nullable',
            'string',
        ],

        'started_at' => [
            'nullable',
            'date',
        ],

        'ended_at' => [
            'nullable',
            'date',
            'after_or_equal:started_at',
        ],

        'is_current' => [
            'boolean',
        ],
    ]);

    /*
     * Une expérience actuelle ne doit pas avoir
     * de date de fin.
     */
    if ($request->boolean('is_current')) {
        $validated['ended_at'] = null;
    }

    $validated['is_current'] = $request->boolean('is_current');

    $experience = $profile->experiences()->create(
        $validated
    );

    return response()->json([
        'success' => true,
        'message' => 'Expérience ajoutée avec succès.',
        'data' => $experience,
    ], 201);
}
public function update(Request $request, Experience $experience)
{
    $profile = $request->user()->freelancerProfile;

    if (
        !$profile ||
        $experience->freelancer_profile_id !== $profile->id
    ) {
        return response()->json([
            'success' => false,
            'message' => 'Expérience introuvable.',
        ], 404);
    }

    $validated = $request->validate([
        'company' => [
            'nullable',
            'string',
            'max:255',
        ],

        'position' => [
            'required',
            'string',
            'max:255',
        ],

        'description' => [
            'nullable',
            'string',
        ],

        'started_at' => [
            'nullable',
            'date',
        ],

        'ended_at' => [
            'nullable',
            'date',
            'after_or_equal:started_at',
        ],

        'is_current' => [
            'boolean',
        ],
    ]);

    if ($request->boolean('is_current')) {
        $validated['ended_at'] = null;
    }

    $validated['is_current'] = $request->boolean('is_current');

    $experience->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Expérience modifiée avec succès.',
        'data' => $experience->fresh(),
    ]);
}

public function destroy(
    Request $request,
    Experience $experience
) {
    $profile = $request->user()->freelancerProfile;

    if (
        !$profile ||
        $experience->freelancer_profile_id !== $profile->id
    ) {
        return response()->json([
            'success' => false,
            'message' => 'Expérience introuvable.',
        ], 404);
    }

    $experience->delete();

    return response()->json([
        'success' => true,
        'message' => 'Expérience supprimée avec succès.',
    ]);
}


}