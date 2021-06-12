<?php


namespace Tests\Feature\App\Http\Controllers;


use App\Mail\GreetingsRegister;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testViewLogin()
    {
        $response = $this->get(route('login'));

        $response->assertViewIs('auth.login');
        $response->assertSee('Login');
    }

    public function testViewRegister()
    {
        $response = $this->get(route('register'));

        $response->assertViewIs('auth.register');
        $response->assertSee('Registre-se');
    }

    public function test_client_can_see_login_page()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_client_can_not_see_login_page()
    {
        //Prepare
        $user = User::factory()->create();
        $this->actingAs($user);

        //Act
        $response = $this->get(route('login'));

        //Assert
        $response->assertRedirect(route('campus'));
    }

    public function test_client_can_see_campus()
    {
        //Prepare
        $user = User::factory()->create();
        $this->actingAs($user);

        //Act
        $response = $this->get(route('campus'));

        //Assert
        $response->assertSee("Seja Bem Vindo(a) $user->name");
    }

    public function test_client_should_authenticate()
    {
        //Prepare
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password'
        ];

        //Act
        $response = $this->post(route('post.login'), $payload);

        //Assert
        $response->assertStatus(200);
    }

    public function test_client_can_not_should_authenticate()
    {
        //Prepare
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'incorrect_password'
        ];

        //Act
        $response = $this->post(route('post.login'), $payload);

        //Assert
        $response->assertStatus(401);
    }

    public function test_client_should_register()
    {
        //Prepare
        Mail::fake();

        $payload = [
            'name' => 'TestUser',
            'username' => 'username',
            'email' => 'test@gmail.com',
            'password' => 'password'
        ];

        //Act
        $response = $this->post(route('post.register'), $payload);

        //Assert
        $response->assertOk();

        $response->assertJsonStructure(['name', 'username']);

        $this->assertDatabaseHas('users', [
            'name' => $payload["name"],
            'email' => $payload["email"]
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $response->decodeResponseJson()["id"]
        ]);

        Mail::assertSent(GreetingsRegister::class, function ($email) use ($payload) {
            return $email->hasTo($payload["email"]);
        });
    }

    public function test_client_can_not_register()
    {
        //Prepare
        $user = User::factory()->create();

        $payload = [
            'name' => 'Guilherme',
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'incorrect_password'
        ];

        //Act
        $response = $this->post(route('post.register'), $payload);

        //Assert
        $response->assertStatus(302);
    }

    public function test_client_can_logout()
    {
        //Prepare
        $user = User::factory()->create();
        $this->actingAs($user);

        //Act
        $response = $this->get(route('logout'));

        //Assert
        $response->assertRedirect(route('login'));
    }


}
