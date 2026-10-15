<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /** Kliyan an ka wè/enprime pwòp tikè pa l yo. */
    public function show(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 404);

        $ticket->load('client', 'vehicle', 'agent', 'loan');

        return view('counter.tickets.show', ['ticket' => $ticket, 'layout' => 'layouts.client']);
    }
}
