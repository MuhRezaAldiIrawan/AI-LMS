<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateService
{
    /**
     * Generate certificate untuk user yang menyelesaikan course
     *
     * @param User $user
     * @param Course $course
     * @return Certificate|null
     */
    public function generateCertificate(User $user, Course $course): ?Certificate
    {
        try {
            // Check jika certificate sudah pernah di-generate
            $existingCertificate = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingCertificate) {
                Log::info("Certificate already exists for user {$user->id} and course {$course->id}");
                return $existingCertificate;
            }

            // Verify bahwa user benar-benar sudah menyelesaikan course
            if (!$course->isCompletedByUser($user)) {
                Log::warning("User {$user->id} has not completed course {$course->id}");
                return null;
            }

            // Generate certificate number dan verification code
            $certificateNumber = Certificate::generateCertificateNumber();
            $verificationCode = Certificate::generateVerificationCode();

            // Create certificate record
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_number' => $certificateNumber,
                'issued_date' => now(),
                'issued_by' => 'LMS Bosowa Administrator',
                'verification_code' => $verificationCode,
            ]);

            // Generate PDF
            $pdfPath = $this->generatePDF($certificate, $user, $course);

            // Update certificate dengan path PDF
            if ($pdfPath) {
                $certificate->update(['certificate_path' => $pdfPath]);
                Log::info("Certificate PDF generated successfully: {$pdfPath}");
            }

            return $certificate;

        } catch (\Exception $e) {
            Log::error("Failed to generate certificate: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate PDF dari certificate
     *
     * @param Certificate $certificate
     * @param User $user
     * @param Course $course
     * @return string|null Path to PDF file
     */
    protected function generatePDF(Certificate $certificate, User $user, Course $course): ?string
    {
        try {
            // Load view template
            $pdf = Pdf::loadView('certificates.template', [
                'certificate' => $certificate,
                'user' => $user,
                'course' => $course,
            ]);

            // Set paper size dan orientation
            $pdf->setPaper('a4', 'landscape');

            // Generate filename
            $filename = 'certificates/' . $certificate->certificate_number . '.pdf';

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('certificates');

            // Save PDF to storage
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($filename, $pdfContent);

            return $filename;

        } catch (\Exception $e) {
            Log::error("Failed to generate PDF: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get certificate untuk user di course tertentu
     *
     * @param User $user
     * @param Course $course
     * @return Certificate|null
     */
    public function getCertificate(User $user, Course $course): ?Certificate
    {
        return Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
    }

    /**
     * Regenerate PDF jika hilang atau corrupt
     *
     * @param Certificate $certificate
     * @return bool
     */
    public function regeneratePDF(Certificate $certificate): bool
    {
        try {
            $user = $certificate->user;
            $course = $certificate->course;

            // Delete old PDF if exists
            if ($certificate->certificate_path) {
                Storage::disk('public')->delete($certificate->certificate_path);
            }

            // Generate new PDF
            $pdfPath = $this->generatePDF($certificate, $user, $course);

            if ($pdfPath) {
                $certificate->update(['certificate_path' => $pdfPath]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Failed to regenerate PDF: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify certificate by verification code
     *
     * @param string $verificationCode
     * @return Certificate|null
     */
    public function verifyCertificate(string $verificationCode): ?Certificate
    {
        return Certificate::where('verification_code', $verificationCode)->first();
    }

    /**
     * Get download response untuk certificate
     *
     * @param Certificate $certificate
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null
     */
    public function downloadCertificate(Certificate $certificate)
    {
        if (!$certificate->fileExists()) {
            // Try to regenerate if file is missing
            $this->regeneratePDF($certificate);
        }

        if ($certificate->fileExists()) {
            $filePath = $certificate->getFilePath();
            $fileName = $certificate->certificate_number . '.pdf';

            return response()->download($filePath, $fileName);
        }

        return null;
    }

    /**
     * Get preview PDF inline (tanpa download)
     *
     * @param Certificate $certificate
     * @return \Illuminate\Http\Response|null
     */
    public function previewCertificate(Certificate $certificate)
    {
        if (!$certificate->fileExists()) {
            // Try to regenerate if file is missing
            $this->regeneratePDF($certificate);
        }

        if ($certificate->fileExists()) {
            $filePath = $certificate->getFilePath();

            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $certificate->certificate_number . '.pdf"'
            ]);
        }

        return null;
    }

    /**
     * Delete certificate dan file PDF-nya
     *
     * @param Certificate $certificate
     * @return bool
     */
    public function deleteCertificate(Certificate $certificate): bool
    {
        try {
            // Delete PDF file
            if ($certificate->certificate_path) {
                Storage::disk('public')->delete($certificate->certificate_path);
            }

            // Delete database record
            $certificate->delete();

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to delete certificate: " . $e->getMessage());
            return false;
        }
    }
}
