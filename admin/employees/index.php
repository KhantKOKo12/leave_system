<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title" style="padding-top: 5px;">List of Employees</h3>
		<div class="card-tools">
			<a href="?page=employees/manage_employee" class="btn" style="background: #4A9B82;border-radius: 6px;color:#FFFFFF;"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hover table-stripped">
				<colgroup>
					<col width="10%">
					<col width="15%">
					
					<col width="20%">
					<col width="30%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Details</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$department_qry = $conn->query("SELECT id,name FROM department_list");
					$dept_arr = array_column($department_qry->fetch_all(MYSQLI_ASSOC),'name','id');
					$designation_qry = $conn->query("SELECT id,name FROM designation_list");
					$desg_arr = array_column($designation_qry->fetch_all(MYSQLI_ASSOC),'name','id');
						$qry = $conn->query("SELECT *,concat(firstname,' ',middlename,' ',lastname) as name from `users` where `type` = '3'  order by concat(firstname,' ',middlename,' ',lastname) asc ");
						while($row = $qry->fetch_assoc()):
							$meta_qry = $conn->query("SELECT * FROM employee_meta where user_id = '{$row['id']}' ");
							while($mrow = $meta_qry->fetch_assoc()){
								$row[$mrow['meta_field']] = $mrow['meta_value'];
							}
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class="text-center"><img src="<?php echo validate_image($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
							
							<td><?php echo ucwords($row['name']) ?></td>
							<td >
								<p class="m-0 ">
									<b>Department: </b><?php echo isset($dept_arr[$row['department_id']]) ? $dept_arr[$row['department_id']] : 'N/A' ?><br>
									<b>Designation: </b><?php echo isset($desg_arr[$row['designation_id']]) ? $desg_arr[$row['designation_id']] : 'N/A' ?><br>
								</p>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-sm dropdown-toggle dropdown-icon" style="background: #4A9B82;border-radius: 6px;color: #fff;" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
								  	<a class="dropdown-item" href="?page=employees/records&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-secodary"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item" href="?page=employees/manage_employee&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Employee permanently?","delete_user",[$(this).attr('data-id')])
		})
		
		var table = $('.table').DataTable({
        // Specify your column index for "Name" and "Details" here
        "columnDefs": [
            {
                "targets": [0, 1, 4], // Assuming column indices for #, Avatar, Action
                "searchable": false
            },
            {
                "targets": [2, 3], // Assuming column indices for Name and Details
                "searchable": true
            }
        ]
    });

    // Custom search function to ignore spaces
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var searchValue = $.fn.dataTable.ext.search[0].searchValue || '';
            var columnsToSearch = [2, 3]; // Adjust column indices as necessary

            // Normalize search value by removing spaces
            searchValue = searchValue.replace(/\s+/g, '').toLowerCase();

            // Normalize data for the columns we're searching
            for (var i = 0; i < columnsToSearch.length; i++) {
                var columnData = data[columnsToSearch[i]].replace(/\s+/g, '').toLowerCase();
                if (columnData.indexOf(searchValue) !== -1) {
                    return true;
                }
            }

            return false;
        }
    );

    // Search input event handler
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });


	})
	function delete_user($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	
</script>