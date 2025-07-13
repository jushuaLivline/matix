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

class RemindController extends Controller
{

  // AUTH -4: Password Reminder - Show form
  public function index(Request $request)
  {
    return view('pages.auth.remind.password.index');
  }

  // AUTH - 4: Handle password reset request
  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      // Find the user based on email and employee code
      $user = Employee::where('mail_address', $request->email)
        ->where('employee_code', $request->name)
        ->first();

      // If no user found, return with error and old input
      if (!$user) {
        return redirect()->back()
          ->with("error", "一致するアカウント情報がみつかりませんでした")
          ->withInput();
      }

      // Invalidate the current password
      $user->update(['password' => null,]);

      
      DB::commit();

      $data = [
        'subject' => 'パスワード再設定のお知らせ',
        'to_email' => $user->mail_address
      ];
      // Set the email recipient
      Mail::send(new PasswordReminderMail($data));

      // Redirect to completion screen
      return redirect()->route("auth.complete", ['type' => 'password']);

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

  // ID Reminder - Show form
  public function getRemindId(Request $request)
  {
    return view('pages.auth.remind.id.index');
  }

  // AUTH - 3: Handle ID reminder request
  public function RemindId(Request $request)
  {
    // Look up user by email
    $user = Employee::where('mail_address', $request->email)->first();

    if (!$user) {
      return redirect()->back()
        ->with("error", "一致するアカウント情報がみつかりませんでした")
        ->withInput();
    }

    // If password is set, confirm password match
    if ($user->password !== null && !Hash::check($request->password, $user->password)) {
      return redirect()->back()
        ->with("error", "一致するアカウント情報がみつかりませんでした")
        ->withInput();
    }

    // Email data for ID reminder
    $detail = [
      'user_name' => $user->employee_code,
    ];

    $data = [
        'subject' => 'ID再設定のお知らせ',
        'to_email' => $user->mail_address,
        'detail' => $detail,
    ];
    // Set the email recipient
    Mail::send(new IdReminderMail($data));

    // Redirect to completion screen
    return redirect()->route("auth.complete", ['type' => 'id']);
  }
}