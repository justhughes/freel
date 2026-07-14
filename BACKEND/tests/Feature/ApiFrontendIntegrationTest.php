<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiFrontendIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
    }

    public function test_demo_client_can_login_and_load_frontend_data(): void
    {
        $login = $this->postJson('/api/auth/login', [
            'login' => 'a',
            'password' => 'a',
        ]);

        $login
            ->assertOk()
            ->assertJsonPath('user.role', User::ROLE_CLIENT)
            ->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/packages')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'base_price', 'category']]]);

        $this->withToken($token)
            ->getJson('/api/orders')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_client_can_create_order_and_payment_for_frontend_checkout(): void
    {
        Storage::fake('public');

        $client = User::query()->where('username', 'a')->firstOrFail();
        $package = \App\Models\ServicePackage::query()->where('is_active', true)->firstOrFail();
        $channel = PaymentChannel::query()->where('code', 'QRIS')->firstOrFail();

        Sanctum::actingAs($client);

        $response = $this->post('/api/orders', [
            'service_package_id' => $package->id,
            'title' => 'Konten Produk Test',
            'business_name' => 'UMKM Test',
            'product_description' => 'Produk untuk pengujian integrasi frontend.',
            'target_audience' => 'Pengguna media sosial.',
            'visual_reference' => 'Minimalis',
            'brief' => 'Buat konten yang cerah.',
            'platform' => 'Instagram',
            'content_size' => '1:1',
            'quantity' => 1,
            'speed_type' => Order::SPEED_REGULAR,
            'booking_date' => now()->addDay()->toDateString(),
            'payment_channel_id' => $channel->id,
            'assets' => [
                UploadedFile::fake()->image('produk.jpg'),
            ],
        ], [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', Order::STATUS_PENDING_PAYMENT)
            ->assertJsonStructure([
                'data' => ['id', 'order_code', 'assets', 'latest_payment'],
                'payment' => ['id', 'amount', 'channel'],
            ]);
    }

    public function test_admin_and_freelancer_endpoints_match_frontend_roles(): void
    {
        $admin = User::query()->where('username', 'b')->firstOrFail();
        Sanctum::actingAs($admin);

        $this->getJson('/api/orders')->assertOk();
        $this->getJson('/api/admin/revisions')->assertOk();

        $freelancer = User::query()->where('username', 'f')->firstOrFail();
        Sanctum::actingAs($freelancer);

        $this->getJson('/api/freelancer/jobs')->assertOk();
        $this->getJson('/api/freelancer/tasks')->assertOk();
    }
}
