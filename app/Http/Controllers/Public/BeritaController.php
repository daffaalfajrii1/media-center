<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\BeritaLike;
use App\Http\Controllers\Concerns\ResolvesBeritaVisitor;
use App\Services\BeritaAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaController extends Controller
{
    use ResolvesBeritaVisitor;

    public function __construct(private BeritaAnalyticsService $analytics)
    {
    }

    public function index(Request $request): View
    {
        $categories = BeritaCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $beritas = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->when($request->filled('kategori'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->kategori));
            })
            ->when($request->search, fn ($q, $search) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            }))
            ->orderByDesc('published_at')
            ->paginate(6)
            ->withQueryString();

        return view('public.berita.index', compact('beritas', 'categories'));
    }

    public function show(Request $request, string $slug)
    {
        $berita = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->where('slug', $slug)
            ->firstOrFail();

        $berita->increment('views_count');
        $berita->refresh();
        $this->analytics->recordView($berita);

        $visitorToken = $this->visitorToken($request);
        $this->rememberVisitorToken($request, $visitorToken);
        $hasLiked = BeritaLike::query()
            ->where('berita_id', $berita->id)
            ->where('visitor_token', $visitorToken)
            ->exists();

        $related = Berita::query()
            ->published()
            ->with(['category'])
            ->where('id', '!=', $berita->id)
            ->when($berita->berita_category_id, fn ($q) => $q->where('berita_category_id', $berita->berita_category_id))
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $sidebarLatest = Berita::query()
            ->published()
            ->with(['category'])
            ->where('id', '!=', $berita->id)
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $sidebarPopular = Berita::query()
            ->published()
            ->with(['category'])
            ->where('id', '!=', $berita->id)
            ->orderByDesc('views_count')
            ->limit(4)
            ->get();

        $prevBerita = null;
        $nextBerita = null;

        if ($berita->published_at) {
            $prevBerita = Berita::query()
                ->published()
                ->where('published_at', '<', $berita->published_at)
                ->orderByDesc('published_at')
                ->first();

            $nextBerita = Berita::query()
                ->published()
                ->where('published_at', '>', $berita->published_at)
                ->orderBy('published_at')
                ->first();
        }

        return response()
            ->view('public.berita.show', compact(
                'berita',
                'related',
                'sidebarLatest',
                'sidebarPopular',
                'prevBerita',
                'nextBerita',
                'hasLiked',
            ))
            ->cookie($this->visitorCookie($visitorToken));
    }

    public function like(Request $request, string $slug): JsonResponse
    {
        $berita = Berita::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $visitorToken = $this->visitorToken($request);
        $this->rememberVisitorToken($request, $visitorToken);

        $alreadyLiked = BeritaLike::query()
            ->where('berita_id', $berita->id)
            ->where('visitor_token', $visitorToken)
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'liked' => true,
                'likes_count' => $berita->likes_count,
                'message' => 'Anda sudah menyukai berita ini.',
            ])->cookie($this->visitorCookie($visitorToken));
        }

        BeritaLike::create([
            'berita_id' => $berita->id,
            'visitor_token' => $visitorToken,
        ]);

        $berita->increment('likes_count');

        return response()->json([
            'liked' => true,
            'likes_count' => $berita->fresh()->likes_count,
            'message' => 'Terima kasih atas dukungan Anda!',
        ])->cookie($this->visitorCookie($visitorToken));
    }
}
