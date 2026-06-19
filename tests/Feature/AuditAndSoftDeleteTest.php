<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditAndSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{name: string, city: string, country: string, rating: int}
     */
    private function hotelAttributes(): array
    {
        return [
            'name' => 'Base Model Inn',
            'city' => 'Dubai',
            'country' => 'AE',
            'rating' => 5,
        ];
    }

    public function test_deleting_a_record_soft_deletes_it_and_hides_it_from_default_queries(): void
    {
        $hotel = Hotel::create($this->hotelAttributes());

        $hotel->delete();

        $this->assertSoftDeleted('hotels', ['id' => $hotel->id]);
        $this->assertNull(Hotel::find($hotel->id));
        $this->assertNotNull(Hotel::withTrashed()->find($hotel->id));
    }

    public function test_created_and_updated_by_are_stamped_with_the_authenticated_user(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();

        $this->actingAs($author);
        $hotel = Hotel::create($this->hotelAttributes());

        $this->assertSame($author->id, $hotel->created_by);
        $this->assertSame($author->id, $hotel->updated_by);

        $this->actingAs($editor);
        $hotel->update(['rating' => 4]);

        $this->assertSame($author->id, $hotel->fresh()->created_by);
        $this->assertSame($editor->id, $hotel->fresh()->updated_by);
    }

    public function test_deleted_by_is_stamped_on_soft_delete(): void
    {
        $user = User::factory()->create();

        $hotel = Hotel::create($this->hotelAttributes());

        $this->actingAs($user);
        $hotel->delete();

        $this->assertSame($user->id, Hotel::withTrashed()->find($hotel->id)->deleted_by);
    }

    public function test_audit_columns_stay_null_without_an_authenticated_user(): void
    {
        $hotel = Hotel::create($this->hotelAttributes());

        $this->assertNull($hotel->created_by);
        $this->assertNull($hotel->updated_by);
    }
}
