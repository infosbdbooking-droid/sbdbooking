<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BlogCommentController extends Controller
{
    // Return view
    public function view()
    {
        return view('blogs.comments');
    }

    // Ajax data for Datatables
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = BlogComment::with('blog')->orderBy('id', 'desc');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('blog_title', function ($row) {
                        return $row->blog ? $row->blog->title : 'N/A';
                    })
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Approve comment
    public function approve($id)
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->status = 'Approved';
            $comment->save();

            return response()->json([
                'success' => true,
                'message' => 'Comment approved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve comment.'
            ], 500);
        }
    }

    // Reject comment
    public function reject($id)
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->status = 'Rejected';
            $comment->save();

            return response()->json([
                'success' => true,
                'message' => 'Comment rejected successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject comment.'
            ], 500);
        }
    }

    // Delete comment
    public function destroy($id)
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment.'
            ], 500);
        }
    }

    // Bulk actions
    public function bulkAction(Request $request)
    {
        try {
            $ids = $request->ids;
            $action = $request->action;

            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No comments selected.'
                ], 422);
            }

            if ($action === 'approve') {
                BlogComment::whereIn('id', $ids)->update(['status' => 'Approved']);
                $msg = 'Selected comments approved.';
            } elseif ($action === 'reject') {
                BlogComment::whereIn('id', $ids)->update(['status' => 'Rejected']);
                $msg = 'Selected comments rejected.';
            } elseif ($action === 'delete') {
                BlogComment::whereIn('id', $ids)->delete();
                $msg = 'Selected comments deleted.';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid bulk action.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute bulk action.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
