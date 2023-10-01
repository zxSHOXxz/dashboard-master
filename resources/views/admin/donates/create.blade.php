@extends('layouts.admin')
@section('content')
    <div class="col-12 p-3">
        <div class="col-12 col-lg-12 p-0 ">
            <form id="validate-form" class="row" enctype="multipart/form-data" method="POST"
                action="{{ route('admin.donates.store') }}">
                @csrf
                <input type="hidden" name="temp_file_selector" id="temp_file_selector" value="{{ uniqid() }}">
                <div class="col-12 col-lg-8 p-0 main-box">
                    <div class="col-12 px-0">
                        <div class="col-12 px-3 py-3">
                            <span class="fas fa-info-circle"></span> إضافة جديد
                        </div>
                        <div class="col-12 divider" style="min-height: 2px;"></div>
                    </div>
                    <div class="col-12 p-3 row">
                        <div class="col-12 col-lg-6 p-2">
                            <div class="col-12">
                                البرنامج
                            </div>
                            <div class="col-12 pt-3">
                                <select class="form-control select2-select" name="program_id" required size="1"
                                    style="height:30px;opacity: 0;">
                                    @foreach ($programs as $programme)
                                        <option value="{{ $programme->id }}"
                                            @if (old('program_id') == $programme->id) selected @endif>{{ $programme->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 p-2">
                            <div class="col-12">
                                القيمة
                            </div>
                            <div class="col-12 pt-3">
                                <input type="number" name="value" required maxlength="1" class="form-control"
                                    value="{{ old('value') }}">
                            </div>
                        </div>
                        <input type="text" hidden value="1" name="donor_id">
                    </div>
                </div>
                <div class="col-12 p-3">
                    <button class="btn btn-success" id="submitEvaluation">حفظ</button>
                </div>
            </form>
        </div>
    </div>
@endsection
