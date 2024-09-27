<table>
	<thead>
		<tr>
			<th><strong>ID</strong></th>
			<th><strong>รหัสพนักงาน</strong></th>
			<th><strong>ชื่อ-สกุล</strong></th>
			<th><strong>สิทธิ์ผู้ใช้งาน</strong></th>
			<th><strong>แต้มคะแนน</strong></th>
			<th><strong>User Group</strong></th>
			@if(!is_blank($user_info_template))
				@foreach ($user_info_template as $key => $value)
				<th><strong>{{$value['label_en']}}</strong></th>
				@endforeach
			@endif
	   	</tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
			<td>{{$loop->iteration}}</td>
     		<td>{{ $rs->user->member_id ?? '' }}</td>
     		<td>{{ $rs->user->name }}</td>
     		<td>{{$rs->role_name?? ''}}</td>
     		<td>{{ $rs->count }}</td>
			 <td>{{ $rs->user_groups_name }}</td>
			 @if(!is_blank($user_info_template))
			 @foreach ($user_info_template as $key => $value)
			 <!-- check data_info => user_org_unit_id  KM 27/09/2022 -->
			 @if($value['key'] == 'user_org_unit_id')
				 @foreach ($all_org_units as $division)
					 @if(!empty($rs->data_info[$value['key']]))
						 @if($division->id ==  $rs->data_info[$value['key']])
						 <td>{{$division->title ?? ''}}</td>
						 @endif
					 @endif
				 @endforeach
			 @else
				 <td>{{$rs->data_info[$value['key']] ?? ''}}</td>
			 @endif
			 @endforeach
		 @endif
		</tr>
    	@endforeach
    </tbody>
</table>