<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Tamamlanmış bir randevu için müşteri puan ve yorumunu kaydeder.
     */
    public function store(Request $request, $appointmentId): RedirectResponse
    {
        $user = auth()->user();
        abort_unless((int) $user->role_id === Role::CUSTOMER, 403, 'Sadece müşteriler değerlendirme yapabilir.');

        $appointment = Appointment::with(['provider', 'serviceRequest'])->findOrFail($appointmentId);

        // 1. Randevu bu müşteriye mi ait?
        abort_unless((int) $appointment->customer_id === $user->id, 403, 'Bu randevu size ait değil.');

        // 2. Randevu tamamlandı (completed) durumunda mı?
        abort_unless($appointment->status === 'completed', 403, 'Yalnızca tamamlanan randevular için değerlendirme yapabilirsiniz.');

        // 3. Daha önce bu randevuya yorum yapılmış mı?
        $alreadyReviewed = Review::where('appointment_id', $appointment->id)
            ->orWhere(function ($q) use ($appointment) {
                if ($appointment->service_request_id) {
                    $q->where('service_request_id', $appointment->service_request_id)
                      ->where('customer_id', auth()->id());
                }
            })
            ->exists();

        if ($alreadyReviewed) {
            return redirect()
                ->back()
                ->with('error', 'Bu randevu için daha önce değerlendirme yaptınız.');
        }

        // 4. Doğrulama
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rating.required'  => 'Lütfen 1 ile 5 arasında bir yıldız puanı seçin.',
            'rating.integer'   => 'Puan tam sayı olmalıdır.',
            'rating.min'       => 'Puan en az 1 olmalıdır.',
            'rating.max'       => 'Puan en fazla 5 olabilir.',
            'comment.required' => 'Lütfen deneyiminiz hakkında kısa bir yorum yazın.',
            'comment.min'      => 'Yorumunuz en az 5 karakter olmalıdır.',
            'comment.max'      => 'Yorumunuz en fazla 1000 karakter olabilir.',
        ]);

        // 5. Kayıt
        Review::create([
            'customer_id'        => $user->id,
            'provider_id'        => $appointment->provider_id,
            'service_request_id' => $appointment->service_request_id,
            'appointment_id'     => $appointment->id,
            'rating'             => (int) $validated['rating'],
            'comment'            => $validated['comment'],
            'is_approved'        => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Değerlendirmeniz ve yorumunuz başarıyla kaydedildi. Teşekkür ederiz!');
    }
}

