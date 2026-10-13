<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;


use Illuminate\Http\JsonResponse;

use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    /**
     * Afficher les compétences du freelance connecté.
     */
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()
            ->freelancerProfile()
            ->firstOrCreate([]);

        $skills = $profile->skills()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $skills,
        ]);
    }

    /**
     * Ajouter une compétence.
     */
    
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
        'name' => ['required', 'string', 'max:255'],
        'level' => ['nullable', 'integer', 'min:1', 'max:5'],
        'years_experience' => ['nullable', 'integer', 'min:0'],
        'description' => ['nullable', 'string'],
    ]);

    $skill = $profile->skills()->create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Compétence ajoutée avec succès.',
        'data' => $skill,
    ], 201);
}

    /**
     * Modifier une compétence.
     */
    public function update(
        Request $request,
        Skill $skill
    ): JsonResponse {
        $profile = $request->user()->freelancerProfile;

        if (!$profile || $skill->freelancer_profile_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Compétence introuvable.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('skills', 'name')
                    ->where('freelancer_profile_id', $profile->id)
                    ->ignore($skill->id),
            ],
            'level' => ['nullable', 'integer', 'between:1,5'],
            'years_experience' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $skill->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Compétence modifiée avec succès.',
            'data' => $skill->fresh(),
        ]);
    }

    /**
     * Supprimer une compétence.
     */
    public function destroy(
        Request $request,
        Skill $skill
    ): JsonResponse {
        $profile = $request->user()->freelancerProfile;

        if (!$profile || $skill->freelancer_profile_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Compétence introuvable.',
            ], 404);
        }

        $skill->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compétence supprimée avec succès.',
        ]);
    }
}