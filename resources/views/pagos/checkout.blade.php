@extends('layouts.app')

@section('title', 'Pagar Reserva')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="mb-4 text-center">
                    <i class="bi bi-credit-card text-primary"></i> Pagar Reserva
                </h3>

                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Resumen de tu reserva</h6>
                    <p class="mb-1"><strong>Hospedaje:</strong> {{ $reserva->hospedaje->nombre }}</p>
                    <p class="mb-1"><strong>Llegada:</strong> {{ $reserva->fecha_inicio->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Salida:</strong> {{ $reserva->fecha_fin->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Personas:</strong> {{ $reserva->num_personas }}</p>
                    <p class="mb-0"><strong>Total a pagar:</strong> <span class="fs-5 text-success fw-bold">${{ number_format($reserva->total, 2) }} USD</span></p>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-shield-lock"></i> El pago quedará <strong>retenido</strong> hasta que llegues al hospedaje. El propietario lo recibirá cuando confirme tu llegada.
                </div>

                <form id="payment-form">
                    <div class="mb-3">
                        <label class="form-label">Número de tarjeta</label>
                        <div id="card-element" class="form-control" style="padding: 10px; height: auto;"></div>
                        <div id="card-errors" class="text-danger small mt-1"></div>
                    </div>

                    <div class="alert alert-secondary small">
                        <i class="bi bi-credit-card-2-front"></i> <strong>Tarjeta de prueba:</strong> 4242 4242 4242 4242 | Fecha: cualquiera futura | CVC: cualquier 3 dígitos
                    </div>

                    <button id="submit-btn" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-lock-fill"></i> Pagar ${{ number_format($reserva->total, 2) }} USD
                    </button>
                </form>

                <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env("STRIPE_KEY") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
            }
        }
    });
    cardElement.mount('#card-element');

    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        displayError.textContent = event.error ? event.error.message : '';
    });

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-lock-fill"></i> Pagar ${{ number_format($reserva->total, 2) }} USD';
        } else {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("pagos.procesar", $reserva->id) }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const pmInput = document.createElement('input');
            pmInput.type = 'hidden';
            pmInput.name = 'payment_method_id';
            pmInput.value = paymentMethod.id;

            form.appendChild(csrf);
            form.appendChild(pmInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@endsection