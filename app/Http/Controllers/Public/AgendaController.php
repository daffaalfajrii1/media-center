<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'upcoming');

        $query = Agenda::query()->published();

        if ($filter === 'past') {
            $query->past()->orderByDesc('event_date');
        } else {
            $query->upcoming()->orderBy('event_date');
            $filter = 'upcoming';
        }

        $agendas = $query->paginate(9)->withQueryString();

        return view('public.agenda.index', compact('agendas', 'filter'));
    }

    public function show(string $slug): View
    {
        $agenda = Agenda::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Agenda::query()
            ->published()
            ->where('id', '!=', $agenda->id)
            ->upcoming()
            ->orderBy('event_date')
            ->limit(3)
            ->get();

        return view('public.agenda.show', compact('agenda', 'related'));
    }
}
