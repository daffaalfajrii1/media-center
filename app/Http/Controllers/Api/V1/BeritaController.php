<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BeritaDetailResource;
use App\Http\Resources\Api\BeritaResource;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class BeritaController extends Controller
{
    /**
     * Daftar berita terbit (paginasi) untuk konsumsi WordPress / klien eksternal.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $beritas = Berita::query()
            ->published()
            ->with(['category', 'author'])
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(fn ($q) => $q->where('title', 'like', $search)->orWhere('excerpt', 'like', $search));
            })
            ->orderByDesc('published_at')
            ->paginate($perPage)
            ->withQueryString();

        return BeritaResource::collection($beritas);
    }

    /**
     * Berita terbaru — cocok untuk section "Berita Terbaru" di WordPress.
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->integer('limit', 4), 1), 20);

        $beritas = Berita::query()
            ->published()
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'source' => config('app.url'),
            'count' => $beritas->count(),
            'data' => BeritaResource::collection($beritas)->resolve(),
        ]);
    }

    /**
     * Detail satu berita berdasarkan slug.
     */
    public function show(string $slug): BeritaDetailResource|JsonResponse
    {
        $berita = Berita::query()
            ->published()
            ->with(['category', 'author'])
            ->where('slug', $slug)
            ->first();

        if (! $berita) {
            return response()->json([
                'success' => false,
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        return new BeritaDetailResource($berita);
    }
}
