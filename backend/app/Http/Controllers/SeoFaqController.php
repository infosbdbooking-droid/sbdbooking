<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoFaq;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SeoFaqController extends Controller
{
    public function view()
    {
        return view('seo.faqs');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = SeoFaq::query()->orderBy('id', 'desc');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:500',
                'answer' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $faq = new SeoFaq();
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->status = $request->has('status') ? (int)$request->status : 1;
            $faq->save();

            return response()->json([
                'success' => true,
                'message' => 'FAQ added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store FAQ.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $faq = SeoFaq::findOrFail($id);
            return response()->json($faq);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not found.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:500',
                'answer' => 'required|string',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $faq = SeoFaq::findOrFail($id);
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->status = $request->status;
            $faq->save();

            return response()->json([
                'success' => true,
                'message' => 'FAQ updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FAQ.'
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $faq = SeoFaq::findOrFail($id);
            $faq->status = (int)$request->status;
            $faq->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $faq = SeoFaq::findOrFail($id);
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete.'
            ], 500);
        }
    }
}
