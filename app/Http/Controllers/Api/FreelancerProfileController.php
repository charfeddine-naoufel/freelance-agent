<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FreelancerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FreelancerProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $profile = $user->freelancerProfile;

        if (!$profile) {
            $profile = FreelancerProfile::create([
                'user_id' => $user->id,
            ]);
        }

        $profile->load([
            'skills',
            'experiences',
            'projects',
        ]);

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
        ]);

        $profile = $user->freelancerProfile;

        if (!$profile) {
            $profile = FreelancerProfile::create([
                'user_id' => $user->id,
            ]);
        }

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'data' => $profile->fresh(),
        ]);
    }
}