<?php

namespace App\Http\Controllers\Landlord;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRController extends Controller
{
    public function recognize(Request $request)
    {
        $request->validate([
            'cccd_image' => 'required|image|max:5120',
        ]);

        try {
            $imagePath = $request->file('cccd_image')->getRealPath();
            $ocr = new TesseractOCR($imagePath);
            $text = $ocr->lang('vie')->run(); // Hoặc 'eng' nếu CCCD in kiểu số tiếng Anh

            // Tìm số CCCD: thường là chuỗi dài nhất 9-12 chữ số
            preg_match('/\d{9,12}/', $text, $matches);
            $identityNumber = $matches[0] ?? null;

            return response()->json([
                'identity_number' => $identityNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'OCR failed: '.$e->getMessage()
            ], 500);
        }
    }
}
