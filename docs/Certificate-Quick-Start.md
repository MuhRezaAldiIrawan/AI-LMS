# 🚀 Quick Start - Certificate System

## Installation (Already Done ✅)

```bash
# 1. Install DomPDF
composer require barryvdh/laravel-dompdf

# 2. Run migration
php artisan migrate --path=database/migrations/2025_10_15_021950_create_certificates_table.php

# 3. Create storage link (if not exists)
php artisan storage:link
```

## Usage Guide

### For Karyawan (Employee)

#### Step 1: Complete a Course
1. Login sebagai karyawan
2. Enroll di course
3. Complete semua lessons:
   - Buka lesson
   - Tandai sebagai completed
4. Pass semua quizzes:
   - Kerjakan quiz
   - Score >= passing score

#### Step 2: Check Progress
- Progress bar harus menunjukkan **100%**
- Alert hijau "Terdaftar - Siap belajar!" dengan progress 100%

#### Step 3: Get Certificate
Ketika 100% complete:
- Alert biru **"🎉 Sertifikat Tersedia!"** muncul otomatis
- Menampilkan:
  - Certificate Number
  - Issue Date
  - Download button
  - Preview button

#### Step 4: Download Certificate
```
Klik "Download Sertifikat" → PDF file terdownload
```

File: `CERT-YYYYMM-XXXXX.pdf`

#### Step 5: Share Certificate (Optional)
- Download PDF
- Share di LinkedIn, social media, dll
- Gunakan verification code untuk validasi

### For Admin

#### Manual Generate Certificate
```bash
# Via web interface (coming soon) or API:
POST /certificate/generate
{
    "user_id": 5,
    "course_id": 1
}
```

#### Regenerate PDF (jika corrupt)
```bash
POST /certificate/{id}/regenerate
```

#### View All Certificates
```sql
SELECT 
    c.certificate_number,
    u.name as user_name,
    co.title as course_title,
    c.issued_date
FROM certificates c
JOIN users u ON c.user_id = u.id
JOIN courses co ON c.course_id = co.id
ORDER BY c.created_at DESC;
```

## Testing Checklist

### ✅ Pre-requisites
- [ ] Storage link created: `php artisan storage:link`
- [ ] Permissions set: `chmod -R 775 storage/app/public/certificates`
- [ ] DomPDF installed: Check `composer.json`

### ✅ Test Certificate Generation

#### Test 1: Complete Course & Auto-Generate
```
1. Login as karyawan
2. Enroll in course
3. Complete all lessons (check lesson_user table)
4. Pass all quizzes (score >= passing_score)
5. Refresh course page
6. Expected: Certificate alert appears
```

**Verify Database:**
```sql
SELECT * FROM certificates 
WHERE user_id = [USER_ID] AND course_id = [COURSE_ID];
```

**Verify File:**
```bash
ls storage/app/public/certificates/CERT-*.pdf
```

#### Test 2: Download Certificate
```
1. Click "Download Sertifikat" button
2. Expected: PDF file downloads
3. Open PDF → Should show certificate with correct data
```

#### Test 3: Preview Certificate
```
1. Click "Preview" button
2. Expected: PDF opens in new tab (inline)
3. Should display in browser, not download
```

#### Test 4: Verify Certificate
```
1. Go to /certificate/verify
2. Input verification code from PDF
3. Click "Verifikasi Sekarang"
4. Expected: Shows certificate details
   - Certificate Number
   - Recipient Name
   - Course Title
   - Issue Date
```

### ✅ Test Security

#### Test 5: Authorization
```
1. Login as User A
2. Try to access User B's certificate
3. Expected: 403 Forbidden
```

#### Test 6: Admin Access
```
1. Login as admin
2. Can access any certificate
3. Can regenerate PDF
4. Expected: Full access granted
```

## Common Commands

### Check Certificate Exists
```php
php artisan tinker

$user = User::find(1);
$course = Course::find(1);
$certificate = $user->getCertificateForCourse($course->id);
dd($certificate);
```

### Manual Generate Certificate
```php
php artisan tinker

$user = User::find(1);
$course = Course::find(1);
$service = app(\App\Services\CertificateService::class);
$cert = $service->generateCertificate($user, $course);
dd($cert);
```

### Check Course Completion
```php
php artisan tinker

$user = User::find(1);
$course = Course::find(1);
$isComplete = $course->isCompletedByUser($user);
$percentage = $course->getCompletionPercentage($user);
echo "Complete: " . ($isComplete ? 'Yes' : 'No') . "\n";
echo "Progress: {$percentage}%\n";
```

### Check Logs
```bash
# Real-time logs
tail -f storage/logs/laravel.log | grep -i certificate

# Search specific error
grep "Certificate" storage/logs/laravel.log
```

## Troubleshooting

### Issue: Certificate tidak muncul setelah 100%

**Diagnosis:**
```php
// Check completion status
$course->isCompletedByUser($user); // Should be true

// Check progress
$course->getCompletionPercentage($user); // Should be 100

// Check pivot table
$enrollment = $user->enrolledCourses()
    ->where('course_id', $course->id)
    ->first();
echo $enrollment->pivot->completed_at; // Should have timestamp
```

**Fix:**
```php
// Manual mark as completed
$course->markAsCompletedFor($user);

// This will auto-generate certificate
```

### Issue: PDF tidak bisa di-download

**Check:**
```bash
# 1. Storage link
ls -la public/storage

# 2. File exists
ls storage/app/public/certificates/

# 3. Permissions
ls -la storage/app/public/certificates/
```

**Fix:**
```bash
# Create storage link
php artisan storage:link

# Fix permissions
chmod -R 775 storage/app/public/certificates
chown -R www-data:www-data storage/app/public/certificates
```

### Issue: PDF corrupt atau blank

**Fix:**
```php
// Regenerate PDF (admin)
POST /certificate/{id}/regenerate

// Or via tinker
$cert = Certificate::find(1);
$service = app(\App\Services\CertificateService::class);
$service->regeneratePDF($cert);
```

## Quick Reference

### Routes
```
GET  /certificate/{id}/download           - Download PDF
GET  /certificate/{id}/preview            - Preview PDF
GET  /certificate/verify                  - Verify certificate
POST /certificate/generate (admin)        - Manual generate
POST /certificate/{id}/regenerate (admin) - Regenerate PDF
```

### Models
```php
Certificate::class
  - user()              // BelongsTo User
  - course()            // BelongsTo Course
  - getDownloadUrl()    // Get download URL
  - getPreviewUrl()     // Get preview URL

User::class
  - certificates()                 // HasMany Certificate
  - getCertificateForCourse($id)  // Get certificate for course

Course::class
  - certificates()              // HasMany Certificate
  - markAsCompletedFor($user)  // Auto-generate certificate
```

### Service
```php
CertificateService::class
  - generateCertificate($user, $course)      // Generate new
  - getCertificate($user, $course)           // Get existing
  - regeneratePDF($certificate)              // Regenerate PDF
  - verifyCertificate($code)                 // Verify by code
  - downloadCertificate($certificate)        // Download response
  - previewCertificate($certificate)         // Preview response
```

## Support

### Documentation
- Full docs: `docs/Certificate-System.md`
- Summary: `docs/Certificate-Implementation-Summary.md`
- Progress tracking: `docs/Progress-Tracking-System.md`

### Logs Location
```
storage/logs/laravel.log
```

### Database Tables
```
certificates
course_user (pivot)
lesson_user (pivot)
quiz_attempts
```

---

**Ready to test!** 🚀

Lengkapi sebuah course sampai 100%, dan certificate akan otomatis ter-generate! 🎓
