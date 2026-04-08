<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\ReprogramarCitaRequest;
use App\Http\Resources\CitaResource;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Cita::query();

        if ($user->rol === 'medico') {
            $query->where('medico_id', $user->id);
        }
        elseif ($user->rol === 'paciente') {
            $query->where('paciente_id', $user->id);
        }

        return CitaResource::collection($query->paginate(15));
    }

    public function store(StoreCitaRequest $request)
    {
        $ocupado = Cita::where('medico_id', $request->medico_id)
            ->where('fecha_hora', $request->fecha_hora)
            ->whereIn('estado', ['Programada', 'Reprogramada'])
            ->exists();

        if ($ocupado) {
            return response()->json([
                'message' => 'El espacio seleccionado ya no está disponible.'
            ], 422);
        }

        $cita = Cita::create([
            'uuid' => Str::uuid(),
            'paciente_id' => $request->user()->id,
            'medico_id' => $request->medico_id,
            'fecha_hora' => $request->fecha_hora,
            'motivo' => $request->motivo,
            'estado' => 'Programada',
        ]);

        return (new CitaResource($cita))->response()->setStatusCode(201);
    }

    public function historial(User $paciente)
    {
        $this->authorize('view-patient-history', $paciente);

        $citas = Cita::query()
            ->where('paciente_id', $paciente->id)
            ->where('estado', 'Atendida')
            ->where('fecha_hora', '<=', now())
            ->orderByDesc('fecha_hora')
            ->get();

        return CitaResource::collection($citas);
    }

    public function show(Cita $cita)
    {
        $this->authorize('view', $cita);
        return new CitaResource($cita);
    }

    public function cancelar(Cita $cita)
    {
        $this->authorize('cancelar', $cita);

        if ($cita->estado === 'Cancelada' || $cita->estado === 'Atendida') {
            return response()->json(['message' => 'La cita no puede ser cancelada en su estado actual.'], 422);
        }

        $cita->update(['estado' => 'Cancelada']);

        return response()->json(['message' => 'Cita cancelada exitosamente.', 'data' => new CitaResource($cita)]);
    }

    public function reprogramar(ReprogramarCitaRequest $request, Cita $cita)
    {
        $this->authorize('reprogramar', $cita);

        if ($cita->estado === 'Cancelada' || $cita->estado === 'Atendida') {
            return response()->json(['message' => 'No se puede reprogramar una cita inactiva.'], 422);
        }

        $ocupado = Cita::where('medico_id', $cita->medico_id)
            ->where('fecha_hora', $request->nueva_fecha_hora)
            ->where('id', '!=', $cita->id)
            ->whereIn('estado', ['Programada', 'Reprogramada'])
            ->exists();

        if ($ocupado) {
            return response()->json(['message' => 'El médico no tiene disponibilidad en esta nueva fecha/hora.'], 422);
        }

        $cita->update([
            'fecha_hora' => $request->nueva_fecha_hora,
            'estado' => 'Reprogramada'
        ]);

        return response()->json(['message' => 'Cita reprogramada exitosamente.', 'data' => new CitaResource($cita)]);
    }

    public function completar(Cita $cita)
    {
        $this->authorize('completar', $cita);

        if ($cita->estado !== 'Programada' && $cita->estado !== 'Reprogramada') {
            return response()->json(['message' => 'Solo las citas activas pueden marcarse como atendidas.'], 422);
        }

        $cita->update(['estado' => 'Atendida']);

        return response()->json(['message' => 'Cita marcada como atendida.', 'data' => new CitaResource($cita)]);
    }
}
