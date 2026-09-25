<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Requerida | Unlock Brands</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(18, 24, 27, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .acid-gradient {
            background: radial-gradient(circle at top right, rgba(132, 204, 22, 0.15), transparent 60%),
                        radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.1), transparent 60%);
        }
    </style>
</head>
<body class="bg-[#0b0f12] text-zinc-100 min-h-screen flex items-center justify-center p-4 acid-gradient">

    <div class="max-w-md w-full glass rounded-2xl p-8 relative overflow-hidden shadow-2xl border-t border-t-lime-500/30">
        <!-- Decorator Light -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-lime-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-lime-500/10 border border-lime-500/20 text-lime-400 mb-4 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            @if(isset($hasExpiredTrial) && $hasExpiredTrial)
                <h1 class="text-2xl font-bold tracking-tight text-white mb-2">Tu periodo de prueba ha finalizado</h1>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Para seguir disfrutando de la telemetría avanzada y analíticas en tiempo real de 
                    <span class="text-lime-400 font-semibold">{{ $user->site_name ?: 'tu marca' }}</span>, por favor suscríbete.
                </p>
            @else
                <h1 class="text-2xl font-bold tracking-tight text-white mb-2">Activa tus 2 Meses de Prueba Gratis</h1>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Disfruta de <span class="text-lime-400 font-semibold">2 meses gratis (60 días)</span> sin necesidad de tarjeta para explorar la telemetría avanzada de 
                    <span class="text-lime-400 font-semibold">{{ $user->site_name ?: 'tu marca' }}</span>.
                </p>
            @endif
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="p-5 rounded-xl bg-black/40 border border-zinc-800 mb-8">
            <div class="flex items-baseline justify-between mb-4">
                @if(isset($hasExpiredTrial) && $hasExpiredTrial)
                    <span class="text-xs font-semibold uppercase tracking-wider text-lime-400 bg-lime-500/10 px-2.5 py-1 rounded-full border border-lime-500/20">Suscripción Mensual</span>
                @else
                    <span class="text-xs font-semibold uppercase tracking-wider text-lime-400 bg-lime-500/10 px-2.5 py-1 rounded-full border border-lime-500/20">2 Meses Gratis (60 días)</span>
                @endif
                <div class="text-right">
                    <span class="text-2xl font-bold text-white">${{ $amount }}</span>
                    <span class="text-xs font-semibold text-lime-400 ml-1 uppercase">{{ $currency }} / mes</span>
                    @if(!isset($hasExpiredTrial) || !$hasExpiredTrial)
                        <p class="text-[11px] text-zinc-400 mt-0.5">Cobro posterior al periodo de prueba</p>
                    @endif
                </div>
            </div>
            <ul class="space-y-2.5 text-xs text-zinc-300">
                @if(isset($hasExpiredTrial) && $hasExpiredTrial)
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-lime-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Acceso ininterrumpido a tus métricas</span>
                    </li>
                @else
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-lime-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span><strong>0 cargos hoy:</strong> Sin necesidad de registrar tarjeta</span>
                    </li>
                @endif
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-lime-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Monitoreo de tráfico en tiempo real e historial</span>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-lime-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Algoritmos de Score de Salud y Próxima Decisión</span>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-lime-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span>Sin compromiso: Cancelable en cualquier momento</span>
                </li>
            </ul>
        </div>

        @if(isset($hasExpiredTrial) && $hasExpiredTrial)
            <form action="{{ route('subscribe.checkout') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-lime-500 to-emerald-500 hover:from-lime-400 hover:to-emerald-400 text-black font-bold text-sm shadow-lg shadow-lime-500/25 hover:shadow-lime-500/40 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span>Pagar Suscripción</span>
                </button>
            </form>
        @else
            <form action="{{ route('subscribe.free_trial') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-lime-500 to-emerald-500 hover:from-lime-400 hover:to-emerald-400 text-black font-bold text-sm shadow-lg shadow-lime-500/25 hover:shadow-lime-500/40 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span>Comenzar 2 Meses de Prueba Gratis</span>
                </button>
            </form>
        @endif

        <div class="flex items-center justify-between text-xs text-zinc-500 pt-4 border-t border-zinc-800/80">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-zinc-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                Sin cobro inicial • Pago encriptado
            </span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-zinc-400 hover:text-white underline transition">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
