<?php

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;

test('admin can create short urls', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->for($company)->admin()->create();

    $response = $this->actingAs($admin)->post(route('short-urls.store'), [
        'long_url' => 'https://admin-test.example.com',
        '_token' => csrf_token(),
    ]);

    $response->assertRedirect(route('short-urls.index'));
    $this->assertDatabaseHas('short_urls', [
        'long_url' => 'https://admin-test.example.com',
        'user_id' => $admin->id,
        'company_id' => $company->id,
    ]);
});

test('member can create short urls', function () {
    $company = Company::factory()->create();
    $member = User::factory()->for($company)->member()->create();

    $response = $this->actingAs($member)->post(route('short-urls.store'), [
        'long_url' => 'https://member-test.example.com',
        '_token' => csrf_token(),
    ]);

    $response->assertRedirect(route('short-urls.index'));
    $this->assertDatabaseHas('short_urls', [
        'long_url' => 'https://member-test.example.com',
        'user_id' => $member->id,
        'company_id' => $company->id,
    ]);
});

test('super admin cannot create short urls', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($superAdmin)->post(route('short-urls.store'), [
        'long_url' => 'https://superadmin-test.example.com',
        '_token' => csrf_token(),
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('short_urls', [
        'long_url' => 'https://superadmin-test.example.com',
    ]);
});

test('admin sees only short urls created in their own company', function () {
    $companyA = Company::factory()->create(['name' => 'Company A']);
    $companyB = Company::factory()->create(['name' => 'Company B']);
    $adminA = User::factory()->for($companyA)->admin()->create();
    $adminB = User::factory()->for($companyB)->admin()->create();

    ShortUrl::factory()->for($companyA)->for($adminA)->create([
        'short_code' => 'onlya1',
        'long_url' => 'https://company-a.com',
    ]);
    ShortUrl::factory()->for($companyB)->for($adminB)->create([
        'short_code' => 'onlyb1',
        'long_url' => 'https://company-b.com',
    ]);

    $response = $this->actingAs($adminA)->get(route('short-urls.index'));

    $response->assertOk();
    $response->assertSee('onlya1');
    $response->assertDontSee('onlyb1');
});

test('member sees only short urls created by themselves', function () {
    $company = Company::factory()->create();
    $member1 = User::factory()->for($company)->member()->create();
    $member2 = User::factory()->for($company)->member()->create();

    ShortUrl::factory()->for($company)->for($member1)->create([
        'short_code' => 'memb1',
        'long_url' => 'https://member-one.com',
    ]);
    ShortUrl::factory()->for($company)->for($member2)->create([
        'short_code' => 'memb2',
        'long_url' => 'https://member-two.com',
    ]);

    $response = $this->actingAs($member1)->get(route('short-urls.index'));

    $response->assertOk();
    $response->assertSee('memb1');
    $response->assertDontSee('memb2');
});

test('short urls are publicly resolvable and redirect to the original url', function () {
    $company = Company::factory()->create();
    $user = User::factory()->for($company)->member()->create();
    $shortUrl = ShortUrl::factory()->for($company)->for($user)->create([
        'short_code' => 'public1',
        'long_url' => 'https://original-destination.com/page',
    ]);

    $response = $this->get('/s/' . $shortUrl->short_code);

    $response->assertRedirect('https://original-destination.com/page');
});
