<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الحضور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">نظام الحضور</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>سجل الحضور والانصراف</h2>
        
        <div class="table-responsive">
            <table class="table table-striped" id="attendanceTable">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>وقت الحضور</th>
                        <th>وقت الانصراف</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('Y-m-d') }}</td>
                        <td>{{ $attendance->check_in ? date('H:i', strtotime($attendance->check_in)) : '-' }}</td>
                        <td>{{ $attendance->check_out ? date('H:i', strtotime($attendance->check_out)) : '-' }}</td>
                        <td>
                            @if($attendance->check_in && $attendance->check_out)
                                @php
                                    $start = \Carbon\Carbon::parse($attendance->check_in);
                                    $end = \Carbon\Carbon::parse($attendance->check_out);
                                    echo $start->diff($end)->format('%h ساعات %i دقائق');
                                @endphp
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($attendance->status == 'completed')
                                <span class="badge bg-success">مكتمل</span>
                            @else
                                <span class="badge bg-warning">قيد العمل</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $attendances->links() }}
        </div>
        
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">العودة للرئيسية</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#attendanceTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json'
                },
                order: [[0, 'desc']]
            });
        });
    </script>
</body>
</html>