@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-history me-2"></i>سجل الحضور
                    </h3>
                </div>
                
                <div class="card-body">
                    @if($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>وقت الحضور</th>
                                        <th>الطريقة</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}</td>
                                        <td>
                                            @if($attendance->check_in)
                                                {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}
                                            @else
                                                <span class="text-danger">لم يسجل</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $attendance->method }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($attendance->status == 'present')
                                                <span class="badge bg-success">حاضر</span>
                                            @else
                                                <span class="badge bg-warning">{{ $attendance->status }}</span>
                                            @endif
                                        </td>
                                      
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- الترقيم -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $attendances->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>لا توجد سجلات حضور حتى الآن
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection