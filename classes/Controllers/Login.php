<?php namespace Bkwld\Decoy\Controllers;

// Dependencies
use Auth;
use Bkwld\Decoy\Models\Admin;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Former;
use URL;
use View;

/**
 * Handle logging in of users.  This is based on the AuthController.php and
 * PasswordController that Laravel's `php artisan make:auth` generates.
 */
class Login extends Base {
  use AuthenticatesUsers, ThrottlesLogins;

  /**
   * Use the guest middleware to redirect logged in admins away from the login
   * screen, exepct for the getLogout() action.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
    $this->middleware('decoy.guest', ['except' => 'getLogout']);
  }

  /**
   * Get the guard to be used during authentication.
   *
   * @return string|null
   */
  protected function getGuard() {
    return config('decoy.core.guard');
  }

  /**
   * Show the application login form.
   *
   * @return \Illuminate\Http\Response
   */
  public function showLoginForm()   {

    // Pass validation rules
		Former::withRules(array(
			'email'    => 'required|email',
			'password' => 'required',
		));

		// Show the login homepage
		View::inject('title', 'Login');
		return View::make('decoy::layouts.blank', [
			'content' => View::make('decoy::account.login')->render(),
		]);
  }

  /**
   * Log the user out of the application.
   *
   * @return \Illuminate\Http\Response
   */
  public function logout() {

    // Logout the session
    Auth::guard($this->getGuard())->logout();

    // Redirect back to previous page so that switching users takes you back to
    // your previous page.
    $previous = url()->previous();
    if ($previous == url('/')) return redirect(route('decoy::account@login'));
    else return redirect($previous);
  }

  /**
   * Get the post register / login redirect path. This is set to the login route
   * so that the guest middleware can pick it up and redirect to the proper
   * start page.
   *
   * @return string
   */
  public function redirectPath() {
    return route('decoy::account@login');
  }
}
