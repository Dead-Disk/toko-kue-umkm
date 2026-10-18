<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_latest_products_when_there_are_no_transactions(): void
    {
        Product::factory()->count(3)->create(['nama_kue' => 'Kue Terbaru']);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee('Kue Terlaris / Favorit');
        $response->assertSee('Kue Terbaru');
    }

    public function test_landing_page_lists_best_selling_products_first(): void
    {
        $laku = Product::factory()->create(['nama_kue' => 'Kue Laku']);
        $jarang = Product::factory()->create(['nama_kue' => 'Kue Jarang']);
        $order = Order::factory()->create();

        OrderItem::factory()->for($order)->create(['product_id' => $laku->id, 'jumlah' => 10]);
        OrderItem::factory()->for($order)->create(['product_id' => $jarang->id, 'jumlah' => 1]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Kue Laku', 'Kue Jarang']);
    }
}
