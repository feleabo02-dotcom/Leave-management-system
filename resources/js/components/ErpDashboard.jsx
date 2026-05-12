import React, { useState } from 'react';
import {
    Menu, X, Search, Bell, Users, CheckSquare, DollarSign,
    Package, BarChart3, UserPlus, FileText, Plus, Clock,
    ChevronLeft, ChevronRight, LayoutDashboard, Briefcase,
    Truck, Factory, Building2, Settings, LogOut, UserCircle
} from 'lucide-react';

const attendanceData = [
    { name: 'Sarah Chen', dept: 'Engineering', time: '08:52 AM', status: 'on-time' },
    { name: 'Marcus Rivera', dept: 'Marketing', time: '09:05 AM', status: 'late' },
    { name: 'Aisha Patel', dept: 'Finance', time: '08:48 AM', status: 'on-time' },
    { name: 'James Kim', dept: 'Engineering', time: '09:22 AM', status: 'late' },
    { name: 'Elena Torres', dept: 'HR', time: '—', status: 'absent' },
];

const sideNav = [
    { icon: LayoutDashboard, label: 'Dashboard', active: true },
    { icon: Users, label: 'Employees' },
    { icon: Briefcase, label: 'Projects' },
    { icon: Truck, label: 'Supply Chain' },
    { icon: Factory, label: 'Manufacturing' },
    { icon: Building2, label: 'Finance' },
    { icon: Settings, label: 'Settings' },
];

export default function ErpDashboard() {
    const [collapsed, setCollapsed] = useState(false);

    return (
        <div className="h-screen flex bg-slate-50 text-slate-800 font-sans overflow-hidden">
            <Sidebar collapsed={collapsed} onToggle={() => setCollapsed(!collapsed)} />
            <div className="flex-1 flex flex-col min-w-0">
                <Topbar collapsed={collapsed} />
                <main className="flex-1 overflow-y-auto p-6 space-y-6">
                    <SummaryRow />
                    <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <SalesChart />
                        <RightPanel />
                    </div>
                    <AttendanceTable />
                </main>
            </div>
        </div>
    );
}

function Sidebar({ collapsed, onToggle }) {
    return (
        <aside className={`flex flex-col bg-slate-900 text-slate-300 transition-all duration-300 ${collapsed ? 'w-16' : 'w-60'}`}>
            <div className="flex items-center gap-3 px-4 h-16 border-b border-slate-800 flex-shrink-0">
                <div className="w-8 h-8 rounded-lg bg-cyan-500 flex items-center justify-center flex-shrink-0">
                    <span className="text-white font-bold text-sm">E</span>
                </div>
                {!collapsed && <span className="font-bold text-lg text-white tracking-tight">ERP Admin</span>}
            </div>
            <nav className="flex-1 py-4 px-2 space-y-1 overflow-y-auto">
                {sideNav.map((item) => (
                    <a key={item.label} href="#"
                        className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all ${
                            item.active
                                ? 'bg-cyan-500/10 text-cyan-400'
                                : 'text-slate-400 hover:bg-slate-800 hover:text-white'
                        }`}
                    >
                        <item.icon className="w-5 h-5 flex-shrink-0" />
                        {!collapsed && <span>{item.label}</span>}
                    </a>
                ))}
            </nav>
            <div className="p-3 border-t border-slate-800">
                <button onClick={onToggle} className="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm text-slate-500 hover:text-white hover:bg-slate-800 transition">
                    {collapsed ? <ChevronRight className="w-5 h-5" /> : <><ChevronLeft className="w-5 h-5" /> Collapse</>}
                </button>
            </div>
        </aside>
    );
}

function Topbar({ collapsed }) {
    return (
        <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 shadow-sm">
            <div className="flex items-center gap-4 flex-1">
                <div className="relative w-full max-w-md">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input type="text" placeholder="Search employees, invoices, projects..."
                        className="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500 text-slate-700 placeholder:text-slate-400" />
                </div>
            </div>
            <div className="flex items-center gap-3">
                <button className="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition border border-transparent hover:border-slate-200">
                    <Bell className="w-5 h-5" />
                    <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>
                <div className="h-6 w-px bg-slate-200"></div>
                <div className="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <div className="w-8 h-8 rounded-full bg-cyan-600 flex items-center justify-center text-white text-xs font-bold">JD</div>
                    <div className="hidden md:block">
                        <p className="text-sm font-semibold text-slate-800 leading-tight">John Doe</p>
                        <p className="text-[10px] text-slate-500 uppercase tracking-wide font-medium">Super Admin</p>
                    </div>
                </div>
            </div>
        </header>
    );
}

function SummaryRow() {
    const cards = [
        { label: 'Current Attendance', value: '342', sub: '82% present today', icon: Users, color: 'cyan' },
        { label: 'Active Tasks', value: '1,284', sub: '47 overdue', icon: CheckSquare, color: 'violet' },
        { label: 'Monthly Revenue', value: '$48,250', sub: '+12.3% vs last month', icon: DollarSign, color: 'emerald' },
        { label: 'Inventory Alerts', value: '18', sub: '7 items below threshold', icon: Package, color: 'amber' },
    ];

    const colorMap = {
        cyan: 'bg-cyan-50 text-cyan-700 border-cyan-200',
        violet: 'bg-violet-50 text-violet-700 border-violet-200',
        emerald: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        amber: 'bg-amber-50 text-amber-700 border-amber-200',
    };

    const iconMap = {
        cyan: 'text-cyan-600',
        violet: 'text-violet-600',
        emerald: 'text-emerald-600',
        amber: 'text-amber-600',
    };

    return (
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {cards.map((card) => (
                <div key={card.label} className="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition flex flex-col gap-3">
                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center border ${colorMap[card.color]}`}>
                        <card.icon className={`w-5 h-5 ${iconMap[card.color]}`} />
                    </div>
                    <div>
                        <p className="text-2xl font-bold text-slate-900">{card.value}</p>
                        <p className="text-xs text-slate-500 mt-0.5 font-medium">{card.label}</p>
                        <p className="text-[10px] text-slate-400 mt-0.5">{card.sub}</p>
                    </div>
                </div>
            ))}
        </div>
    );
}

function SalesChart() {
    return (
        <div className="xl:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
            <div className="flex items-center justify-between mb-6">
                <h2 className="font-semibold text-slate-800 flex items-center gap-2">
                    <BarChart3 className="w-5 h-5 text-cyan-500" /> Sales vs Expenses
                </h2>
                <select className="text-xs border border-slate-200 rounded-lg px-3 py-1.5 text-slate-600 bg-white outline-none">
                    <option>This Year</option>
                    <option>This Quarter</option>
                    <option>This Month</option>
                </select>
            </div>
            <div className="flex items-end gap-3 h-52">
                {['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].map((m, i) => {
                    const salesH = 40 + Math.sin(i * 1.2) * 25 + Math.random() * 20;
                    const expH = 30 + Math.cos(i * 0.9) * 20 + Math.random() * 15;
                    return (
                        <div key={m} className="flex-1 flex flex-col items-center gap-1">
                            <div className="w-full flex gap-[3px] items-end h-44">
                                <div className="flex-1 bg-cyan-500 rounded-t-md transition-all hover:bg-cyan-600"
                                    style={{ height: `${salesH}%` }} title={`Sales ${Math.round(salesH)}`}></div>
                                <div className="flex-1 bg-amber-400 rounded-t-md transition-all hover:bg-amber-500"
                                    style={{ height: `${expH}%` }} title={`Expenses ${Math.round(expH)}`}></div>
                            </div>
                            <span className="text-[10px] text-slate-400 font-medium">{m}</span>
                        </div>
                    );
                })}
            </div>
            <div className="flex items-center gap-4 mt-4 pt-4 border-t border-slate-100">
                <div className="flex items-center gap-2 text-xs text-slate-600"><div className="w-3 h-3 rounded bg-cyan-500"></div> Sales</div>
                <div className="flex items-center gap-2 text-xs text-slate-600"><div className="w-3 h-3 rounded bg-amber-400"></div> Expenses</div>
            </div>
        </div>
    );
}

function RightPanel() {
    const actions = [
        { icon: UserPlus, label: 'New Employee', color: 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border-cyan-200', iconColor: 'text-cyan-600' },
        { icon: FileText, label: 'Create Invoice', color: 'bg-violet-50 text-violet-700 hover:bg-violet-100 border-violet-200', iconColor: 'text-violet-600' },
        { icon: Plus, label: 'Add Product', color: 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200', iconColor: 'text-emerald-600' },
    ];

    return (
        <div className="space-y-6">
            <div className="bg-white rounded-xl border border-slate-200 p-5">
                <h2 className="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <Clock className="w-4 h-4 text-amber-500" /> Quick Actions
                </h2>
                <div className="grid grid-cols-1 gap-2">
                    {actions.map((a) => (
                        <button key={a.label}
                            className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border transition ${a.color}`}>
                            <a.icon className={`w-5 h-5 ${a.iconColor}`} />
                            {a.label}
                        </button>
                    ))}
                </div>
            </div>
            <div className="bg-white rounded-xl border border-slate-200 p-5">
                <h2 className="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <Users className="w-4 h-4 text-slate-500" /> Team Online
                </h2>
                <div className="flex -space-x-2">
                    {['SC', 'MR', 'AP', 'JK', 'ET'].map((init, i) => (
                        <div key={i}
                            className={`w-9 h-9 rounded-full border-2 border-white flex items-center justify-center text-xs font-bold ${
                                ['bg-cyan-500', 'bg-violet-500', 'bg-emerald-500', 'bg-amber-500', 'bg-pink-500'][i]
                            } text-white`}>
                            {init}
                        </div>
                    ))}
                    <div className="w-9 h-9 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-xs font-medium text-slate-500">+8</div>
                </div>
            </div>
        </div>
    );
}

function AttendanceTable() {
    const statusBadge = (status) => {
        const map = {
            'on-time': 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'late': 'bg-amber-50 text-amber-700 border-amber-200',
            'absent': 'bg-red-50 text-red-700 border-red-200',
        };
        return map[status] || 'bg-slate-50 text-slate-600 border-slate-200';
    };

    return (
        <div className="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 className="font-semibold text-slate-800 flex items-center gap-2">
                    <Clock className="w-5 h-5 text-cyan-500" /> Live Attendance
                </h2>
                <button className="text-xs text-cyan-600 hover:text-cyan-700 font-medium">View All</button>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="text-[10px] uppercase tracking-wider text-slate-500 font-semibold bg-slate-50">
                            <th className="px-5 py-3">Employee</th>
                            <th className="px-5 py-3">Department</th>
                            <th className="px-5 py-3">Check-in</th>
                            <th className="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-50">
                        {attendanceData.map((row) => (
                            <tr key={row.name} className="hover:bg-slate-50 transition">
                                <td className="px-5 py-3.5">
                                    <div className="flex items-center gap-3">
                                        <div className="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">
                                            {row.name.split(' ').map(n => n[0]).join('')}
                                        </div>
                                        <span className="text-sm font-medium text-slate-800">{row.name}</span>
                                    </div>
                                </td>
                                <td className="px-5 py-3.5 text-sm text-slate-500">{row.dept}</td>
                                <td className="px-5 py-3.5 text-sm text-slate-600 font-medium">{row.time}</td>
                                <td className="px-5 py-3.5">
                                    <span className={`inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border capitalize ${statusBadge(row.status)}`}>
                                        {row.status === 'on-time' ? 'On Time' : row.status === 'late' ? 'Late' : 'Absent'}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
