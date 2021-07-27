@extends('master.master')

@section('content')
<div class="container">
    <h3>Data</h3>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewData"> Create New Data</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Title</th>
                <th>Email</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Status</th>
                <th width="300px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="dataForm" name="dataForm" class="form-horizontal">
                   <input type="hidden" name="data_id" id="data_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="" maxlength="150" required="true">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-12">
                        <input type="text" id="email" name="email" required="" placeholder="Enter email" maxlength="150" class="form-control" required="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" required="" placeholder="Enter description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cost</label>
                        <div class="col-sm-12">
                        <input type="number" id="cost" name="cost" required="" placeholder="Enter cost" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-12">
                        <input type="text" id="status" value=0 name="status" required="" placeholder="Enter status" maxlength="1" class="form-control">
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                     </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
@section('js')
<script type="text/javascript">
  $(function () {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('data.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'title', name: 'title'},
            {data: 'email', name: 'email'},
            {data: 'description', name: 'description'},
            {data: 'cost', name: 'cost'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    $('#createNewData').click(function () {
        $('#saveBtn').val("create-data");
        $('#data_id').val('');
        $('#dataForm').trigger("reset");
        $('#modelHeading').html("Create New Data");
        $('#ajaxModel').modal('show');
    });
    $('body').on('click', '.editData', function () {
      var data_id = $(this).data('id');
      $.get("{{ route('data.index') }}" +'/' + data_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Data");
          $('#saveBtn').val("edit-data");
          $('#ajaxModel').modal('show');
          $('#data_id').val(data.id);
          $('#title').val(data.title);
          $('#email').val(data.email);
          $('#description').val(data.description);
          $('#cost').val(data.cost);
          $('#status').val(data.status);
      })
   });
    $('#saveBtn').click(function (e) {
        if(!$('#title').val() ){
            alert("Title Can not Empty");
            return false;
        }
        if(!$('#email').val()){
            alert("Email Can not Empty");
            return false;
        }
        e.preventDefault();
        $(this).html('Save');

        $.ajax({
          data: $('#dataForm').serialize(),
          url: "{{ route('data.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

            if(data.success){
                console.log('Success:', data);
                alert(Object.values(data));
                $('#dataForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                table.draw();
            }else{
                console.log('Error:', data);
                alert("Erro: "+ Object.values(data));
                ('#saveBtn').html('Save Changes');
            }

          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });

    $('body').on('click', '.deleteData', function () {

        var data_id = $(this).data("id");
        $confirm = confirm("Are You sure want to delete !");
        if($confirm == true ){
            $.ajax({
                type: "DELETE",
                url: "{{ route('data.store') }}"+'/'+data_id,
                success: function (data) {
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    });

  });
</script>
@endsection
