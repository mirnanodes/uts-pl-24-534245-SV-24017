<?php

namespace App\Http\Controllers\Api;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use App\Http\Controllers\Controller;
use App\Http\Requests\GearRequest;
use App\Http\Resources\GearResource;
use App\Models\Gear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class GearController extends Controller
{
    private const MAX_PER_PAGE = 50;

    /**
     * GET /api/gears — paginated list with optional search & category filter.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 10), 1), self::MAX_PER_PAGE);

        $gears = Gear::query()
            ->search($request->string('search')->trim()->toString())
            ->ofCategory($request->string('category')->toString())
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return GearResource::collection($gears);
    }

    /**
     * POST /api/gears
     */
    public function store(GearRequest $request): JsonResponse
    {
        $gear = Gear::create($request->validated());

        return GearResource::make($gear)
            ->additional(['message' => 'Alat berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * GET /api/gears/{gear}
     */
    public function show(Gear $gear): GearResource
    {
        return GearResource::make($gear);
    }

    /**
     * PUT/PATCH /api/gears/{gear}
     */
    public function update(GearRequest $request, Gear $gear): GearResource
    {
        $gear->update($request->validated());

        return GearResource::make($gear)
            ->additional(['message' => 'Alat berhasil diperbarui.']);
    }

    /**
     * DELETE /api/gears/{gear}
     */
    public function destroy(Gear $gear): JsonResponse
    {
        $gear->delete();

        return response()->json([
            'message' => 'Alat berhasil dihapus.',
        ]);
    }

    /**
     * GET /api/gear-options — choices for the frontend select inputs.
     */
    public function options(): JsonResponse
    {
        return response()->json([
            'data' => [
                'categories' => GearCategory::options(),
                'conditions' => GearCondition::options(),
            ],
        ]);
    }
}
