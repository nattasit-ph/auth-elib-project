<table>
	<thead>
		<tr>
			<th><strong>ชื่อห้อง</strong></th>
			<th><strong>รายละเอียด</strong></th>
            <th><strong>สิ่งอำนวยความสะดวก</strong></th>
			<th><strong>จำนวนที่นั่ง</strong></th>
			<th><strong>เวลาเปิด</strong></th>
			<th><strong>เวลาปิด</strong></th>
			<th><strong>สถานะ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
        @isset($rs->facilities)
        @php $facilities = ""; @endphp
            @foreach($rs->facilities as $item)
                @if(!empty($item))
                @php $facilities = $facilities.$item.","; @endphp       
                @endif
            @endforeach
        @endisset
     	<tr>
     		<td>{{ $rs->title }}</td>
     		<td>{!! $rs->description ?? '' !!}</td>
            <td> {{ $facilities == '' ? '-' : $facilities}}</td>
     		<td>{{ $rs->max_seats ?? '' }}</td>
			<td>{{ $rs->open_time ?? '00:00' }}</td>
			<td>{{ $rs->closed_time ?? '23:59' }}</td>
  			<td>{{ $rs->status == 1 ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>