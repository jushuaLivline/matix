<?php

namespace App\Services\Master;

use App\Models\MachineNumber;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class MachineService
{


  public function store($request)
  {
    // Validate the incoming request data
    $validated = $request->validated();

    // Store input into session for later reuse (e.g. repopulate form)
    session()->forget('mn_last_input'); // Clear old session data
    session()->put('mn_last_input', $validated); // Store new input

    // Remove non-database field if exists (e.g., from UI only)
    unset($validated['project_name']);

    // Format date fields using Carbon
    $validated['created_at'] = Carbon::parse($request->created_at)->format('Y-m-d H:i:s');
    $validated['drawing_date'] = Carbon::parse($request->drawing_date)->format('Y-m-d H:i:s');
    $validated['completion_date'] = Carbon::parse($request->completion_date)->format('Y-m-d H:i:s');

    // Create and save new machine number record
    $machineNumber = new MachineNumber($validated);
    $machineNumber->save();

    return $machineNumber;
  }

  public function update($request, $id)
  {

    // Validate incoming request data.
    $validated = $request->validated();

    // Format the dates using Carbon.
    $validated['created_at'] = Carbon::parse($request->created_at)->format('Y-m-d H:i:s');
    $validated['drawing_date'] = Carbon::parse($request->drawing_date)->format('Y-m-d H:i:s');
    $validated['completion_date'] = Carbon::parse($request->completion_date)->format('Y-m-d H:i:s');

    // Set delete_flag value: if checkbox exists (checked) then 1, otherwise 0.
    $validated['delete_flag'] = $request->has('delete_flag') ? 1 : 0;

    // Remove non-database field if exists (e.g., from UI only)
    unset($validated['project_name']);

    // Retrieve the MachineNumber by id or throw a 404 error if not found.
    $machineNumber = MachineNumber::findOrFail($id);

    // Update the machine number record with validated data.
    $machineNumber->update($validated);
    
    return $machineNumber;
  }
}
