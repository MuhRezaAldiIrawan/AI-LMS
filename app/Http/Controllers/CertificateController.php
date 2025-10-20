<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Download certificate PDF
     */
    public function download($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);

        // Authorization: hanya user yang memiliki certificate atau admin yang bisa download
        if (Auth::id() !== $certificate->user_id && !canAccess('admin')) {
            abort(403, 'Unauthorized access to certificate');
        }

        $response = $this->certificateService->downloadCertificate($certificate);

        if (!$response) {
            return redirect()->back()->with('error', 'Certificate file not found. Please contact administrator.');
        }

        return $response;
    }

    /**
     * Preview certificate inline (di browser)
     */
    public function preview($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);

        // Authorization: hanya user yang memiliki certificate atau admin yang bisa preview
        if (Auth::id() !== $certificate->user_id && !canAccess('admin')) {
            abort(403, 'Unauthorized access to certificate');
        }

        $response = $this->certificateService->previewCertificate($certificate);

        if (!$response) {
            return redirect()->back()->with('error', 'Certificate file not found. Please contact administrator.');
        }

        return $response;
    }

    /**
     * Verify certificate by verification code
     */
    public function verify(Request $request)
    {
        $verificationCode = $request->input('code');

        if (!$verificationCode) {
            return view('certificates.verify', ['certificate' => null, 'error' => 'Please enter verification code']);
        }

        $certificate = $this->certificateService->verifyCertificate($verificationCode);

        if (!$certificate) {
            return view('certificates.verify', ['certificate' => null, 'error' => 'Invalid verification code']);
        }

        $certificate->load(['user', 'course']);

        return view('certificates.verify', ['certificate' => $certificate, 'error' => null]);
    }

    /**
     * Regenerate certificate (admin only)
     */
    public function regenerate($certificateId)
    {
        if (!canAccess('admin')) {
            abort(403, 'Unauthorized action');
        }

        $certificate = Certificate::findOrFail($certificateId);

        $success = $this->certificateService->regeneratePDF($certificate);

        if ($success) {
            return redirect()->back()->with('success', 'Certificate PDF regenerated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to regenerate certificate PDF.');
    }

    /**
     * Get certificate untuk course tertentu (untuk AJAX)
     */
    public function getCertificateForCourse($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        $certificate = $this->certificateService->getCertificate($user, $course);

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'issued_date' => $certificate->issued_date->format('d M Y'),
                'download_url' => $certificate->getDownloadUrl(),
                'preview_url' => $certificate->getPreviewUrl(),
            ]
        ]);
    }

    /**
     * Manual generate certificate (admin only)
     */
    public function generate(Request $request)
    {
        if (!canAccess('admin')) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);

        $certificate = $this->certificateService->generateCertificate($user, $course);

        if ($certificate) {
            return redirect()->back()->with('success', 'Certificate generated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to generate certificate. Make sure the course is completed.');
    }

    /**
     * Landing page setelah menyelesaikan kursus (tampilan selamat + tombol download/preview)
     */
    public function congrats($courseId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $course = Course::with(['author', 'category'])->findOrFail($courseId);

        // Pastikan user menyelesaikan kursus
        if (!$course->isCompletedByUser($user)) {
            // Arahkan kembali ke ringkasan kursus (mode pembelajar)
            return redirect()->to(route('course.show', $course->id) . '?mode=learn')
                ->with('info', 'Selesaikan seluruh materi untuk mendapatkan sertifikat.');
        }

        // Ambil / siapkan sertifikat
        $certificate = $user->getCertificateForCourse($course->id);
        if (!$certificate) {
            // Coba generate jika belum ada (bergantung pada service yang ada)
            $certificate = $this->certificateService->generateCertificate($user, $course);
        }

        return view('pages.certificate.congrats', compact('course', 'certificate'));
    }
}
