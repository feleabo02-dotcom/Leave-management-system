<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccrualPlan;
use App\Models\AccrualPlanLevel;
use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccrualPlanController extends Controller
{
    public function index(): View
    {
        $plans = AccrualPlan::with(['leaveType', 'levels'])->get();
        $leaveTypes = LeaveType::query()->where('active', true)->get();

        return view('erp.admin.accrual-plans', compact('plans', 'leaveTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'transition_mode' => ['required', 'in:immediately,end_of_accrual'],
            'accrued_gain_time' => ['required', 'in:start,end'],
            'carryover_date' => ['required', 'in:year_start,allocation,other'],
            'custom_carryover_date' => ['nullable', 'date'],
            'is_based_on_worked_time' => ['boolean'],
        ]);

        AccrualPlan::create($data);

        return redirect()->route('admin.accrual-plans')
            ->with('status', 'accrual-plan-created');
    }

    public function storeLevel(Request $request, AccrualPlan $accrualPlan): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'added_value' => ['required', 'numeric', 'min:0'],
            'added_value_type' => ['required', 'in:days,hours'],
            'frequency' => ['required', 'in:daily,weekly,biweekly,monthly,bimonthly,quarterly,biyearly,yearly'],
            'first_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'first_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'cap_accrued_time' => ['boolean'],
            'cap_accrued_time_amount' => ['nullable', 'numeric', 'min:0'],
            'cap_accrued_time_yearly' => ['boolean'],
            'cap_accrued_time_yearly_amount' => ['nullable', 'numeric', 'min:0'],
            'action_with_unused_accruals' => ['required', 'in:lost,carry_over'],
            'carryover_options' => ['required', 'in:unlimited,limited'],
            'carryover_limit_days' => ['nullable', 'integer', 'min:0'],
            'accrual_validity_days' => ['nullable', 'integer', 'min:1'],
        ]);

        $maxSeq = $accrualPlan->levels()->max('sequence') ?? 0;

        $accrualPlan->levels()->create(array_merge($data, [
            'sequence' => $maxSeq + 1,
        ]));

        return redirect()->route('admin.accrual-plans')
            ->with('status', 'accrual-level-created');
    }

    public function destroyLevel(AccrualPlan $accrualPlan, AccrualPlanLevel $level): RedirectResponse
    {
        $level->delete();

        return redirect()->route('admin.accrual-plans')
            ->with('status', 'accrual-level-deleted');
    }

    public function destroy(AccrualPlan $accrualPlan): RedirectResponse
    {
        $accrualPlan->delete();

        return redirect()->route('admin.accrual-plans')
            ->with('status', 'accrual-plan-deleted');
    }

    public function runAccruals(): RedirectResponse
    {
        $processed = app(\App\Services\AccrualService::class)->processAllAccruals();

        return redirect()->route('admin.accrual-plans')
            ->with('status', "accruals-processed-{$processed}");
    }
}
