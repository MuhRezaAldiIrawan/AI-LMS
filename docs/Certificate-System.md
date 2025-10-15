# Certificate Generation System - LMS Bosowa v2

## 📋 Overview
Sistem sertifikat otomatis yang akan generate dan memberikan sertifikat digital kepada user yang telah menyelesaikan course 100% (semua lessons dan quizzes completed).

## 🎯 Features

### ✅ Auto-Generate Certificate
- Sertifikat di-generate **otomatis** ketika user menyelesaikan course 100%
- Trigger: Saat semua lessons completed dan semua quizzes passed
- PDF dihasilkan dengan template professional

### ✅ Download & Preview
- **Download**: User bisa download sertifikat dalam format PDF
- **Preview**: Lihat sertifikat di browser sebelum download
- **Security**: Hanya user pemilik atau admin yang bisa akses

### ✅ Verification System
- Setiap sertifikat memiliki **verification code** unik
- Public verification page untuk verifikasi keaslian sertifikat
- Tidak perlu login untuk verify

### ✅ Certificate Management
- Admin bisa regenerate PDF jika corrupt
- Admin bisa manual generate certificate
- Certificate tracking di database

## 🏗️ Architecture

### Database Schema

#### Table: `certificates`
```sql
CREATE TABLE certificates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    certificate_number VARCHAR(255) UNIQUE NOT NULL,
    certificate_path VARCHAR(255) NULL,
    issued_date DATE NOT NULL,
    issued_by VARCHAR(255) NULL,
    verification_code TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, course_id)
);
```

**Fields:**
- `certificate_number`: Format CERT-YYYYMM-XXXXX (e.g., CERT-202510-00001)
- `certificate_path`: Path to PDF file in storage (e.g., certificates/CERT-202510-00001.pdf)
- `verification_code`: 16-character hex code untuk verifikasi
- `unique(user_id, course_id)`: Satu user hanya bisa punya 1 certificate per course

### Models & Relationships

#### Certificate Model
```php
Certificate::class
├── belongsTo(User::class)
├── belongsTo(Course::class)
└── methods:
    ├── generateCertificateNumber(): string
    ├── generateVerificationCode(): string
    ├── getDownloadUrl(): string
    ├── getPreviewUrl(): string
    ├── fileExists(): bool
    └── getFilePath(): ?string
```

#### User Model (updated)
```php
User::class
└── hasMany(Certificate::class)
    └── getCertificateForCourse($courseId)
```

#### Course Model (updated)
```php
Course::class
└── hasMany(Certificate::class)
```

## 🔄 Auto-Generation Flow

### 1. Course Completion Detection
```
User completes last lesson/quiz
    ↓
Check if course is 100% complete
    ↓
Course::isCompletedByUser($user) === true
    ↓
Course::markAsCompletedFor($user) triggered
```

### 2. Certificate Generation Process
```
markAsCompletedFor($user)
    ↓
1. Update completed_at in course_user pivot
    ↓
2. Award points to user
    ↓
3. Call CertificateService::generateCertificate()
    ↓
    ├── Check if certificate already exists
    ├── Verify course completion
    ├── Generate certificate_number
    ├── Generate verification_code
    ├── Create database record
    ├── Generate PDF from Blade template
    └── Save PDF to storage/app/public/certificates/
```

### 3. Storage Structure
```
storage/
└── app/
    └── public/
        └── certificates/
            ├── CERT-202510-00001.pdf
            ├── CERT-202510-00002.pdf
            └── ...
```

## 📄 Certificate Template

### Design Elements
- **Size**: A4 Landscape (297mm x 210mm)
- **Style**: Professional with decorative borders
- **Colors**: Purple gradient (#667eea to #764ba2)
- **Layout**:
  - Header: Logo & subtitle
  - Title: "CERTIFICATE of Completion"
  - Recipient name: Large & centered
  - Course name: Bold & highlighted
  - Completion text
  - 3 signature blocks (Instructor, Admin, Director)
  - Footer: Certificate number, issue date, verification code

### Template Variables
```blade
$certificate - Certificate model instance
$user - User model instance
$course - Course model instance
```

## 🛠️ Service Layer

### CertificateService Methods

#### `generateCertificate(User $user, Course $course): ?Certificate`
Generate new certificate atau return existing certificate.
- Validates course completion
- Creates database record
- Generates PDF file
- Returns Certificate instance or null

#### `getCertificate(User $user, Course $course): ?Certificate`
Get existing certificate untuk user di course tertentu.

#### `regeneratePDF(Certificate $certificate): bool`
Regenerate PDF file jika corrupt atau hilang.

#### `verifyCertificate(string $verificationCode): ?Certificate`
Verify certificate by verification code.

#### `downloadCertificate(Certificate $certificate)`
Return download response untuk certificate PDF.

#### `previewCertificate(Certificate $certificate)`
Return inline PDF preview response.

#### `deleteCertificate(Certificate $certificate): bool`
Delete certificate record dan PDF file.

## 🎨 Frontend Integration

### Employee Course View

#### Display Logic
```blade
@if($isEnrolled && $enrolledProgressPercentage === 100)
    @php
        $certificate = Auth::user()->getCertificateForCourse($course->id);
    @endphp
    
    @if($certificate)
        <!-- Show download & preview buttons -->
    @else
        <!-- Show "Certificate being processed" message -->
    @endif
@endif
```

#### UI Elements
- **Success Alert**: Blue gradient dengan icon certificate
- **Certificate Info**: Number & issue date
- **Download Button**: Primary button dengan icon
- **Preview Button**: Outline button, opens in new tab

## 🔒 Security & Authorization

### Route Protection
```php
// User atau admin bisa download/preview certificate mereka
Route::get('certificate/{id}/download', 'download')
    // Check: Auth::id() === $certificate->user_id || canAccess('admin')

// Admin only
Route::post('certificate/generate', 'generate')->middleware('admin')
Route::post('certificate/{id}/regenerate', 'regenerate')->middleware('admin')

// Public access (no auth)
Route::get('certificate/verify', 'verify')
```

### Authorization Check
```php
if (Auth::id() !== $certificate->user_id && !canAccess('admin')) {
    abort(403, 'Unauthorized access to certificate');
}
```

## 📍 Routes

### User Routes
```php
GET  /certificate/{id}/download          - Download certificate PDF
GET  /certificate/{id}/preview           - Preview certificate (inline)
GET  /certificate/course/{courseId}      - Get certificate for course (AJAX)
```

### Admin Routes
```php
POST /certificate/generate               - Manual generate certificate
POST /certificate/{id}/regenerate        - Regenerate PDF
```

### Public Routes
```php
GET  /certificate/verify?code=XXXXX      - Verify certificate authenticity
```

## 🧪 Testing Guide

### Manual Testing Steps

#### 1. Complete a Course
```bash
# Sebagai karyawan:
1. Login ke sistem
2. Enroll di course
3. Complete semua lessons
4. Pass semua quizzes
5. Refresh halaman course
```

**Expected Result:**
- Progress bar menunjukkan 100%
- Alert "Sertifikat Tersedia!" muncul
- Button "Download Sertifikat" dan "Preview" tersedia

#### 2. Download Certificate
```bash
1. Klik button "Download Sertifikat"
2. PDF file akan terdownload
```

**Expected Result:**
- File: `CERT-YYYYMM-XXXXX.pdf`
- Size: ~100-200KB
- Content: Certificate dengan nama user dan course

#### 3. Preview Certificate
```bash
1. Klik button "Preview"
2. PDF terbuka di tab baru
```

**Expected Result:**
- PDF tampil inline di browser
- Bisa zoom in/out
- Tidak auto-download

#### 4. Verify Certificate
```bash
1. Buka /certificate/verify
2. Input verification code dari certificate
3. Klik "Verifikasi Sekarang"
```

**Expected Result:**
- Jika valid: Tampil detail certificate (nama, course, tanggal)
- Jika invalid: Error message "Invalid verification code"

### Database Verification

#### Check Certificate Record
```sql
SELECT * FROM certificates 
WHERE user_id = [USER_ID] 
  AND course_id = [COURSE_ID];
```

**Expected Fields:**
- `certificate_number`: CERT-202510-XXXXX
- `certificate_path`: certificates/CERT-202510-XXXXX.pdf
- `issued_date`: Today's date
- `verification_code`: 16-char hex string

#### Check File Exists
```bash
ls storage/app/public/certificates/CERT-*.pdf
```

### API Testing (Optional)

#### Get Certificate for Course
```bash
curl -X GET \
  http://localhost/certificate/course/1 \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "certificate_number": "CERT-202510-00001",
        "issued_date": "15 Oct 2025",
        "download_url": "http://localhost/certificate/1/download",
        "preview_url": "http://localhost/certificate/1/preview"
    }
}
```

## 🐛 Troubleshooting

### Issue: Certificate tidak auto-generate
**Cause**: Course completion check gagal
**Solution**:
```php
// Check manually:
$user = User::find($userId);
$course = Course::find($courseId);
$isComplete = $course->isCompletedByUser($user);

// If false, check:
// 1. Apakah semua lessons completed?
$completedLessons = $user->completedLessons()->count();

// 2. Apakah semua quizzes passed?
$passedQuizzes = $user->quizAttempts()
    ->where('passed', true)
    ->whereIn('quiz_id', $course->modules->map->quiz->pluck('id'))
    ->count();
```

### Issue: PDF file not found
**Cause**: PDF generation gagal atau file terhapus
**Solution**:
```php
// Regenerate PDF (admin only)
POST /certificate/{id}/regenerate

// Or via code:
$certificateService = app(\App\Services\CertificateService::class);
$certificateService->regeneratePDF($certificate);
```

### Issue: Download tidak jalan
**Cause**: Storage link belum dibuat
**Solution**:
```bash
php artisan storage:link
```

### Issue: Permission denied saat generate PDF
**Cause**: Storage directory tidak writable
**Solution**:
```bash
chmod -R 775 storage/app/public/certificates
chown -R www-data:www-data storage/app/public/certificates
```

## 📊 Logs & Monitoring

### Important Logs
```php
// Certificate generation success
Log::info("Certificate auto-generated for user {$user->id} completing course {$this->id}");

// Certificate generation failed
Log::warning("Failed to auto-generate certificate for user {$user->id} completing course {$this->id}");

// PDF generation error
Log::error("Failed to generate PDF: " . $e->getMessage());
```

### Check Logs
```bash
tail -f storage/logs/laravel.log | grep -i certificate
```

## 🎓 Usage Examples

### For Karyawan (Employee)
1. **Complete Course**
   - Finish all lessons
   - Pass all quizzes
   - Progress reaches 100%

2. **Get Certificate**
   - Certificate auto-generated
   - Download button appears
   - Click to download PDF

3. **Share Certificate**
   - Download PDF
   - Share on social media
   - Verification code included

### For Admin
1. **Manual Generate**
   ```php
   POST /certificate/generate
   {
       "user_id": 5,
       "course_id": 1
   }
   ```

2. **Regenerate PDF**
   ```php
   POST /certificate/1/regenerate
   ```

3. **View All Certificates**
   ```sql
   SELECT c.*, u.name, co.title 
   FROM certificates c
   JOIN users u ON c.user_id = u.id
   JOIN courses co ON c.course_id = co.id
   ORDER BY c.created_at DESC;
   ```

## 📦 Dependencies

### Required Packages
- `barryvdh/laravel-dompdf: ^3.1` - PDF generation
- `dompdf/dompdf: ^3.1` - Core PDF library

### Install
```bash
composer require barryvdh/laravel-dompdf
```

## 🔧 Configuration

### DomPDF Config (Optional)
```php
// config/dompdf.php
return [
    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,
    'options' => [
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'log_output_file' => null,
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => 'serif',
        'dpi' => 96,
        'enable_php' => false,
        'enable_javascript' => true,
        'enable_remote' => true,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],
];
```

## 📁 File Structure

```
app/
├── Models/
│   └── Certificate.php
├── Services/
│   └── CertificateService.php
├── Http/
│   └── Controllers/
│       └── CertificateController.php
database/
└── migrations/
    └── 2025_10_15_021950_create_certificates_table.php
resources/
└── views/
    └── certificates/
        ├── template.blade.php
        └── verify.blade.php
routes/
└── web.php (certificate routes)
storage/
└── app/
    └── public/
        └── certificates/
            └── *.pdf
docs/
└── Certificate-System.md (this file)
```

## 🚀 Future Enhancements

### Potential Features
- [ ] Email notification dengan certificate attachment
- [ ] Certificate templates per course category
- [ ] Multi-language certificates
- [ ] QR code untuk quick verification
- [ ] Certificate expiry date (untuk renewal courses)
- [ ] Digital signature integration
- [ ] Blockchain verification (optional)
- [ ] Certificate gallery untuk user profile
- [ ] Social media sharing buttons
- [ ] Certificate analytics (download count, verification count)

## 📝 Notes

### Important Points
1. **Auto-generation** hanya trigger saat course 100% complete
2. **One certificate per user per course** (enforced by unique constraint)
3. **PDF storage** menggunakan Laravel Storage (public disk)
4. **Security** menggunakan authorization checks di controller
5. **Verification code** bersifat permanent dan unique

### Best Practices
- Always check file existence sebelum download/preview
- Use try-catch untuk PDF generation errors
- Log semua certificate operations
- Regular backup storage/certificates/ directory
- Monitor disk space usage

## 🎉 Conclusion

Sistem certificate ini fully automated dan terintegrasi dengan progress tracking system. Ketika user menyelesaikan course 100%, certificate akan otomatis di-generate dan langsung bisa di-download. User mendapat recognition atas pencapaian mereka dengan sertifikat professional yang bisa diverifikasi keasliannya.

**Status**: ✅ **READY TO USE**
