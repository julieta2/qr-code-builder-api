<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\QRCodeRepository;
use Illuminate\Support\Facades\Validator;

class QRCodeController extends Controller
{
    protected QRCodeRepository $qrCodeRepository;

    public function __construct(QRCodeRepository $qrCodeRepository)
    {
        $this->qrCodeRepository = $qrCodeRepository;
    }

    public function index()
    {
        $qrCodes = $this->qrCodeRepository->getAll();

        return response()->json(['qr_codes' => $qrCodes], 200);
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'size' => 'required|integer',
            'background_color' => ['required', 'regex:/rgba?\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})(?:,\s*([01](\.\d+)?))?\)/i'],
            'fill_color' => ['required', 'regex:/rgba?\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})(?:,\s*([01](\.\d+)?))?\)/i'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Create and store the QR code
        $qrCode = $this->qrCodeRepository->createQRCode($request->all());

        return response()->json(['message' => 'QR code created successfully', 'qr_code' => $qrCode], 201);
    }

    public function show($id)
    {
        $qrCode = $this->qrCodeRepository->getQRCodeById($id);

        if (!$qrCode) {
            return response()->json(['error' => 'QR code not found'], 404);
        }

        return response()->json(['qr_code' => $qrCode], 200);
    }
}

