<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Exports\MachineNumbersExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Http\Request;
use App\Models\MachineNumber;

use App\Constants\MachineNumberConstant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Master\MachineRequest;
use Carbon\Carbon;
use App\Services\Master\MachineService;


class MachineController extends Controller
{
  protected $machineService;

  public function __construct(MachineService $machineService)
  {
    $this->machineService = $machineService;
  }

  // MASTER - 158
  public function index(Request $request)
  {
    $data = MachineNumber::search($request)->paginateResults(20)->withQueryString();

    $data->getCollection()->transform(function ($item): object {
      return (object) [
        ...$item->toArray(),
        'created_at' => $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('Ymd') : '',
        'drawing_date' => $item->drawing_date ? \Carbon\Carbon::parse($item->drawing_date)->format('Ymd') : '',
        'completion_date' => $item->completion_date ? \Carbon\Carbon::parse($item->completion_date)->format('Ymd') : '',
        'machine_division' => MachineNumberConstant::MACHINE_DIVISION[$item->machine_division] ?? $item->machine_division,
      ];
    });

    $machineDivision = MachineNumberConstant::MACHINE_DIVISION;

    return view('pages.master.machine.index', compact('data', 'machineDivision'));
  }

  // MASTER - 158
  public function excel_export(Request $request)
  {
    $data = MachineNumber::search($request)->paginateResults(20)->withQueryString();

    $fileName = '機番マスタ一覧_'.now()->format('Ymd').'.xlsx';
    return Excel::download(new MachineNumbersExport($data), $fileName);

  }

  public function create(Request $request)
  {
    // Initialize constants for the machine division dropdown options
    $machineDivision = MachineNumberConstant::MACHINE_DIVISION;

    // Define the request method context (used in the view to distinguish create/edit)
    $requestMethod = 'add';

    // Retrieve the last inserted machine number ID from the session, default to empty array if not set
    $last_input = session('mn_last_input', []);

    // Return the edit view (used for both create and edit) with data compacted
    return view('pages.master.machine.edit', compact(
      'machineDivision',
      'requestMethod',
      'last_input'
    ));
  }
  public function edit(Request $request, $id)
  {
    // Fetch the machine number by ID with its related project
    $machineNumber = MachineNumber::with('project')->findOrFail($id);

    // Get machine division options from the constant
    $machineDivision = MachineNumberConstant::MACHINE_DIVISION;

    // Define context to indicate this is an edit form
    $requestMethod = 'edit';

    // Return the shared edit/create view with required data
    return view('pages.master.machine.edit', compact(
      'machineNumber',
      'machineDivision',
      'requestMethod'
    ));
  }

  public function update(MachineRequest $request, $id)
  {
    DB::beginTransaction();
    try {
      $this->machineService->update($request, $id);
      // Commit transaction.
      DB::commit();

      // Redirect back with a success message.
      return redirect()->route('master.masterMachine.index', $request->query())
        ->with('success', '機番マスタの更新が完了しました');

    } catch (\Exception $e) {
      // Rollback database changes if any errors occur.
      DB::rollBack();

      // Log the error for debugging purposes.
      Log::error('Machine Number Update Failed: ' . $e->getMessage());

      // Redirect back with an error message.
      return redirect()->back()->with('error', 'Failed to update Machine Number!');
    }
  }

  public function store(MachineRequest $request)
  {
    DB::beginTransaction();
    try {
      $this->machineService->store($request);
      DB::commit();
      // Redirect to create page again with success message and current query params
      return redirect()
        ->route('master.masterMachine.create', $request->query())
        ->with('success', '機番マスタの登録が完了しました');

    } catch (Exception $e) {
      DB::rollBack(); // Roll back DB changes on error
      Log::error('Machine Number Store Failed: ' . $e->getMessage());

      // Redirect back with error message
      return redirect()->back()->with('error', 'Failed to create Machine Number!');
    }
  }

  public function destroy(Request $request, $id)
  {
    DB::beginTransaction(); // Begin transaction to ensure data integrity

    try {
      // Retrieve the machine number by ID
      $machineNumber = MachineNumber::find($id);

      // Check if the machine number exists, else handle it gracefully
      if (!$machineNumber) {
        return redirect()->back()->with('error', 'Machine Number not found!');
      }

      // Perform deletion
      $machineNumber->delete();

      DB::commit(); // Commit the transaction

      // Redirect to index with a success message
      return redirect()->route('master.masterMachine.index', $request->query())
        ->with('success', '機番マスタの削除が完了しました。');

    } catch (Exception $e) {
      DB::rollBack(); // Rollback transaction in case of failure
      Log::error('Error deleting Machine Number: ' . $e->getMessage());

      // Redirect back with error message
      return redirect()->back()->with('error', 'Failed to delete Machine Number!');
    }
  }


  public function duplicate(Request $request)
  {
    // Retrieve the last input stored in the session for machine number
    $machineNumber = session()->get('mn_last_input', []);

    // If no data is found in the session, return an empty array
    return response()->json($machineNumber);
  }

  public function checkMachineNumber(Request $request)
  {
    // Check if the given machine number already exists in the database
    $exists = MachineNumber::where('machine_number', $request->machine_number)->exists();

    // Return false if the machine number exists, true otherwise (valid)
    return response()->json(!$exists);
  }
}