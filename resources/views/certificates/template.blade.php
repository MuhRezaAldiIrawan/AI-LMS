<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #ffffff;
        }

        .certificate-container {
            width: 297mm; /* A4 landscape width */
            height: 210mm; /* A4 landscape height */
            padding: 20mm;
            margin: 0 auto;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 15px solid #4c51bf;
            box-shadow: 0 0 0 5px #fff, 0 0 0 10px #4c51bf;
        }

        .certificate-content {
            width: 100%;
            height: 100%;
            background: white;
            padding: 30px 40px;
            position: relative;
            border: 2px solid #cbd5e0;
        }

        /* Decorative corners */
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #4c51bf;
        }

        .corner-tl {
            top: 15px;
            left: 15px;
            border-right: none;
            border-bottom: none;
        }

        .corner-tr {
            top: 15px;
            right: 15px;
            border-left: none;
            border-bottom: none;
        }

        .corner-bl {
            bottom: 15px;
            left: 15px;
            border-right: none;
            border-top: none;
        }

        .corner-br {
            bottom: 15px;
            right: 15px;
            border-left: none;
            border-top: none;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #4c51bf;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: #718096;
            font-style: italic;
        }

        /* Title */
        .certificate-title {
            text-align: center;
            font-size: 48px;
            font-weight: bold;
            color: #2d3748;
            margin: 20px 0;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .certificate-subtitle {
            text-align: center;
            font-size: 18px;
            color: #718096;
            margin-bottom: 25px;
        }

        /* Content */
        .recipient-section {
            text-align: center;
            margin: 25px 0;
        }

        .awarded-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 10px;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #2d3748;
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 2px solid #4c51bf;
            display: inline-block;
            min-width: 400px;
        }

        .completion-text {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.8;
            margin: 20px 50px;
            text-align: center;
        }

        .course-name {
            font-size: 22px;
            font-weight: bold;
            color: #4c51bf;
            margin: 15px 0;
        }

        /* Footer */
        .footer-section {
            display: table;
            width: 100%;
            margin-top: 30px;
            padding-top: 20px;
        }

        .signature-block {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-line {
            width: 180px;
            border-top: 2px solid #2d3748;
            margin: 50px auto 10px;
        }

        .signature-name {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 3px;
        }

        .signature-title {
            font-size: 12px;
            color: #718096;
            font-style: italic;
        }

        /* Certificate Info */
        .certificate-info {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }

        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            font-size: 11px;
            color: #718096;
        }

        .info-right {
            text-align: right;
        }

        .verification-code {
            font-family: 'Courier New', monospace;
            color: #4c51bf;
            font-weight: bold;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 120px;
            color: rgba(76, 81, 191, 0.05);
            font-weight: bold;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-content">
            <!-- Decorative Corners -->
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <!-- Watermark -->
            <div class="watermark">LMS BOSOWA</div>

            <!-- Header -->
            <div class="header">
                <div class="logo">🎓 LMS BOSOWA</div>
                <div class="subtitle">Learning Management System</div>
            </div>

            <!-- Title -->
            <div class="certificate-title">Certificate</div>
            <div class="certificate-subtitle">of Completion</div>

            <!-- Recipient -->
            <div class="recipient-section">
                <div class="awarded-text">This certificate is proudly presented to</div>
                <div class="recipient-name">{{ $user->name }}</div>
            </div>

            <!-- Completion Text -->
            <div class="completion-text">
                For successfully completing the course
                <div class="course-name">{{ $course->title }}</div>
                Demonstrating dedication, commitment, and achievement in acquiring
                new knowledge and skills through our learning platform.
            </div>

            <!-- Footer with Signatures -->
            <div class="footer-section">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $course->author->name ?? 'Instructor' }}</div>
                    <div class="signature-title">Course Instructor</div>
                </div>

                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->issued_by ?? 'Administrator' }}</div>
                    <div class="signature-title">Platform Administrator</div>
                </div>

                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">Director</div>
                    <div class="signature-title">LMS Bosowa</div>
                </div>
            </div>

            <!-- Certificate Information -->
            <div class="certificate-info">
                <div class="info-left">
                    <strong>Certificate No:</strong> {{ $certificate->certificate_number }}<br>
                    <strong>Issue Date:</strong> {{ $certificate->issued_date->format('d F Y') }}
                </div>
                <div class="info-right">
                    <strong>Verification Code:</strong><br>
                    <span class="verification-code">{{ $certificate->verification_code }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
