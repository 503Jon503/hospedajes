@extends('layouts.app')

@section('title', 'Chat - ' . $reserva->hospedaje->nombre)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-3">
                    <i class="bi bi-chat-dots-fill fs-5"></i>
                    <div>
                        <strong>{{ $reserva->hospedaje->nombre }}</strong><br>
                        <small>
                            Chat entre
                            {{ $reserva->cliente->nombre }} {{ $reserva->cliente->apellido }}
                            y
                            {{ $reserva->hospedaje->propietario->nombre }} {{ $reserva->hospedaje->propietario->apellido }}
                        </small>
                    </div>
                    <a href="{{ route('reservas.index') }}" class="btn btn-light btn-sm ms-auto">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body p-0">
                    <div id="chat-box" style="height:450px; overflow-y:auto; padding:1rem; background:var(--bs-light);">
                        @if($mensajes->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-chat display-4"></i>
                                <p class="mt-2">No hay mensajes aún. ¡Sé el primero en escribir!</p>
                            </div>
                        @else
                            @foreach($mensajes as $msg)
                            @php $esMio = $msg->remitente_id === session('user_data.id'); @endphp
                            <div class="d-flex {{ $esMio ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                <div style="max-width:70%">
                                    @if(!$esMio)
                                    <small class="text-muted d-block mb-1 ms-1">
                                        {{ $msg->remitente->nombre }} {{ $msg->remitente->apellido }}
                                    </small>
                                    @endif
                                    <div class="px-3 py-2 rounded-3 {{ $esMio ? 'bg-primary text-white' : 'bg-white border' }}">
                                        {{ $msg->mensaje }}
                                    </div>
                                    <small class="text-muted d-block mt-1 {{ $esMio ? 'text-end' : '' }}">
                                        {{ $msg->created_at->format('d/m/Y H:i') }}
                                        @if($esMio)
                                            @if($msg->leido)
                                                <i class="bi bi-check2-all text-info"></i>
                                            @else
                                                <i class="bi bi-check2"></i>
                                            @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="card-footer">
                    <form action="{{ route('chat.enviar', $reserva->id) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <input type="text" name="mensaje" class="form-control"
                                placeholder="Escribe un mensaje..." maxlength="1000"
                                autocomplete="off" required>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                        @error('mensaje')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto scroll al último mensaje
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;

    // Auto refresh cada 15 segundos
    setTimeout(() => window.location.reload(), 15000);
</script>
@endsection