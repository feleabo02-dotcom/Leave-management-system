<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('attendance.read');

        $query = Attendance::with(['employee.user', 'employee.department']);

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        } else {
            $query->where('date', now()->toDateString());
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $request->department_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee.user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $attendances = $query->latest()->paginate(20);

        $today = $request->date ? Carbon::parse($request->date) : now();
        $totalEmployees = Employee::count();
        $presentToday = Attendance::where('date', $today->toDateString())->whereIn('status', ['present', 'late'])->count();
        $lateToday = Attendance::where('date', $today->toDateString())->where('status', 'late')->count();
        $absentToday = $totalEmployees - Attendance::where('date', $today->toDateString())->whereIn('status', ['present', 'late'])->count();
        $avgHours = Attendance::where('date', $today->toDateString())->avg('total_hours');

        $departments = Department::all();

        return view('erp.attendance.index', compact(
            'attendances', 'presentToday', 'lateToday', 'absentToday', 'avgHours', 'departments'
        ));
    }

    public function myAttendance(Request $request)
    {
        $employee = Auth::user()->employee;
        $todayAttendance = null;
        $monthlyRecords = collect();
        $monthlyStats = ['present' => 0, 'absent' => 0, 'late' => 0, 'total_hours' => 0, 'overtime_hours' => 0];
        $history = collect();

        if ($employee) {
            $todayAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', now()->toDateString())
                ->first();

            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);
            $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

            $monthlyRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orderBy('date')
                ->get()
                ->keyBy(fn($item) => $item->date->format('Y-m-d'));

            $monthlyStats = [
                'present' => $monthlyRecords->whereIn('status', ['present', 'late'])->count(),
                'absent' => $monthlyRecords->where('status', 'absent')->count(),
                'late' => $monthlyRecords->where('status', 'late')->count(),
                'total_hours' => $monthlyRecords->sum('total_hours'),
                'overtime_hours' => $monthlyRecords->sum('overtime_hours'),
            ];

            $history = Attendance::where('employee_id', $employee->id)
                ->latest()
                ->take(10)
                ->get();
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        return view('erp.attendance.my-attendance', compact(
            'employee', 'todayAttendance', 'history', 'monthlyRecords', 'monthlyStats', 'month', 'year',
            'startOfMonth', 'endOfMonth'
        ));
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

        if ($employee->shift && $employee->shift->start_time) {
            try {
                $shiftStart = Carbon::createFromFormat('H:i:s', $employee->shift->start_time)
                    ->setDate($checkInTime->year, $checkInTime->month, $checkInTime->day);

                $graceEnd = $shiftStart->copy()->addMinutes($employee->shift->grace_period ?? 0);

                if ($checkInTime->gt($graceEnd)) {
                    $lateness = (int) $checkInTime->diffInMinutes($shiftStart);
                    $attendance->status = 'late';
                }
            } catch (\Exception $e) {
                // If shift time parsing fails, skip lateness
            }
        }

        $attendance->update([
            'check_in' => $checkInTime,
            'check_in_ip' => $request->ip(),
            'lateness_minutes' => $lateness,
        ]);

        return back()->with('success', 'Checked in at ' . $checkInTime->format('H:i'));
    }

    public function checkOut(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) return back()->with('error', 'Employee profile not found.');

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return back()->with('error', 'You must check in first.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'You have already checked out today.');
        }

        $checkOutTime = now();
        $totalMinutes = $checkOutTime->diffInMinutes($attendance->check_in);
        $totalHours = round($totalMinutes / 60, 2);

        $overtime = 0;
        if ($employee->shift && $employee->shift->end_time) {
            try {
                $shiftEnd = Carbon::createFromFormat('H:i:s', $employee->shift->end_time)
                    ->setDate($checkOutTime->year, $checkOutTime->month, $checkOutTime->day);

                if ($checkOutTime->gt($shiftEnd)) {
                    $overtime = round($checkOutTime->diffInMinutes($shiftEnd) / 60, 2);
                }
            } catch (\Exception $e) {
                // If shift time parsing fails, skip overtime
            }
        }

        $attendance->update([
            'check_out' => $checkOutTime,
            'check_out_ip' => $request->ip(),
            'total_hours' => $totalHours,
            'overtime_hours' => $overtime,
        ]);

        return back()->with('success', 'Checked out at ' . $checkOutTime->format('H:i'));
    }

    public function store(Request $request)
    {
        $this->authorize('attendance.create');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave,holiday',
            'note' => 'nullable|string|max:500',
        ]);

        $data = [
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
        ];

        if ($validated['check_in']) {
            $data['check_in'] = Carbon::parse($validated['date'] . ' ' . $validated['check_in']);
        }
        if ($validated['check_out']) {
            $data['check_out'] = Carbon::parse($validated['date'] . ' ' . $validated['check_out']);
        }
        if ($data['check_in'] && $data['check_out']) {
            $data['total_hours'] = round($data['check_out']->diffInMinutes($data['check_in']) / 60, 2);
        }

        Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            $data
        );

        return back()->with('success', 'Attendance record saved.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $this->authorize('attendance.update');

        $validated = $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave,holiday',
            'note' => 'nullable|string|max:500',
        ]);

        $data = [
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
        ];

        if ($validated['check_in']) {
            $data['check_in'] = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $validated['check_in']);
        }
        if ($validated['check_out']) {
            $data['check_out'] = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $validated['check_out']);
        }
        if (isset($data['check_in']) && isset($data['check_out'])) {
            $data['total_hours'] = round($data['check_out']->diffInMinutes($data['check_in']) / 60, 2);
        }

        $attendance->update($data);

        return back()->with('success', 'Attendance record updated.');
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('attendance.export');

        $query = Attendance::with(['employee.user', 'employee.department']);

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $request->department_id));
        }

        $records = $query->orderBy('date', 'desc')->get();

        $filename = 'attendance-export-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Employee', 'Employee Code', 'Department', 'Check In', 'Check Out', 'Total Hours', 'Overtime', 'Lateness (min)', 'Status', 'Note']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->date->format('Y-m-d'),
                    $r->employee?->user?->name ?? 'Unknown',
                    $r->employee?->employee_code ?? '',
                    $r->employee?->department?->name ?? '',
                    $r->check_in?->format('H:i') ?? '',
                    $r->check_out?->format('H:i') ?? '',
                    $r->total_hours,
                    $r->overtime_hours,
                    $r->lateness_minutes,
                    $r->status,
                    $r->note ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
