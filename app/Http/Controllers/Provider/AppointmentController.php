<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Ustanın randevularını listeler.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::PROVIDER && $user->serviceProvider, 403);

        $providerId = $user->serviceProvider->id;

        $query = Appointment::with(['customer', 'serviceRequest.category'])
            ->where('provider_id', $providerId);

        // Durum Filtresi
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->paginate(10)->withQueryString();

        $stats = [
            'total'     => Appointment::where('provider_id', $providerId)->count(),
            'scheduled' => Appointment::where('provider_id', $providerId)->where('status', 'scheduled')->count(),
            'completed' => Appointment::where('provider_id', $providerId)->where('status', 'completed')->count(),
            'cancelled' => Appointment::where('provider_id', $providerId)->where('status', 'cancelled')->count(),
        ];

        return view('provider.appointments.index', compact('appointments', 'stats'));
    }

    /**
     * Randevuyu tamamlandı olarak işaretler.
     */
    public function complete(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::PROVIDER && $user->serviceProvider, 403);

        $appointment = Appointment::where('provider_id', $user->serviceProvider->id)->findOrFail($id);
        $appointment->update(['status' => 'completed']);

        if ($appointment->serviceRequest) {
            $appointment->serviceRequest->update(['status' => 'completed']);
        }

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'        => true,
                'message'        => 'Randevu başarıyla tamamlandı olarak işaretlendi.',
                'status'         => 'completed',
                'appointment_id' => $appointment->id,
            ]);
        }

        return redirect()->back()->with('success', 'Randevu başarıyla tamamlandı olarak işaretlendi.');
    }

    /**
     * Randevuyu iptal eder.
     */
    public function cancel(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::PROVIDER && $user->serviceProvider, 403);

        $appointment = Appointment::where('provider_id', $user->serviceProvider->id)->findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'        => true,
                'message'        => 'Randevu iptal edildi.',
                'status'         => 'cancelled',
                'appointment_id' => $appointment->id,
            ]);
        }

        return redirect()->back()->with('success', 'Randevu iptal edildi.');
    }

    /**
     * Randevu tarih ve saatini günceller (Erteleme / Yeniden Planlama).
     */
    public function reschedule(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::PROVIDER && $user->serviceProvider, 403);

        $validated = $request->validate([
            'appointment_date' => ['required', 'date'],
            'note'             => ['nullable', 'string', 'max:1000'],
        ], [
            'appointment_date.required' => 'Lütfen yeni bir randevu tarihi ve saati seçin.',
            'appointment_date.date'     => 'Geçersiz tarih formatı.',
        ]);

        $appointment = Appointment::where('provider_id', $user->serviceProvider->id)->findOrFail($id);
        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'note'             => $validated['note'] ?? $appointment->note,
            'status'           => 'scheduled',
        ]);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'          => true,
                'message'          => 'Randevu tarihi başarıyla güncellendi.',
                'status'           => 'scheduled',
                'appointment_id'   => $appointment->id,
                'appointment_date' => \Carbon\Carbon::parse($appointment->appointment_date)->format('d.m.Y H:i'),
                'note'             => $appointment->note,
            ]);
        }

        return redirect()->back()->with('success', 'Randevu tarihi başarıyla güncellendi.');
    }
}

