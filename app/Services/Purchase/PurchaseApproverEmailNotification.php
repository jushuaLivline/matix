<?php

namespace App\Services\Purchase;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Mail\Purchase\PurchaseRejectedNotification;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PurchaseApproverEmailNotification
{
  public function purchaseNotification($request, $purchaseNotificationData, $next = false)
  {
    // Retrieve the approver's email
    $recipient = Employee::where('employee_code', $purchaseNotificationData->next_approver)
    ->where('purchasing_approval_request_email_notification_flag', 1)
    ->first();

    // added URLs
    $requisition_url = route('purchase.detail.showRequisitionApprovalDetails', ['id' => $purchaseNotificationData->requisition_number]);
    $approvals_search_url = route('purchase.approval.list.index');
    $purchaseNotificationData->requisition_url = $requisition_url;
    $purchaseNotificationData->approvals_search_url = $approvals_search_url;
     
    $data = [
      'subject' => '承認確認】購買依頼について',
      'to_email' => $recipient->mail_address,
      'purchaseNotificationData' => $purchaseNotificationData,
      'next' => $next,
    ];
   
    return $this->sendPurchaseNotification(
      $request,
      $purchaseNotificationData,
      new PurchaseApproverNotification($data),
      'purchaseNotification'
    );

  }

  public function rejectPurchaseNotification(Request $request, $purchaseNotificationData, $returned = false)
  {
    $requisition_url = route('purchase.detail.showRequisitionApprovalDetails', ['id' => $purchaseNotificationData->requisition_number]);
    $notify_person = $purchaseNotificationData->next_approver;

    // Notify the creator of the requisition 
    // field: 否認理由
    // URL: /purchase/approval/detail/{requisition_number}
    if ($request->notify_creator) {
      $notify_person = $request->notify_creator;
    }

    // Retrieve the approver's email
    $recipient = Employee::where('employee_code', $notify_person)
    ->where('purchasing_approval_request_email_notification_flag', 1)
    ->first();

    $data = [
      'subject' => '購買依頼差し戻しについて',
      'to_email' => $recipient->mail_address,
      'purchaseNotificationData' => $purchaseNotificationData,
      'requisition_url' => $requisition_url,
      'returned' => $returned,
    ];

    return $this->sendPurchaseNotification(
      $request,
      $purchaseNotificationData,
      new PurchaseRejectedNotification($data),
      'rejectPurchaseNotification'
    );
  }

  /**
   * Sends a purchase notification email to the next approver.
   *
   * @param Request $request
   * @param mixed $purchaseNotificationData
   * @param Mailable $emailInstance The email instance to be sent.
   * @param string $logContext A string indicating the context of the log (e.g., 'purchaseNotification' or 'rejectPurchaseNotification').
   * @return \Illuminate\Http\JsonResponse
   */
  private function sendPurchaseNotification(Request $request, $purchaseNotificationData, $emailInstance, $logContext)
  {
    try {
      // Retrieve the approver's email
      $recipient = Employee::where('employee_code', $purchaseNotificationData->next_approver)
        ->where('purchasing_approval_request_email_notification_flag', 1)
        ->first();

      if ($recipient) {

        // Set the email recipient
        Mail::send($emailInstance);

        // Log success
        // Log::info("Email sent successfully ({$logContext})", [
        //   'email' => $recipient->mail_address,
        //   'approver' => $request->user()->employee_code
        // ]);

        return response()->json(['message' => 'Purchase confirmation email sent!'], 200);
      }

      // Log warning if no recipient is found
      // Log::warning("No recipient found for purchase approval email ({$logContext}).", [
      //   'employee_code' => $request->user()->employee_code
      // ]);

      return response()->json(['message' => 'No recipient found for the email.'], 404);
    } catch (\Exception $e) {
      // Log error
      Log::error("Error sending purchase confirmation email ({$logContext}).", [
        'error' => $e->getMessage(),
        'employee_code' => $request->user()->employee_code
      ]);

      return response()->json(['message' => 'Failed to send email.'], 500);
    }
  }
}
