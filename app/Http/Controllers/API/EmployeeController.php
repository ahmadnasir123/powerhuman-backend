<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $gender = $request->input('gender');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $company_id = $request->input('company_id');
        $limit = $request->input('limit', 10);

        //powerhuman.com/api/employee?id=1
        $companyQuery = Employee::query();

        // Get single data
        if ($id) {
            $employee = $companyQuery->with(['team', 'role'])->find($id);

            if ($employee) {
                return ResponseFormatter::success($employee, 'Employee found');
            }

            return ResponseFormatter::error('Employee not found', 404);
        }


        //powerhuman.com/api/employee
        // Get multiple data
        $employees = $companyQuery;

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }
        if ($email) {
            $employees->where('email', $email);
        }
        if ($gender) {
            $employees->where('gender', $gender);
        }
        if ($age) {
            $employees->where('age', $age);
        }
        if ($phone) {
            $employees->where('phone', 'like', '%' . $phone . '%');
        }
        if ($team_id) {
            $employees->where('team_id', $team_id);
        }
        if ($role_id) {
            $employees->where('role_id', $role_id);
        }
        if ($company_id) {
            $employees->whereHas('team', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }

        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employees Success'
        );
    }

    public function create(CreateEmployeeRequest $request)
    {

        try {
            // Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Create Employee
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            if (!$employee) {
                throw new Exception('Employee not created');
            }

            return ResponseFormatter::success($employee, 'Employee created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            // Get Employee
            $employee = Employee::find($id);

            // Check if employee exits
            if (!$employee) {
                throw new Exception('employee not found');
            }

            // Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Update Employee
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            return ResponseFormatter::success($employee, 'Employee updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // get Employee
            $employee = Employee::find($id);

            // TODO: Check if employee is owned by user

            // Check if employee exits
            if (!$employee) {
                throw new Exception('Employee not found');
            }

            // Delete Employee
            $employee->delete();

            return ResponseFormatter::success('Employee deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
