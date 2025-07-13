<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    
    function download(Attachment $attachment){
        return response()->downloadAttachment($attachment);
    }

    function destroy(Request $request, Attachment $attachment){
        $attachment->delete();
        return $request->redirect ? redirect($request->redirect) : back();
    }
}
