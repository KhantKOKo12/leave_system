<style>
.modal-header {
    background: #4A9B82;
    color: #ffffff;
}
</style>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title" style="padding-top: 5px;">List of Department</h3>
        <?php if($_settings->userdata('type') == 1): ?>
        <div class="card-tools">
            <a href="javascript:void(0)" class="btn" style="background: #4A9B82;border-radius: 6px;color: #FFFFFF;"
                id="create_new"><span class="fas fa-plus"></span> Create New</a>
        </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-stripped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Date Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `department_list` order by unix_timestamp(date_updated) desc, unix_timestamp(date_created) desc ");
						while($row = $qry->fetch_assoc()):
                            $row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
					?>
                        <tr title="<?php echo $row['description'] ?>">
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td><?php echo $row['name'] ?></td>
                            <td>
                                <p class="truncate m-0"><?php echo $row['description'] ?></p>
                            </td>
                            <td><?php echo ($row['date_updated'] != null) ? date('Y-m-d H:i',strtotime($row['date_updated'])) : date('Y-m-d H:i',strtotime($row['date_created'])); ?>
                            </td>
                            <td align="center">
                                <button type="button" class="btn dropdown-toggle dropdown-icon"
                                    style="background: #4A9B82;border-radius: 6px;color: #fff;" data-toggle="dropdown">
                                    Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item edit_data" href="javascript:void(0)"
                                        data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span>
                                        Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)"
                                        data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span>
                                        Delete</a>
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
$(document).ready(function() {
    $('.delete_data').click(function() {
        _conf("Are you sure to delete this Department permanently?", "delete_department", [$(this).attr(
            'data-id')])
    })
    $('.edit_data').click(function() {
        uni_modal("<i class='fa fa-edit'></i> Edit Department Details",
            'maintenance/manage_department.php?id=' + $(this).attr('data-id'))
    })
    $('#create_new').click(function() {
        uni_modal("<i class='fa fa-plus'></i> Create New Department",
            'maintenance/manage_department.php')
    })
    $('.table').dataTable();

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var searchTerm = $('.dataTables_filter input').val().toLowerCase().replace(/\s+/g, '');
            var department = data[1]; // Adjust index
            if (department === undefined || department === null) {
                department = '';
            }
            department = department.toLowerCase().replace(/\s+/g, '');
            return department.indexOf(searchTerm) !== -1;
        }
    );

})

function delete_department($id) {
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_department",
        method: "POST",
        data: {
            id: $id
        },
        dataType: "json",
        error: err => {
            console.log(err)
            alert_toast("An error occured.", 'error');
            end_loader();
        },
        success: function(resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                location.reload();
            } else {
                alert_toast("An error occured.", 'error');
                end_loader();
            }
        }
    })
}
</script>