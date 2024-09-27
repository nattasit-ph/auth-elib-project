<input type="hidden" id="room_type_id" name="room_type_id" value="{{$room_type->id ?? ''}}">
<div class="kt-portlet">
    <div class="kt-portlet__body kt-portlet__body--fit">
        <div class="kt-portlet__body">
            
            <div class="form-group  col-12 col-md-12">
                <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>ประเภทห้อง:</label>
                <input id="title" name="title" type="text" class="form-control required" placeholder="Enter title" value="{{$room_type->title ?? '' }}" autocomplete="off" required>
            </div>

            <div class="form-group  col-12 col-md-12">
                <label>คำอธิบายเพิ่มเติม:</label>
                <input id="description" name="description" type="text" class="form-control" placeholder="Enter Description" value="{{$room_type->description ?? '' }}" autocomplete="off">
            </div>

            <div class="form-group  col-12 col-md-6">
                <label>ลำดับที่แสดงผล:</label>
                <input id="weight" name="weight" type="number" class="form-control" placeholder="e.g. 10, 20, 30" value="{{$room_type->weight ?? '' }}" autocomplete="off">
                <div class="my-3">
                    <span class="form-text text-muted">แสดงผลเรียงลำดับจากน้อยไปหามาก</span>
                </div>
            </div>

            <div class="form-group">
                <label>สถานะ:</label>
                <div class="kt-radio-inline">
                    <label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
                        <input type="radio" name="status" value="1" checked=""> Active
                        <span></span>
                    </label>
                    <label class="kt-radio kt-radio--bold kt-radio--brand">
                        @if (isset($room_type))
                        <input type="radio" name="status" value="0" {{ $room_type->status == '0' ? 'checked' : '' }}> Inactive
                        @else
                        <input type="radio" name="status" value="0" checked=""> Inactive
                        @endif
                        <span></span>
                    </label>
                </div>
            </div>

        </div>
    </div>
</div>


