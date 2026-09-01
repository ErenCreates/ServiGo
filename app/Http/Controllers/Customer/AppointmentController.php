<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Müşterinin randevularını listeler.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::CUSTOMER, 403);

        $query = Appointment::with(['provider.user', 'provider.category', 'serviceRequest', 'review'])
            ->where('customer_id', $user->id);

        // Durum Filtresi
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->paginate(10)->withQueryString();

        $stats = [
            'total'     => Appointment::where('customer_id', $user->id)->count(),
            'scheduled' => Appointment::where('customer_id', $user->id)->where('status', 'scheduled')->count(),
            'completed' => Appointment::where('customer_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Appointment::where('customer_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        return view('customer.appointments.index', compact('appointments', 'stats'));
    }

    /**
     * Müşterinin randevuyu iptal etmesini sağlar.
     */
    public function cancel(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::CUSTOMER, 403);

        $appointment = Appointment::where('customer_id', $user->id)->findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'        => true,
                'message'        => 'Randevunuz başarıyla iptal edildi.',
                'status'         => 'cancelled',
                'appointment_id' => $appointment->id,
            ]);
        }

        return redirect()->back()->with('success', 'Randevunuz başarıyla iptal edildi.');
    }
}

