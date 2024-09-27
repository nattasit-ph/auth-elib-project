<table>
	<thead>
		<tr>
			<th><strong>ชื่อของรางวัล</strong></th>
         <th><strong>จำนวนของรางวัลที่แลก</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->reward_name ?? ''}}</td>
         <td>{{ $rs->redempt_qty ?? '0'}}</td>
		</tr>
    	@endforeach
    </tbody>
</table>