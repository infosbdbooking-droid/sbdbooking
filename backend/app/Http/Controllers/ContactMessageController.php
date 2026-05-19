<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $messages = ContactMessage::query()->orderBy('id', 'desc')->paginate(10);
                
                if ($messages->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No contact messages found.',
                        'data' => []
                    ], 200);
                }
                
                return response()->json($messages);
            }

            return view('contact_messages.index');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Pending,In Progress,Resolved',
                'admin_reply' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = ContactMessage::findOrFail($id);
            $message->status = $request->status;
            $message->admin_reply = $request->admin_reply;
            $message->save();

            return response()->json([
                'success' => true,
                'message' => 'Contact message updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact message deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
