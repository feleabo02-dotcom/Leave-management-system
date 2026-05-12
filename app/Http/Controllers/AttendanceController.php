<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $this->authorize('attendance.read');
        $attendances = Attendance::with(['employee.user', 'employee.department'])->latest()->paginate(20);
        return view('erp.attendance.index', compact('attendances'));
    }

    public function myAttendance()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee profile not found.');
        }

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->first();

        $history = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->take(10)
            ->get();

        return view('erp.attendance.my-attendance', compact('employee', 'todayAttendance', 'history'));
    }

    public function checkIn(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) return back()->with('error', 'Employee profile not found.');

        $date = now()->toDateString();
        
        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $date],
            ['status' => 'present']
        );

        if ($attendance->check_in) {
            return back()->with('error', 'You have already checked in today.');
        }

        $checkInTime = now();
        $lateness = 0;

        if ($employee->shift) {
            $shiftStart = Carbon::createFromFormat('H:i:s', $employee->shift->start_time)
                ->setDate($checkInTime->year, $checkInTime->month, $checkInTime->day);
            
            $graceEnd = $shiftStart->copy()->addMinutes($employee->shift->grace_period);
            
            if ($checkInTime->gt($graceEnd)) {
                $lateness = $checkInTime->diffInMinutes($shiftStart);
                $attendance->status = 'late';
            }
        }

        $attendance->update([
            'check_in' => $checkInTime,
            'check_in_ip' => $request->ip(),
            'lateness_minutes' => $lateness,
        ]);

        return back()->with('success', 'Checked in successfully at ' . $checkInTime->format('H:i'));
    }

    public function checkOut(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) return back()->with('error', 'Employee profile not found.');
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return back()->with('error', 'You must check in before checking out.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'You have already checked out today.');
        }

        $checkOutTime = now();
        $totalMinutes = $checkOutTime->diffInMinutes($attendance->check_in);
        $totalHours = round($totalMinutes / 60, 2);
        
        $overtime = 0;
        if ($employee->shift) {
            $shiftEnd = Carbon::createFromFormat('H:i:s', $employee->shift->end_time)
                ->setDate($checkOutTime->year, $checkOutTime->month, $checkOutTime->day);
            
            if ($checkOutTime->gt($shiftEnd)) {
                $overtime = round($checkOutTime->diffInMinutes($shiftEnd) / 60, 2);
            }
        }

        $attendance->update([
            'check_out' => $checkOutTime,
            'check_out_ip' => $request->ip(),
            'total_hours' => $totalHours,
            'overtime_hours' => $overtime,
        ]);

        return back()->with('success', 'Checked out successfully at ' . $checkOutTime->format('H:i'));
    }
}
