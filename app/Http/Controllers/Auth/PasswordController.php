<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Exports\Master\EmployeeExport;
use App\Models\Employee;
use App\Models\Authority;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordReminderMail;
use App\Mail\IdReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class PasswordController extends Controller
{

  public function index(Request $request)
  {
    $user = Employee::where('id', Crypt::decryptString($request->id ?? ''))->first();

    if (!$user) {
      return redirect()->route("auth.login");
    }
    return view('pages.auth.reset.password.index', [
      'user' => $user
    ]);

  }

  public function update(Request $request, $id)
  {

    DB::beginTransaction();
    try {
      $user = Employee::where('id', Crypt::decryptString($id ?? ''))->first();

      if (!$user) {
        return redirect()->route("auth.login");
      }

      $user->update([
        'password' => Hash::make($request->password),
      ]);

      DB::commit();

      // Redirect to completion screen
      return redirect()->route("auth.complete");

    } catch (\Exception $e) {
      DB::rollBack();

      // Log the error for debugging
      Log::error('Error encountered AUTH: ' . $e->getMessage());

      // Return JSON error response
      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}