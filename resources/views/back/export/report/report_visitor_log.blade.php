<table>
	<thead>
		<tr>
			<th><strong>URL</strong></th>
			<th><strong>IP Address</strong></th>
			<th><strong>Created Date</strong></th>
			<th><strong>Browser</strong></th>
			<th><strong>Device</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
			<?php
					$browser = 'Unknown';
					if (preg_match('/Firefox/i', $rs->browser)) $browser = 'Firefox';
					elseif (preg_match('/Mac/i', $rs->browser)) $browser = 'Mac';
					elseif (preg_match('/Chrome/i', $rs->browser)) $browser = 'Chrome';
					elseif (preg_match('/Opera/i', $rs->browser)) $browser = 'Opera';
					elseif (preg_match('/MSIE/i', $rs->browser)) $browser = 'IE'; 
			?>
     	<tr>
     		<td>{{ $rs->browser_detail }}</td>
     		<td>{{ $rs->ip }}</td>
     		<td>{{ $rs->created_date }}</td>
     		<td>{{ $browser }}</td>
			<td>{{ $rs->device }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>