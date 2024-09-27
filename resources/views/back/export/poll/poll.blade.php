<table>
	<thead>
		<tr>
			<th><strong>คำถาม</strong></th>
			<th><strong>วันที่เริ่มต้น</strong></th>
			<th><strong>วันที่สิ้นสุด</strong></th>
			<th><strong>จำนวนคำตอบ</strong></th>
			<th><strong>จำนวนคะแนนโหวต</strong></th>
			<th><strong>สถานะ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->question }}</td>
     		<td>{{ $rs->poll_start_date }}</td>
     		<td>{{ $rs->poll_end_date }}</td>
     		<td>{{ $rs->total_options }}</td>
     		<td>{{ $rs->total_votes }}</td>
     		<td>{{ $rs->status == 1 ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</td>
     		@if (isset($rs->pollOptions) && $rs->pollOptions->count() > 0)
     		@foreach ($rs->pollOptions as $option)
     			<td>{{ $option->title }}</td>
     			<td>{{ isset($options_with_total[$option->id]->total) ? $options_with_total[$option->id]->total : 0 }}</td>
     		@endforeach
     		@endif
		</tr>
    	@endforeach
    </tbody>
</table>