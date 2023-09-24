<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateResponsibilityRequest;


class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        //powerhuman.com/api/responsibility?id=1
        $ResponsibilityQuery = Responsibility::query();

        // Get single data
        if ($id) {
            $responsibility = $ResponsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility found');
            }

            return ResponseFormatter::error('Responsibility not found', 404);
        }


        //powerhuman.com/api/responsibility
        // Get multiple data
        $Responsibilities = $ResponsibilityQuery->where('role_id', $request->role_id);

        if ($name) {
            $Responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $Responsibilities->paginate($limit),
            'Responsibilities Success'
        );
    }

    public function create(CreateResponsibilityRequest $request)
    {

        try {

            // Create Responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if (!$responsibility) {
                throw new Exception('Responsibility not created');
            }

            return ResponseFormatter::success($responsibility, 'Responsibility created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    public function destroy($id)
    {
        try {
            // get Responsibility
            $responsibility = Responsibility::find($id);

            // TODO: Check if responsibility is owned by user

            // Check if responsibility exits
            if (!$responsibility) {
                throw new Exception('Responsibility not found');
            }

            // Delete Responsibility
            $responsibility->delete();

            return ResponseFormatter::success('Responsibility deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
