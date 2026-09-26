<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Public Certificate Verification Page.
     * Anyone can verify a certificate by scanning the QR code or searching by verification code / certificate number.
     */
    public function verify(?string $code = null)
    {
        $certificate = null;
        $searched = false;

        if ($code) {
            $searched = true;
            $certificate = Certificate::with(['student', 'course', 'branch', 'franchise'])
                ->where('verification_code', $code)
                ->orWhere('certificate_number', $code)
                ->first();
        }

        return view('public.verify-certificate', compact('certificate', 'searched', 'code'));
    }

    /**
     * Search handler for manual certificate lookup.
     */
    public function search(Request $request)
    {
        $code = trim($request->input('code'));
        if (empty($code)) {
            return redirect()->route('certificate.verify');
        }

        return redirect()->route('certificate.verify', ['code' => $code]);
    }

    /**
     * Printable Official Certificate View.
     */
    public function print(string $code)
    {
        $certificate = Certificate::with(['student', 'course', 'branch', 'franchise'])
            ->where('verification_code', $code)
            ->firstOrFail();

        return view('certificates.print-template', compact('certificate'));
    }
}
