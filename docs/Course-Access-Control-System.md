# 🔐 Course Access Control & Enrollment System

## ✨ **Fitur yang Diimplementasikan**

### 1. **Role-Based Course Access**
- **Admin**: Akses penuh ke semua course tanpa batasan
- **Pengajar**: 
  - Akses penuh untuk course milik sendiri
  - Perlu enrollment untuk course pengajar lain
- **Karyawan**: Perlu enrollment untuk semua course

### 2. **Dynamic Course View**
Course detail page sekarang memiliki 2 mode:
1. **Access Denied Mode** - Menampilkan preview dan enrollment option
2. **Full Access Mode** - Menampilkan semua tabs course

### 3. **Enrollment System**
- Karyawan bisa self-enroll ke course
- Pengajar perlu approval admin untuk akses course lain

## 🏗 **Architecture Overview**

### **Middleware Flow**
```
User Access Course → CourseAccessMiddleware → Controller
                          ↓
               Check Role & Enrollment Status
                          ↓
            Pass access_denied flag to Controller
                          ↓
               Controller handles view rendering
```

### **Controller Logic**
```php
if ($accessDenied) {
    // Mode 1: Preview + Enrollment
    return view with enrollmentMessage & canEnroll
} else {
    // Mode 2: Full Course Access  
    return view with full course data
}
```

## 🎯 **User Experience Per Role**

### **👤 Admin**
- ✅ Akses langsung ke semua course
- ✅ Semua tabs: Detail, Modul & Quiz, Peserta, Overview
- ✅ Full management capabilities

### **👨‍🏫 Pengajar**

#### **Own Course:**
- ✅ Akses penuh seperti admin
- ✅ Semua tabs tersedia
- ✅ Bisa edit dan manage

#### **Other's Course (Not Enrolled):**
- ⚠️ Preview mode dengan pesan: "Anda belum diberikan akses ke kursus ini"
- ❌ Tidak ada tombol self-enroll
- ℹ️ Perlu hubungi admin untuk akses

#### **Other's Course (Enrolled):**
- ✅ Tab: Detail Kursus, Modul & Quiz
- ❌ Tab Peserta & Overview tidak terlihat

### **👨‍💼 Karyawan**

#### **Not Enrolled:**
- ⚠️ Preview mode dengan pesan: "Anda perlu mendaftar di kursus ini"
- ✅ Tombol "Daftar Kursus" tersedia
- 👀 Preview info course (thumbnail, deskripsi, instruktur)

#### **Enrolled:**
- ✅ Tab: Detail Kursus, Modul & Quiz
- ❌ Tab Peserta & Overview tidak terlihat
- 📚 Akses penuh untuk belajar

## 📁 **File yang Dimodifikasi/Dibuat**

### 1. **Middleware Update**
**`app/Http/Middleware/CourseAccessMiddleware.php`**
```php
// Tidak langsung redirect, tapi pass flag ke controller
$request->merge(['access_denied' => true, 'user_role' => $role]);
return $next($request);
```

### 2. **Controller Enhancement**  
**`app/Http/Controllers/Course/CourseController.php`**
```php
public function show(Request $request, string $id) {
    // Handle access denied cases
    if ($request->get('access_denied')) {
        return view with enrollment flow;
    }
    // Handle normal access
    return view with full course data;
}

public function enroll(Course $course) {
    // Self-enrollment logic
}
```

### 3. **Routes Addition**
**`routes/web.php`**
```php
// Enrollment route
Route::middleware('role:karyawan,pengajar')
    ->post('course/{course}/enroll', 'enroll')
    ->name('course.enroll');
```

### 4. **View Enhancement**
**`resources/views/pages/course/show.blade.php`**
```blade
@if(isset($accessDenied) && $accessDenied)
    {{-- Access Denied Mode --}}
    {{-- Preview + Enrollment Form --}}
@else
    {{-- Full Access Mode --}}
    {{-- All Tabs with Role-based Visibility --}}
@endif
```

## 🎨 **UI/UX Features**

### **Access Denied Screen**
- 🔒 Lock icon visual indicator
- ⚠️ Warning alert with role-specific message
- 🖼️ Course preview (thumbnail, description, instructor)
- 🔵 Enrollment button (untuk karyawan)
- ⬅️ Back to courses link

### **Tab Visibility Logic**
```blade
{{-- Detail Kursus: Semua enrolled user --}}
{{-- Modul & Quiz: Semua enrolled user --}}
{{-- Peserta & Akses: Owner + Admin only --}}
{{-- Overview: Owner + Admin only --}}
```

## 🧪 **Testing Scenarios**

### **Test Case 1: Admin**
- Login sebagai admin → Akses semua course → Semua tabs terlihat

### **Test Case 2: Pengajar - Own Course**  
- Login sebagai pengajar → Akses course sendiri → Full access

### **Test Case 3: Pengajar - Not Enrolled**
- Login sebagai pengajar → Akses course lain → Preview + "Belum diberi akses"

### **Test Case 4: Karyawan - Not Enrolled**
- Login sebagai karyawan → Akses course → Preview + tombol "Daftar Kursus"

### **Test Case 5: Karyawan - Enrollment Flow**
- Klik "Daftar Kursus" → Redirect ke course detail → Akses penuh modul & quiz

### **Test Case 6: Karyawan - Enrolled**
- Login sebagai karyawan enrolled → Akses course → Tab detail + modul only

## 🚀 **Next Steps Implementation**

1. **Notification System** - Email notifikasi setelah enrollment
2. **Progress Tracking** - Track pembelajaran user per course
3. **Certificate System** - Generate certificate setelah course completed
4. **Admin Approval** - Workflow approval untuk pengajar access request

## ⚙️ **Database Schema**

**Table: course_user (Enrollment)**
```sql
- user_id (FK)
- course_id (FK)  
- enrolled_at (timestamp)
- completed_at (timestamp, nullable)
```

**Usage:**
```php
// Check enrollment
$user->isEnrolledIn($course)

// Enroll user  
$user->enrolledCourses()->attach($course->id, ['enrolled_at' => now()])
```

**✅ Status: IMPLEMENTED & READY FOR TESTING**
