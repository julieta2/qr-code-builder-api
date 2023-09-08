<?php

namespace App\Repositories;

use App\Models\QRCode as QR;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeRepository
{
    public function createQRCode(array $data)
    {
        $pattern = '/rgba?\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})(?:,\s*([01](\.\d+)?))?\)/i';

        preg_match($pattern, $data['background_color'], $matchesBackground);
        preg_match($pattern, $data['fill_color'], $matchesFillColor);
        $matchesBackground = array_map('intval', array_slice(array_filter($matchesBackground, fn($el) => $el != ""), 1, count($matchesBackground)));
        $matchesFillColor = array_map('intval', array_slice(array_filter($matchesFillColor, fn($el) => $el != ""), 1, count($matchesFillColor)));
        $writer = new PngWriter();
        $qrCode = new QrCode($data['content']);
        $qrCode->setSize($data['size']);
        $qrCode->setBackgroundColor(new Color(...$matchesBackground));
        $qrCode->setForegroundColor(new Color(...$matchesFillColor));

        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();
        $qrCodeData = [
            'content' => $data['content'],
            'size' => $data['size'],
            'background_color' => $data['background_color'],
            'fill_color' => $data['fill_color'],
            'image' => $dataUri,
        ];
        return QR::create($qrCodeData);
    }

    public function getQRCodeById($id)
    {
        return QR::find($id);
    }

    public function getAll()
    {
        return QR::orderBy('created_at', 'desc')->get();
    }
}
