<table>
	<thead>
		<tr>
			<th><strong>วันที่ส่งแบบฟอร์ม</strong></th>
			<th><strong>รหัสพนักงาน</strong></th>
			<th><strong>ชื่อ-สกุลผู้ส่ง</strong></th>
			<th><strong>Email</strong></th>
			@foreach ($form_fields as $field)
				<th><strong>{{ $field->label }}</strong></th>
			@endforeach
			{{-- <th><strong>สถานะแบบฟอร์ม</strong></th> --}}
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->created_date }}</td>
     		<td>{{ $rs->creator->member_id ?? '' }}</td>
     		<td>{{ $rs->creator->name ?? '' }}</td>
     		<td>{{ $rs->creator->email ?? '' }}</td>
			@if(!is_null($rs->data_fields))
     			@foreach ($rs->data_fields as $k=>$data)
     				<td>{{ is_array($data) ? implode(',', $data) : $data }}</td>
     			@endforeach
			@endif
     		{{-- 
  			<td>
  				@switch ($rs->status)
  					@case (0)
  						รอการอนุมัติ
  						@break
  					@case (1)
  						กำลังดำเนินการ
  						@break
  					@case (2)
  						ดำเนินการเรียบร้อย
  						@break
  				@endswitch
  			</td>
  			--}}
		</tr>
    	@endforeach
    </tbody>
</table>