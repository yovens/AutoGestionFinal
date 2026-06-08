@extends('admin.layout')

@section('title', 'Messages des clients')
@section('page_title', 'Messages reçus')

@section('styles')
<style>
    /* Estil Modal */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; justify-content: center; align-items: center; }
    .modal-content { background: #fff; padding: 30px; border-radius: 16px; width: 90%; max-width: 500px; color: #1e293b; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .btn-close { margin-top: 20px; padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; transition: 0.2s; }
    .btn-close:hover { background: #2563eb; }
    .btn-close {
    margin-top: 30px;
    width: 100%;
    padding: 14px;
    background: #3b82f6; /* Koulè ble modèn */
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease; /* Fè chanjman koulè a dous */
    display: flex;
    justify-content: center;
    align-items: center;
}

.btn-close:hover {
    background: #2563eb; /* Koulè ble pi fonse lè sourit la sou li */
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); /* Yon ti lonbraj pou ba l pwofondè */
    transform: translateY(-2px); /* Bouton an monte yon ti jan lè w sou li */
}
.modal { 
    display: none; 
    /* ... lòt estil ... */
    opacity: 0; 
    transition: opacity 0.3s ease; 
}
.btn-close:active {
    transform: translateY(0); /* Bouton an retounen plas li lè w klike */
}
    /* Layout Tablo */
    .table-responsive { overflow-x: auto; }


    /* Kouch ki kouvri ekran an (Backdrop) */
.modal { 
    display: none; 
    position: fixed; 
    top: 0; left: 0; 
    width: 100%; height: 100%; 
    background: rgba(15, 23, 42, 0.7); /* Koulè ble nwa transparan */
    backdrop-filter: blur(5px); /* Efè "glass" sou dèyè a */
    z-index: 9999; /* Asire li parèt sou tout lòt bagay */
    justify-content: center; 
    align-items: center; 
    padding: 20px;
}

/* Bwat mesaj la */
.modal-content { 
    background: #ffffff; 
    padding: 35px; 
    border-radius: 24px; 
    width: 100%; 
    max-width: 550px; 
    color: #1e293b; 
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); 
    animation: fadeIn 0.3s ease; /* Animasyon lè l parèt */
}

/* Animasyon pou modal la parèt dousman */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Tèks mesaj la */
.modal-content p {
    font-size: 1rem;
    line-height: 1.7;
    color: #475569;
    word-wrap: break-word; /* Pou mesaj ki long pa depase kad la */
}
</style>
@endsection

@section('content')
<div class="card" style="padding: 20px; background: var(--panel-bg); border-radius: 16px; border: 1px solid var(--panel-border);">
    <div class="table-responsive">
        @if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #fecaca;">
        {{ session('error') }}
    </div>
@endif
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--panel-border); color: var(--text-muted);">
                    <th style="padding: 15px; text-align: left;">Client</th>
                    <th style="padding: 15px; text-align: left;">Objet</th>
                    <th style="padding: 15px; text-align: left;">Message</th>
                    <th style="padding: 15px; text-align: left;">Date</th>
                    <th style="padding: 15px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                <tr style="border-bottom: 1px solid var(--panel-border);">
                    <td style="padding: 15px;">{{ $msg->user->nom ?? 'Inconnu' }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $msg->subject }}</td>
                    <td style="padding: 15px; color: var(--text-muted);">{{ Str::limit($msg->message, 50) }}</td>
                    <td style="padding: 15px;">{{ $msg->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 15px; text-align: center; display: flex; gap: 10px; justify-content: center;">
                        <button type="button" onclick="showModal('{{ $msg->id }}')" style="background: none; border: none; color: #3b82f6; cursor: pointer;">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Efase mesaj sa a?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <div id="modal-{{ $msg->id }}" class="modal">
                    <div class="modal-content">
                        <h4 style="margin-bottom: 10px;">{{ $msg->subject }}</h4>
                        <p style="color: #64748b; font-size: 0.9rem;">De: {{ $msg->user->nom ?? 'Inconnu' }}</p>
                        <hr style="margin: 15px 0;">
                        <p style="line-height: 1.6;">{{ $msg->message }}</p>

<form action="{{ route('admin.contacts.reply', $msg->id) }}" method="POST">
        @csrf
        <textarea name="message" rows="4" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;" placeholder="Écrivez votre réponse ici..." required></textarea>
        <button type="submit" style="margin-top: 15px; width: 100%; padding: 12px; background: #10b981; color: white; border: none; border-radius: 12px; cursor: pointer;">
            Envoyer la réponse
        </button>
    </form>


                        <button onclick="closeModal('{{ $msg->id }}')" 
        class="btn-close" 
        style="margin-top: 10px; 
               width: 70%; 
               padding: 10px; 
               background-color: #3b82f6; 
               color: white; 
               border: none; 
               border-radius: 10px; 
               font-weight: 400; 
               font-size: 1rem; 
               cursor: pointer; 
               transition: all 0.3s ease; 
               display: flex; 
               justify-content: center; 
               align-items: center;">
    Fermer
</button>
                    </div>
                </div>
                @empty
                <tr><td colspan="5" style="padding: 40px; text-align: center;">Aucun message pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showModal(id) {
        document.getElementById('modal-' + id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById('modal-' + id).style.display = 'none';
    }
    // Fèmen modal la si ou klike deyò zòn kontni an
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endpush

