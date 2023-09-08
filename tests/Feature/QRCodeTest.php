<?php

use App\Http\Controllers\QRCodeController;
use App\Models\QRCode;
use App\Models\User;
use App\Repositories\QRCodeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;

class QRCodeTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test creating a QR code with valid request data.
     *
     * @return void
     */
    public function testCreateQRCodeWithValidData()
    {
        $requestData = [
            'content' => 'some content for QR',
            'size' => 100,
            'background_color' => 'rgba(255,255,255,1)',
            'fill_color' => 'rgba(0,0,0,1)',
        ];

        $qrCodeRepository = $this->getMockBuilder(QRCodeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $qrCodeRepository->expects($this->once())
            ->method('createQRCode')
            ->with($requestData)
            ->willReturn('generated_qr_code_data');

        $controller = new QRCodeController($qrCodeRepository);

        $request = $this->mockRequest($requestData);

        $response = $controller->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->status());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'QR code created successfully', 'qr_code' => 'generated_qr_code_data']),
            $response->getContent()
        );
    }

    /**
     * Test creating a QR code with invalid request data.
     *
     * @return void
     */
    public function testCreateQRCodeWithInvalidData()
    {
        $invalidData = [
            'content' => '',
            'size' => 'not_an_integer',
            'background_color' => 'invalid_color_format',
            'fill_color' => 'invalid_color_format',
        ];

        $controller = new QRCodeController(new QRCodeRepository());

        $request = $this->mockRequest($invalidData);

        $response = $controller->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->status());

        // Assert that the JSON response contains the expected validation errors
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'errors' => [
                    'content' => ['The content field is required.'],
                    'size' => ['The size must be an integer.'],
                    'background_color' => ['The background color format is invalid.'],
                    'fill_color' => ['The fill color format is invalid.'],
                ],
            ]),
            $response->getContent()
        );
    }

    /**
     * Create a mock request with data.
     *
     * @param array $data
     * @return \Illuminate\Http\Request
     */
    protected function mockRequest(array $data)
    {
        $request = new \Illuminate\Http\Request();
        $request->replace($data);
        return $request;
    }

    public function testGetAllQRCodes()
    {
        QRCode::factory()->count(5)->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/qr-codes');
        $response->assertStatus(200);

        $response->assertJsonCount(5, 'qr_codes');
    }

    public function testGetSingleQRCodeForAuthUser()
    {
        $user = User::factory()->create();

        $qrCode = QRCode::factory()->create();

        $this->actingAs($user);

        $response = $this->get("/api/qr-codes/{$qrCode->id}");

        $response->assertStatus(200);
    }
}
