@props([
    'status' => 'active',
    'size' => 'md'
])

@php
$normalized = strtolower(trim((string)$status));

$badgeConfig = match($normalized) {
    'active', 'aktif', 'approved', 'disetujui', 'hadir', 'present' => [
        'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dot' => 'bg-emerald-500',
        'label' => $status === 'active' ? 'Aktif' : ($status === 'present' ? 'Hadir' : ($status === 'approved' ? 'Disetujui' : ucfirst($status)))
    ],
    'pending', 'menunggu', 'tertunda' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        'dot' => 'bg-amber-500',
        'label' => $status === 'pending' ? 'Pending' : ucfirst($status)
    ],
    'revision', 'revisi' => [
        'bg' => 'bg-sky-50 text-sky-700 border-sky-200',
        'dot' => 'bg-sky-500',
        'label' => 'Revisi'
    ],
    'rejected', 'ditolak', 'terminated', 'nonaktif' => [
        'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
        'dot' => 'bg-rose-500',
        'label' => $status === 'rejected' ? 'Ditolak' : ($status === 'terminated' ? 'Nonaktif' : ucfirst($status))
    ],
    'completed', 'selesai' => [
        'bg' => 'bg-blue-50 text-[#005B9C] border-blue-200',
        'dot' => 'bg-[#005B9C]',
        'label' => 'Selesai Magang'
    ],
    'wfo' => [
        'bg' => 'bg-blue-50 text-[#005B9C] border-blue-200',
        'dot' => 'bg-[#005B9C]',
        'label' => 'WFO (Kantor)'
    ],
    'wfh' => [
        'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'dot' => 'bg-indigo-500',
        'label' => 'WFH (Remote)'
    ],
    'izin', 'permission' => [
        'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'dot' => 'bg-cyan-500',
        'label' => 'Izin'
    ],
    'sakit', 'sick' => [
        'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
        'dot' => 'bg-rose-500',
        'label' => 'Sakit'
    ],
    'late', 'terlambat' => [
        'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
        'dot' => 'bg-amber-500',
        'label' => 'Terlambat'
    ],
    default => [
        'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
        'dot' => 'bg-slate-400',
        'label' => ucfirst($status)
    ]
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider {$badgeConfig['bg']}" ]) }}>
    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $badgeConfig['dot'] }}"></span>
    <span>{{ $badgeConfig['label'] }}</span>
</span>
