<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderRevision;
use App\Models\Payment;
use App\Models\User;
use App\Services\OrderPricingService;
use App\Services\OrderWorkflowService;
use App\Services\PaymentService;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoreWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
    }

    public function test_application_uses_jakarta_timezone(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', now()->timezoneName);
    }

    public function test_deadline_keeps_current_clock_time_instead_of_always_2359(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-03 14:27:35', 'Asia/Jakarta'));

        $package = \App\Models\ServicePackage::query()->firstOrFail();
        $deadline = app(OrderPricingService::class)->deadline(
            $package,
            Order::SPEED_REGULAR,
            Carbon::parse('2026-07-04', 'Asia/Jakarta')
        );

        $this->assertSame('14:27:35', $deadline->format('H:i:s'));

        Carbon::setTestNow();
    }

    public function test_paid_order_enters_job_board(): void
    {
        $payment = Payment::query()
            ->where('status', Payment::STATUS_PENDING)
            ->firstOrFail();

        app(PaymentService::class)->markAsPaid($payment, 'TEST-GATEWAY-001');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_PAID,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $payment->order_id,
            'status' => Order::STATUS_QUEUE,
        ]);
    }

    public function test_freelancer_can_take_matching_job_once(): void
    {
        $freelancer = User::query()->where('username', 'f')->firstOrFail();
        $order = Order::query()->where('status', Order::STATUS_QUEUE)->firstOrFail();

        app(OrderWorkflowService::class)->takeJob($order, $freelancer);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'freelancer_id' => $freelancer->id,
            'status' => Order::STATUS_PROCESS,
        ]);
    }

    public function test_full_revision_cycle_returns_latest_result_to_client(): void
    {
        Storage::fake('public');

        $order = Order::query()
            ->where('status', Order::STATUS_REVIEW)
            ->with(['client', 'freelancer', 'currentSubmission'])
            ->firstOrFail();

        $client = $order->client;
        $freelancer = $order->freelancer;
        $admin = User::query()->where('username', 'b')->firstOrFail();
        $workflow = app(OrderWorkflowService::class);
        $firstVersion = $order->currentSubmission->version;

        $revision = $workflow->requestRevision(
            $order,
            $client,
            'Tolong warna dibuat lebih cerah dan logo diperbesar.'
        );

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_REVISION_REQUESTED,
            'revision_used' => 0,
        ]);
        $this->assertDatabaseHas('order_revisions', [
            'id' => $revision->id,
            'status' => OrderRevision::STATUS_PENDING_ADMIN,
        ]);

        $workflow->forwardRevision(
            $revision,
            $admin,
            'Mohon ikuti catatan klien dan pertahankan identitas brand.'
        );

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_REVISION,
            'revision_used' => 1,
        ]);

        $submission = $workflow->submitWork(
            $order->fresh(),
            $freelancer,
            UploadedFile::fake()->create(
                'hasil-revisi.pdf',
                100,
                'application/pdf'
            ),
            'Warna sudah dibuat lebih cerah dan logo diperbesar.'
        );

        $this->assertSame($firstVersion + 1, $submission->version);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_REVIEW,
        ]);
        $this->assertDatabaseHas('order_revisions', [
            'id' => $revision->id,
            'status' => OrderRevision::STATUS_COMPLETED,
            'result_submission_id' => $submission->id,
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('client.orders.show', $order));

        $response
            ->assertOk()
            ->assertSee('hasil-revisi.pdf')
            ->assertSee('Warna sudah dibuat lebih cerah')
            ->assertSee('Minta Revisi');
    }

    public function test_pending_revision_is_not_visible_to_freelancer_before_admin_forwards_it(): void
    {
        $order = Order::query()
            ->where('status', Order::STATUS_REVIEW)
            ->with(['client', 'freelancer'])
            ->firstOrFail();

        app(OrderWorkflowService::class)->requestRevision(
            $order,
            $order->client,
            'Catatan ini harus diperiksa admin terlebih dahulu.'
        );

        $response = $this
            ->actingAs($order->freelancer)
            ->get(route('freelancer.tasks.show', $order));

        $response
            ->assertOk()
            ->assertDontSee('Catatan ini harus diperiksa admin terlebih dahulu.');
    }

    public function test_api_routes_are_loaded_and_protected(): void
    {
        $this->assertFileExists(base_path('routes/api.php'));

        $this->getJson('/api/packages')
            ->assertUnauthorized();
    }
}
