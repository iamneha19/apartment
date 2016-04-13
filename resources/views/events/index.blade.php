 @extends('layouts.default') @section('panel_title')Events @stop
@section('panel_subtitle','Calendar') @section('head')

<link href="{{asset('css/fullcalendar.css')}}" rel='stylesheet' />
<link href="{{asset('css/fullcalendar.print.css')}}" rel='stylesheet'
	media='print' />
<script src="{{asset('js/moment.min.js')}}"></script>
<script src="{{asset('js/fullcalendar.min.js')}}"></script>
<link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}"
	rel="stylesheet" type="text/css">
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script>
    var cal;
    $( document ).ready(function() {
        $('#loader').hide();
        
    cal = $('#calendar').fullCalendar({

    

        events: function(start, end, timezone, callback) {
            $.ajax({
            	data: {
						 month:end.month(),
						 year:end.year(),
                        },
                url: generateUrl('event/list'),
                success: function(d) {
                    callback(d.response);
                 }
            });
        },
            editable: true,
            buttonText:{
                today:    'Today'
            },
        eventClick: function(calEvent, jsEvent, view)
            {
              var r=confirm("Do you want to delete event " + calEvent.title + " ? ");
              if (r===true)
                {
                   
                   var event_idd = calEvent._id;
					console.log(event_idd);

                            $.ajax({
                                            type: "POST",
                                            //url: generateUrl('event/delete'+ calEvent._id),
                                        url: generateUrl('event/delete'+'/'+calEvent._id),
                                            success: function(response){
                                                var result = response.response;
                                                
                                                if(result.success)
                                            	{$('#calendar').fullCalendar('removeEvents', calEvent._id);
                                            	grit("",result.msg)
                                            	}else{
                                            		grit("",result.msg)
                                                	}
                                            },
                                    });

                }
            },

            

            eventDrop: function(event, delta, revertFunc) {

                    var dNow = new Date();

                    //var utcdate= (dNow.getMonth()+ 1) + '/' + dNow.getDate() + '/' + dNow.getFullYear();
                    //var utcdate= (dNow.getFullYear() + '/' + (dNow.getMonth()+ 1) + '/' + dNow.getDate();
                    //alert(utcdate);
                    //alert(event.start.format());


                     var $today = new Date();
                     var $yesterday = new Date($today);
                     $yesterday.setDate($today.getDate() - 1);

                if (event.start < $yesterday) 
                        revertFunc();
                    else
                    {
                        
                            var $data = {
                                            id:event.id,
                                            name:event.title,
                                            date:event.start.format(),
                                                    };
                            //alert(event.start);
                            $.ajax({
                                      type: "POST",
                                      url: generateUrl('event/save'),
                                      data: $data,
                                      success: function(response){

                                          var result = response.response;
                                          
                                          if(result.success){
                                      		grit("",result.msg)
                                          }
                          					else{
                          					revertFunc();
                                      		//grit("",result.msg)
                                          	}

                                      },
                                    });
                    }
                //alert(event.id + event.title + " was dropped on " + event.start.format());

                //alert(event.title + " was dropped on " + event.start.format());

                    /*if (!confirm(event.title + " was dropped on " + event.start.format() + ". Are you sure about this change?")) {
                    revertFunc();
                } */


            }

    /*     eventClick: function(event, element) {
            event.title = "CLICKED!";
            $('#calendar').fullCalendar('updateEvent', event);
        } */




    });
/*     $('.fc-next-button').click(function(){
           $.ajax({
               url: generateUrl('event/list'),
               success: function(d) {
                   console.log(d.response);
               }
           });
    	}); */
    $('#eventdate').datetimepicker({

//            useCurrent : true,
        format: 'YYYY-MM-DD',
        minDate:moment(new Date()).format('YYYY-MM-DD'),
        ignoreReadonly : true,
        widgetPositioning: {
            horizontal: 'left',
            vertical:'bottom'
         }
    });

    $("#openSuggest").click(function(){
            $('#eventdate').val("");
    });



//    $("#buttonsuggest").click(function(){
//            console.log("Clicked");
//            if ($("#eventdate").val().length > 0 && $("#eventname").val().length > 0) {
//                    $('input[type="submit"], input[type="buttonsuggest"], button').disable(false);
//            }
//    });

    /* $('#myModal').on('hidden', function(){
        $(this).data('modal', null);
    });
     */
    //$("#myModal").remove();
    //$('.modal-backdrop').remove();

    });
    </script>

@stop @section('content')
<script>
    app.controller('CalendarControllerr',function($scope,$http){
//    $this.disable = false;
    $scope.data=null;
    $scope.events=null;
    $scope.event=null;
    $('#loader').hide();
    $('#myModal').on('hidden.bs.modal', function (e) {
            //alert("remove");
            $('#myModal').data('myModal', null);
            })

    //Disable function
    jQuery.fn.extend({
        disable: function(state) {
            return this.each(function() {
                this.disabled = state;
            });
        }
    });

        $scope.submit=function(){
            if($('#myform').valid())
            {
		$('#loader').show();		
                var month = $('#eventdate').val();
                console.log($('#eventdate').val());
                var date = moment();
                
                $('#calendar').fullCalendar( 'gotoDate', $.fullCalendar.moment(month));
                //$('#calendar').fullCalendar('gotoDate', date); 
                

            this.disable=true;
            //$('input[type="submit"], input[type="buttonsuggest"], button').disable(true);
            var $this=this;
            /*x = document.getElementById("eventname").value;
             alert(x); */
            /*  if(!document.getElementById('eventname').value.trim().length){
                    alert("Please enter name");
                    return;
                }
             else if(!('eventdate')||!document.getElementById('eventdate').value.trim().length){
                            alert("Please enter date");
                             return;
                     }
             else{ */
                            var $data = {
                                            name:this.name,
                                            date:$('#eventdate').val(),
                                                    };
                            $http.post(generateUrl('event/save'), $data).
                              then(function(response) {
                                        $('#loader').hide();
//                                      $('input[type="submit"], input[type="buttonsuggest"], button').disable(false);
                                      cal.fullCalendar("renderEvent",response.data.response.data);
                                      $('#myModal').modal('hide');
                                      $this.date="";
                                      $('#eventdate').val("");
                                      $this.name="";
                                      grit("","Event Saved!")
                                      $this.disable=false;
                                      $this.eventform.$setPristine(); 
                                      //grit("",response.data.response.msg)
                              });

                             //location.reload();
                //}
                }
                else{
                    console.log("validation error!");
                }
        }

    $scope.close=function(){
              //console.log(this);
              var $this=this;
              $('#myModal').modal('hide');
              $this.name="";
              $this.date="";
              $('#eventdate').val("");
              // 	$("#myModal")[0].reset();
          $("#myModal label.error").remove();
              $this.eventform.$setPristine(); 
    }

    $scope.update=function(){

            var $data = {
                            title:this.title,
                            id:this.id,		
                            text:CKEDITOR.instances.editor_start.getData()
                                    };
                 $('#loader').show();
            $http.post(generateUrl('event/update'), $data).
              then(function(response) {	
                  $('#loader').hide();
                      //console.log(response.data.response.msg);
                      //alert(response.data.response.msg);
                      //window.location = "http://localhost/apartment/public/index.php/admin/adminforum";
              });
        }


    /* $http.get(generateUrl('eve
                    nt/list')).
    then(function(response) {
        $scope.topics = response.data.response;
    }); */

    })

    </script>
<style>
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control
	{
	background-color: #fff;
	opacity: 1;
	}
</style>
<div class="col-md-12">
	<div class="col-md-12" style="margin-bottom: 20px;">
		<button type="button" id="openSuggest" class="btn btn-primary pull-right"
			data-toggle="modal" data-target="#myModal">Suggest Activity</button>
	</div>

	<!-- Modal -->
	<div ng-controller="CalendarControllerr" class="modal fade"
		id="myModal" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">


		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-label="Close"
						ng-click="close()">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Suggest Activity</h4>
				</div>
				<form ng-submit="submit()" class="modal-body" id="myform"
					name="eventform" novalidate>
					<div class="form-group">
						<label class="form-label" for="eventname">Event Name</label> <input
							name="event_name" ng-model="name"
							ng-model-options="{updateOn : 'blur'}" type="text"
							class="form-control" id="eventname" placeholder="Name"
							name="name">
						<!-- 						<div class="text-danger"
                                                            ng-if="eventform.$submitted && eventform.name.$invalid">Please
                                                            enter name</div> -->
					</div>
					<div class="form-group">
						<label class="form-label" for="eventdate">Date</label> <input
							name="event_date" ng-model="date" type="text"
							class="form-control" id="eventdate" placeholder="Date"
							name="date" readonly="readonly">
						<!-- <div class="text-danger"
                                                            ng-if="eventform.$submitted && eventform.date.$invalid">Please
                                                            select date</div> -->
					</div>
					<!--<div class="form-group">
                                                    <label for="eventnotes">Notes</label> <input type="text"
                                                            class="form-control" id="eventnotes" placeholder="Notes">
                                            </div> -->
					<div class="form-group">
						<button id="buttonsuggest" type="submit" class="btn btn-primary"
							ng-disabled="disable">@{{ disable ? 'Creating activity..' :
							'Submit' }}</button>
							
							<button type="button" class="btn btn-primary" ng-click="close()">Cancel</button>
					</div>
				</form>
<!-- 				<div class="modal-footer">
					
				</div> -->
                            <link href="{!! asset('css/loader.css') !!}" rel="stylesheet" type="text/css" />
                            <div id="loader" class="loading">Loading&#8230;</div>
			</div>
		</div>
	</div>
	<div id='calendar' class='col-md-12'></div>
</div>



<script>




    $(':input[type="text"]').change(function() {
        $(this).val($(this).val().trim());
    });
    $("#myform").validate({
        ignore: [], 
        rules: {
            event_name: {
                required: true,
                minlength: 0
              },
              event_date: "required",
        },
    /*                 errorPlacement: function(error, element) {
            if (element.attr("name") == "text"  ) {
              error.insertAfter("#cke_notice_desc");
            } else if (element.attr("name") == "type"  ) {
                $( ".form-group.type-radio-group" ).append( error );
            } else if (element.attr("name") == "status"  ) {
                $( ".publish-error" ).html( error );
            }else {
              error.insertAfter(element);
            }
        } */
    });
    </script>
@stop
