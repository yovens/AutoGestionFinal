<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $date = $this->date($request);
        $kind = $request->query('kind');

        $tickets = Ticket::with('client', 'vehicle', 'agent')
            ->whereDate('created_at', $date)
            ->when(array_key_exists((string) $kind, Ticket::TITLES), fn ($q) => $q->where('kind', $kind))
            ->latest()->get();

        return view('counter.tickets.index', compact('tickets', 'date', 'kind'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('client', 'vehicle', 'agent', 'loan');

        return view('counter.tickets.show', ['ticket' => $ticket, 'layout' => 'layouts.admin']);
    }

    private function date(Request $request): Carbon
    {
        try {
            return $request->filled('date') ? Carbon::parse($request->query('date'))->startOfDay() : Carbon::today();
        } catch (\Throwable) {
            return Carbon::today();
        }
    }
}
