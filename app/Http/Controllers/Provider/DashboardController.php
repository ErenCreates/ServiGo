<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\UpdateProfileRequest;
use App\Models\Role;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
   public function index()
    {
        $user = auth()->user();

        // Ustanın profil bilgileri tam mı?
        $hasProfile = $user->serviceProvider && !empty($user->serviceProvider->category_id);

        $requests = collect();

        if ($hasProfile) {
            // Ustanın profili varsa kendisine gelen talepleri çekiyoruz
        $requests = \App\Models\ServiceRequest::with(['customer', 'category'])
                        ->where('provider_id', $user->serviceProvider->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        }
                    
        return view('provider.dashboard', [
            'requests' => $requests,
            'showProfileWarning' => !$hasProfile
        ]);
    }

    public function editProfile(): View
    {
        $user = auth()->user();
        $provider = $user->serviceProvider ?? new \App\Models\ServiceProvider();
        $categories = ServiceCategory::orderBy('name')->get();

        return view('provider.profile_edit', compact('user', 'provider', 'categories'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        abort_unless((int) $request->user()->role_id === Role::PROVIDER, 403);

        $validated = $request->validate([
            'category_id'   => ['required', 'exists:service_categories,id'],
            'bio'           => ['required', 'string', 'max:2000'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'company_name'  => ['nullable', 'string', 'max:255'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'category_id.required'  => 'Lütfen bir hizmet kategorisi seçin.',
            'category_id.exists'    => 'Seçilen kategori geçerli değil.',
            'bio.required'          => 'Biyografi alanı zorunludur.',
            'working_hours.string'  => 'Çalışma saatleri geçerli bir metin olmalıdır.',
            'company_name.string'   => 'Firma adı geçerli bir metin olmalıdır.',
            'latitude.numeric'      => 'Enlem değeri geçerli bir sayı olmalıdır.',
            'longitude.numeric'     => 'Boylam değeri geçerli bir sayı olmalıdır.',
        ]);

        $user = $request->user();

        $user->serviceProvider()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'category_id'   => $validated['category_id'],
                'bio'           => $validated['bio'],
                'working_hours' => $validated['working_hours'] ?? null,
                'company_name'  => $validated['company_name'] ?? ($user->serviceProvider->company_name ?? $user->name),
                'latitude'      => $validated['latitude'] ?? ($user->serviceProvider->latitude ?? 41.0082),
                'longitude'     => $validated['longitude'] ?? ($user->serviceProvider->longitude ?? 28.9784),
            ]
        );

        return redirect()
            ->route('provider.dashboard')
            ->with('success', 'İş profiliniz başarıyla güncellendi.');
    }

    public function update(Request $request): RedirectResponse
    {
        return $this->updateProfile($request);
    }

    public function acceptRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'appointment_date' => ['nullable', 'date'],
            'note'             => ['nullable', 'string', 'max:1000'],
        ]);

        $serviceRequest = \App\Models\ServiceRequest::where('provider_id', auth()->user()->serviceProvider->id)
                            ->findOrFail($id);

        $serviceRequest->update(['status' => 'accepted']);

        $appointmentDate = !empty($validated['appointment_date'])
            ? $validated['appointment_date']
            : now()->addDay()->setHour(10)->setMinute(0);

        $appointment = \App\Models\Appointment::updateOrCreate(
            ['service_request_id' => $serviceRequest->id],
            [
                'customer_id'      => $serviceRequest->customer_id,
                'provider_id'      => $serviceRequest->provider_id,
                'appointment_date' => $appointmentDate,
                'status'           => 'scheduled',
                'note'             => $validated['note'] ?? null,
            ]
        );

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'     => true,
                'message'     => 'Talep başarıyla kabul edildi ve randevu oluşturuldu!',
                'status'      => 'accepted',
                'request_id'  => $serviceRequest->id,
                'appointment' => [
                    'id'               => $appointment->id,
                    'appointment_date' => $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d.m.Y H:i') : null,
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Talep kabul edildi ve randevu oluşturuldu!');
    }

    public function rejectRequest(Request $request, $id)
    {
        $serviceRequest = \App\Models\ServiceRequest::where('provider_id', auth()->user()->serviceProvider->id)
                            ->findOrFail($id);

        $serviceRequest->update(['status' => 'rejected']);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'    => true,
                'message'    => 'Talep başarıyla reddedildi.',
                'status'     => 'rejected',
                'request_id' => $serviceRequest->id,
            ]);
        }

        return redirect()->back()->with('success', 'Talep reddedildi.');
    }

    public function updateRequestStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);
        $serviceRequest = \App\Models\ServiceRequest::where('provider_id', auth()->user()->serviceProvider->id)
                            ->findOrFail($id);
        $serviceRequest->update(['status' => $validated['status']]);
        
        $message = $validated['status'] == 'accepted' ? 'Talep başarıyla kabul edildi!' : 'Talep reddedildi.';

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'    => true,
                'message'    => $message,
                'status'     => $validated['status'],
                'request_id' => $serviceRequest->id,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}