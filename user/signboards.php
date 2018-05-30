<?php
$userrole = 'Standard User';
$title = 'Send message to Signboards';
require '../login/misc/pagehead.php';
$uid = $_SESSION['uid'];
$usr = UserHandler::pullUserById($uid);
?>
<script src="../login/js/additional-methods.min.js"></script>
<link rel="stylesheet" type="text/css" href="../login/js/DataTables/datatables.min.css"/>
<script type="text/javascript" src="../login/js/DataTables/datatables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<script src="http://bootboxjs.com/bootbox.js"></script>
</head>
<body>
  <?php require 'login/misc/pullnav.php'; ?>
    <div class="container">
        <div class="col-sm-3"></div>
        
        
        <div class="col-sm-6">
            <h3><?php echo $title;?></h3>
            
            
            
   <form id="messageform" name="messageform" enctype="multipart/form-data">
    <div class="form-group row">
      <div class="col-xs-8">
        <label for="message" class="label label-default">Message</label>
        <input class="form-control" id="message" name="message" type="text" required>
      </div>
      <div class="col-xs-3">
      	  <label for="minutes" class="label label-default">Minutes to Scroll</label>
      	  <input type="number" class="form-control" id="minutes" name="minutes" min="1" step="1" data-bind="value:minutes" required />
		</div>
	  	</div>
       
       
       
       
       <span class="label label-success">Select signboards message will be sent to</span>
              
            <div class="row">
        <table id="signboards" class="table table-sm">
          <thead>
            <tr>
            <th></th>
            <th>Serial #</th>
            <th>Location</th>
            </tr>
          </thead>
	<tbody>
	<tr>
	   <td class="select-checkbox"></td>
	   <td>1001</td>
	   <td>Facility 1 - Poolhouse (this is customer defined)</td>
	</tr>
	<tr>
	   <td class="select-checkbox"></td>
	   <td>1002</td>
	   <td>Facility 1 - Lobby (this is customer defined)</td>
	</tr>
	<tr>
	   <td class="select-checkbox"></td>
	   <td>1003</td>
	   <td>Facility 2 - Gym (this is customer defined)</td>
	</tr>
	</tbody>
        </table>
      </div>

                
                  <br/>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="message"></div>
                            <button type="button" class="btn btn-primary" id="submitbtn" name="submitbtn">Send Message</button>
                        </div>
                    </div>
            </form>
            </div>
        </div>
    
       
        <div class="col-sm-3"></div>   
</div>
    
 <script type="application/javascript">
 $(document).ready(function() {
  //var rows = $('tr.immediate');
  var table = $('#signboards').DataTable({
   //scrollY:        300,
   //scrollX:        true,
   //scrollCollapse: true,
   "searching": false,
   paging: false,
   fixedColumns: {
    leftColumns: 1
   },
   columnDefs: [{
    orderable: false,
    className: 'select-checkbox',
    targets: 0,
    checkboxes: {
     selectRow: true
    }

   }],


   select: {
    style: 'multi',
    selector: 'td:first-child'
   },
   order: [
    [1, 'asc']
   ]
  });




  // Handle form submission event
  $('#submitbtn').on('click', function(e) {
   //event.preventDefault(); //prevent default action
   //var $validator = $("#messageform").validate();
   var form = $('#messageform');
   var api = $('#signboards').DataTable();
   var formarray = $('#messageform').serializeArray();

   var dataTableRows = api.rows({
    selected: true
   }).data().toArray();
   var arrTableSelectedRowsRendered = [];
   for (var i = 0; i < dataTableRows.length; i++) {
    dataTableRows[i] = dataTableRows[i].slice(1, dataTableRows[i].length);
    arrTableSelectedRowsRendered.push(dataTableRows[i].slice(0, dataTableRows[i].length - 1));
   }

   var str2 = String(arrTableSelectedRowsRendered);
   formarray.push({
    name: 'serial',
    value: str2
   });

   if (arrTableSelectedRowsRendered.length < 1) {
    $.alert('No signboards selected', 'Error');
   } else {


    bootbox.confirm("Send Message?", function(result) {

     if (result) {

      $.ajax({
       type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
       url: 'ajax/sendmessage2.php', // the url where we want to POST
       data: formarray, // our data object
       dataType: 'json', // what type of data do we expect back from the server
       encode: true
      })

      // using the done promise callback
      .done(function(data) {

       // log data to the console so we can see
       console.log(data);

       // here we will handle errors and validation messages
       if (!data.success) {
          if (data.errors.message) {
              $.alert({
                title: 'Error!',
                content: data.errors.message,
                theme: 'material',
                type: 'red',
                typeAnimated: true,
              });
            }
          if (data.errors.minutes) {
              $.alert({
                  title: 'Error!',
                  content: data.errors.minutes,
                  theme: 'material',
                  type: 'red',
                  typeAnimated: true,
              });
            }
          if (data.errors.serial) {
              $.alert({
                  title: 'Error!',
                  content: data.errors.serial,
                  theme: 'material',
                  type: 'red',
                  typeAnimated: true,
              });
            }
       } else {
        // ALL GOOD! just show the success message!
            $.alert({
                  title: 'Success!',
                  content: 'Message Sent',
                  theme: 'material',
                  type: 'green',
                  typeAnimated: true,
              });
          $("#messageform").trigger('reset');
       }

      }); //end ajax done
     }
    });
   }

   console.log(formarray);
   console.log(arrTableSelectedRowsRendered);
  });
 });

</script>
</body>
</html>

