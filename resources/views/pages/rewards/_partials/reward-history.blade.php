@forelse($redemptions as $idx => $r)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $r->reward->name ?? '-' }}</td>
        <td>{{ $r->points_cost }}</td>
        <td>
            @if($r->status == 'pending')
                <span class="badge bg-warning">Pending</span>
            @elseif($r->status == 'processed')
                <span class="badge bg-info">Diproses</span>
            @elseif($r->status == 'completed')
                <span class="badge bg-success">Selesai</span>
            @elseif($r->status == 'rejected')
                <span class="badge bg-danger">Ditolak</span>
            @else
                <span class="badge bg-secondary">{{ $r->status }}</span>
            @endif
        </td>
        <td style="max-width:260px; white-space:normal; word-wrap:break-word;">{{ $r->admin_notes ? $r->admin_notes : '-' }}</td>
        <td>{{ $r->created_at->format('d M Y H:i') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5">Belum ada riwayat penukaran.</td>
    </tr>
@endforelse
