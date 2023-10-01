@extends('layouts.admin')
@section('content')
    <div class="col-12 p-3">
        <div class="col-12 col-lg-12 p-0 main-box">

            <div class="col-12 px-0">
                <div class="col-12 p-0 row">
                    <div class="col-12 col-lg-4 py-3 px-3">
                        <span class="fas fa-donates"></span> المقالات
                    </div>
                    <div class="col-12 col-lg-4 p-0">
                    </div>
                </div>
                <div class="col-12 divider" style="min-height: 2px;"></div>
            </div>

            <div class="col-12 py-2 px-2 row">
                <div class="col-12 col-lg-4 p-2">
                    <form method="GET">
                        <input type="text" name="q" class="form-control" placeholder="بحث ... "
                            value="{{ request()->get('q') }}">
                    </form>
                </div>
            </div>
            <div class="col-12 p-3" style="overflow:auto">
                <div class="col-12 p-0" style="min-width:1100px;">


                    <table class="table table-bordered  table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المستخدم</th>
                                <th>القيمة</th>
                                <th>البرنامج</th>
                                <th>تحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donates as $donate)
                                <tr>
                                    <td>{{ $donate->id }}</td>
                                    <td><a href="{{ route('admin.donates.index.donor', $donate->donor) }}">
                                            {{ $donate->donor->user->name }} </a></td>
                                    <td>{{ $donate->value }}</td>
                                    <td>{{ $donate->program->name }}</td>
                                    <td style="width: 360px;">
                                        @can('donates-read')
                                            <a href="{{ route('admin.donates.show', ['donate' => $donate]) }}">
                                                <span class="btn  btn-outline-primary btn-sm font-1 mx-1">
                                                    <span class="fas fa-search "></span> عرض
                                                </span>
                                            </a>
                                        @endcan
                                        @can('donates-update')
                                            <a href="{{ route('admin.donates.edit', $donate) }}">
                                                <span class="btn  btn-outline-success btn-sm font-1 mx-1">
                                                    <span class="fas fa-wrench "></span> تحكم
                                                </span>
                                            </a>
                                        @endcan
                                        @can('donates-delete')
                                            <form method="POST" action="{{ route('admin.donates.destroy', $donate) }}"
                                                class="d-inline-block">@csrf @method('DELETE')
                                                <button class="btn  btn-outline-danger btn-sm font-1 mx-1"
                                                    onclick="var result = confirm('هل أنت متأكد من عملية الحذف ؟');if(result){}else{event.preventDefault()}">
                                                    <span class="fas fa-trash "></span> حذف
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 p-3">
                {{ $donates->appends(request()->query())->render() }}
            </div>
        </div>
    </div>
@endsection
