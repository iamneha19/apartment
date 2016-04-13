@section('title', 'Parking Configuration')
@section('panel_title', 'Parking Configuration')
@section('content')
    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDM1FfKl1JiAjQXLzyDYURJbZiF5XalZ_g"></script>
<script>
        app.controller("SocietyInfoCtrl", function($scope,$window,$http,$filter) {
            $scope.society;
            $scope.openCategory_data;
            $scope.stillCategory_data;
            $scope.garageCategory_data;
            $scope.singleCategory_data;
            $scope.multipleCategory_data;
//            console.log($scope.garageCategory_data);
             $scope.getSocietyInfo = function() {
                console.log(this);
                var request_url = generateUrl('society_info');
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.society = result.response.data;
                $scope.getCities($scope.society.state_id);
                }).error(function(data, status, headers, config) {
//                        console.log(data);
                });
            };
            $scope.getSocietyInfo();
            
            
            $scope.type = function() {
                var type = 'society';
                $http.get(generateUrl('v1/superadmin/list/typeList/'+type))
                .then(function(r){
                    $scope.types = r.data.results.data;
                });
            }
            
            $scope.type();
            
            $scope.getOpenCategory = function() {
                console.log(this);
                var request_url = generateUrl('v1/parking_config/data');
                  var records = $.param({id:1});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data;
                            $scope.openCategory_data = result.data;
                }, 
                        function(response) { // optional
//                            alert("fail");
                        });
//                });
            };
            $scope.getOpenCategory();
            
            $scope.getStillCategory = function() {
                console.log(this);
                var request_url = generateUrl('v1/parking_config/data');
                  var records = $.param({id:2});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data;
                            $scope.stillCategory_data = result.data;
                }, 
                        function(response) { // optional
//                            alert("fail");
                        });
//                });
            };
            $scope.getStillCategory();
            
            $scope.getGarageCategory = function() {
                console.log(this);
                var request_url = generateUrl('v1/parking_config/data');
                  var records = $.param({id:3});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data;
                            $scope.garageCategory_data = result.data;
                }, 
                        function(response) { // optional
//                            alert("fail");
                        });
//                });
            };
            $scope.getGarageCategory();
            
            $scope.getSingleCategory = function() {
                console.log(this);
                var request_url = generateUrl('v1/parking_config/data');
                  var records = $.param({id:5});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data;
                            $scope.singleCategory_data = result.data;
                            console.log($scope.singleCategory_data);
                }, 
                        function(response) { // optional
//                            alert("fail");
                        });
//                });
            };
            $scope.getSingleCategory();
            
            $scope.getMultipleCategory = function() {
                console.log(this);
                var request_url = generateUrl('v1/parking_config/data');
                  var records = $.param({id:6});
                        $http({
                            url: request_url,
                            method: "POST",
                            data:records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        }).then(function(response) {
                            var result = response.data;
                            $scope.multipleCategory_data = result.data;
                }, 
                        function(response) { // optional
//                            alert("fail");
                        });
//                });
            };
            $scope.getMultipleCategory();
            
            $('#society-form').submit(function(e){
                e.preventDefault();
                if ($("#society-form").valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Updating please wait..');
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('update/society_info');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#society-form").find('button[type=submit]').attr('disabled',false);
                        $("#society-form").find('button[type=submit]').text('Update');
                       var result = response.data.response; // to get api result
                       console.log(result);
                        if(result.success){
                              grit('',result.msg);
                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
          $scope.getStates = function() {
             var options = {orderby:'ASC'}
                var request_url = generateUrl('v1/states',options) + "&per_page=unlimited";
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.states = result.results;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
            $scope.getStates();
             $scope.getCities = function(stateId) {
                 
             var options = {orderby:'ASC'}
                var request_url = generateUrl('v1/cities',options) +"&state_id="+stateId+"&per_page=unlimited";
                  $http.get(request_url)
                .success(function(result, status, headers, config) {  
                        $scope.cities = result.results;
                }).error(function(data, status, headers, config) {
                        console.log(data);
                });
            };
             $scope.change = function(){
                console.log($scope.StateSelected); 
                $scope.getCities($scope.StateSelected);
            };
          
          jQuery.validator.addMethod("unique", function(value, element) {
            var unique_id =  $('#unique_id').val();
            if(unique_id!='')
            {
                if(unique_id != unique_id.match(/^[a-zA-Z0-9]+$/))
                {
                    return false;
              }else{
                     return true;
               }
            }else{
                return true;
            }
        
          });
          
          $('#open-category-form').submit(function(e){
                e.preventDefault();
                if ($("#open-category-form").valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Please wait..');
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('v1/parking_config/create');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#open-category-form").find('button[type=submit]').attr('disabled',false);
                        $("#open-category-form").find('button[type=submit]').text('Add');
                       var result = response.data; // to get api result
                       console.log(response);
                        if(result.success){
                            grit('',result.msg);
                            $('#open-category-form input').prop('readonly',true);
                            $("#open-category-form").find('button[type=submit]').attr('disabled',true);
//                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
            
            ///// still category form
            $('#still-category-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Please wait..');
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('v1/parking_config/create');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#still-category-form").find('button[type=submit]').attr('disabled',false);
                        $("#still-category-form").find('button[type=submit]').text('Add');
                       var result = response.data; // to get api result
                       console.log(response);
                        if(result.success){
                            grit('',result.msg);
                            $('#still-category-form input').prop('readonly',true);
                            $("#still-category-form").find('button[type=submit]').attr('disabled',true);
//                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
            
            ////garage category form
            $('#garage-category-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Please wait..');
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('v1/parking_config/create');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#garage-category-form").find('button[type=submit]').attr('disabled',false);
                        $("#garage-category-form").find('button[type=submit]').text('Add');
                       var result = response.data; // to get api result
                       console.log(response);
                        if(result.success){
                            grit('',result.msg);
                            $('#garage-category-form input').prop('readonly',true);
                            $("#garage-category-form").find('button[type=submit]').attr('disabled',true);
//                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
            
            ///single category form
            $('#single-category-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Please wait..');
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('v1/parking_config/create');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#single-category-form").find('button[type=submit]').attr('disabled',false);
                        $("#single-category-form").find('button[type=submit]').text('Add');
                       var result = response.data; // to get api result
                       console.log(response);
                        if(result.success){
                            grit('',result.msg);
                            $('#single-category-form input').prop('readonly',true);
                            $("#single-category-form").find('button[type=submit]').attr('disabled',true);
//                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
            
            $('#multiple-category-form').submit(function(e){
                e.preventDefault();
                if ($(this).valid()){
                   $(this).find('button[type=submit]').attr('disabled',true);
                   $(this).find('button[type=submit]').text('Please wait..');
                    
                    var records = $.param($( this ).serializeArray());
                    var request_url = generateUrl('v1/parking_config/create');
                    $http({
                            url: request_url,
                            method: "POST",
                            data: records,
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                        })
                    .then(function(response) {
                        $("#multiple-category-form").find('button[type=submit]').attr('disabled',false);
                        $("#multiple-category-form").find('button[type=submit]').text('Add');
                       var result = response.data; // to get api result
                       console.log(response);
                        if(result.success){
                            grit('',result.msg);
                            $('#single-category-form input').prop('readonly',true);
                            $("#multiple-category-form").find('button[type=submit]').attr('disabled',true);
//                            window.location.reload();
                        }else{
                            console.log("some error occured!");
                        } 
                       
                        
                    }, 
                    function(response) { // optional
//                        alert("fail");
                    });
                }
            });
            
            $http.get(generateUrl('society_info'))
               .then(function(response){
                    var str = response.data.response.data.google_map_src;
           $('#i_frame').attr('src', str)

                        if(str == "") {
                            $('#map').hide();
                        }
                        else
                        {
                          $('#map').show();  
                        }
               });
               
//            $scope.map = function() {
//               $http.get(generateUrl('society_info'))
//               .then(function(response){
//                    $window.open(response.data.response.data.google_map_src);
//               });
//                };
            
            function initialize(lat,lng) {
               
                var myCenter = new google.maps.LatLng(lat,lng);
                var marker;
                var mapProp = {
                    center:myCenter,
                    zoom:18,
                    mapTypeId:google.maps.MapTypeId.ROADMAP
                };
                var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
                var marker=new google.maps.Marker({
                    position:myCenter,
                    animation:google.maps.Animation.BOUNCE
                    });

                  marker.setMap(map);

            }

            google.maps.event.addDomListener(window, 'load', initialize(19.117876,72.883250));
          
          
            $scope.map = function() {
                var map;
                $http.get(generateUrl('society_info'))
               .then(function(response) {  
                var map = response.data.response.data.name + ' ' + response.data.response.data.address + ' ' + response.data.response.data.address_line_2 + ' ' +  response.data.response.data.city + ' ' +  response.data.response.data.state + ' ' +  response.data.response.data.pincode;
                $scope.getCities($scope.society.state_id);
                $http.get("https://maps.googleapis.com/maps/api/geocode/json?address="+ map +"&key=AIzaSyDM1FfKl1JiAjQXLzyDYURJbZiF5XalZ_g&sensor=true")
                .then(function(response) {
                    $scope.maps = response.data.results;
                    var lat = $scope.maps[0].geometry.location.lat;
                    var lng = $scope.maps[0].geometry.location.lng;
                    google.maps.event.addDomListener(window, 'load', initialize(lat,lng));
            });
                
                });

            }
             
            $scope.map();
        });
    </script>
    <style>.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fff;
    opacity: 1;</style>
    <div class="col-lg-12" ng-controller="SocietyInfoCtrl" >
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <!--<li role="presentation" ><a href="#society_info" aria-controls="profile" role="tab" data-toggle="tab">Society Info</a></li>
          <li role="presentation" class="active"><a href="#parking" aria-controls="messages" role="tab" data-toggle="tab">Parking</a></li> -->
          <!--<li role="presentation"><a href="#google_map" aria-controls="messages" role="tab" data-toggle="tab">Google Map</a></li>-->
        </ul>
        
        <!-- Tab panes -->
        <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="society_info">
                <div class="row">
                    <div class="col-lg-6">
                        <form id="society-form" method="post" action="">
                            <div class="form-group">
                                 <label class="form-label">Society Name</label>
                                 <input type="text" class="form-control" name = "name" maxlength="40" value="@{{society.name}}"  placeholder="Society Name">
                            </div>            
                            <div class="form-group">
                                <label class="form-label" for="emailsubject">Society Type</label> 
                                <select  name="society_category_id"
                                        class="form-control" required>
                                        <option value="@{{society.typeId}}" selected="">@{{society.type}}</option>
                                        <option ng-repeat="type in types" value='@{{type.id}}'>@{{type.name}}</option>
                                </select> 
                            </div>
                            <div class="form-group">
                                <label class="form-label">Address Line 1</label>
                                <textarea ng-model= "society.address" class="form-control" name="address" value="@{{society.address}}"
                                                           placeholder="Address line 1"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Address Line 2</label>
                                <textarea class="form-control" name="address_line_2" value="@{{society.address_line_2}}"
                                                           placeholder="Address line 2"></textarea>
                            </div>

                             <div class="form-group">
                                    <label class="form-label">State</label>
                                    <select ng-model="StateSelected" ng-change="change()" name="state_id" class="form-control">
                                        <option value="" disabled="">Select State</option>
                                        <option ng-repeat="state in states" value='@{{state.id}}' ng-selected="society.state_id == state.id">@{{state.name}}</option>
                                    </select>
                                 </div>

                            <div class="form-group">
                                    <label class="form-label">City</label>
                                    <select name="city_id" class="form-control">
                                        <option value="" disabled="">Select City</option>
                                        <option ng-repeat="city in cities" value='@{{city.id}}' ng-selected="society.city_id == city.id">@{{city.name}}</option>
                                    </select>
                                 </div>
                             <div class="form-group">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode" value="@{{society.pincode}}"  placeholder="Pincode">
                            </div>  

                            <div class="form-group">
                                <label >Landmark</label>
                                <input type="text" class="form-control" id="unique_id" maxlength="100" name = "landmark" value="@{{society.landmark}}"  placeholder="Landmark">
                            </div>
                            <div class="form-group">
                                <label >Nearest station</label>
                                <input type="text" class="form-control" name = "nearest_station" value="@{{society.nearest_station}}"  placeholder="Nearest Station">
                            </div>
                            
                            <div class="form-group">
<!--                                <label >Google Map URL</label>
                                <input type="text" class="form-control" name = "google_map_src" value="@{{society.google_map_src}}"  placeholder="Iframe">-->
                                
<!--                                <div id="map">
                                    <a ng-click="map()" href="#">  View on Google Map</a>  
                                   <iframe id="i_frame"  width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>    
                                </div>-->
                                 <div id="googleMap" style="width:500px;height:380px;"></div> 
                                 <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="map in maps" class="edit" on-finish-render="ngRepeatFinished">
                                        <td>@{{map.geometry.location.lat}}</td>
                                        <td>@{{map.geometry.location.lng}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{route('conversations')}}"><button class="btn btn-primary" type="button"  >Cancel</button></a>
                        </form>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane active" id="parking">
             
                <div class="row">
                    <div class="col-lg-12">
                        <form class="form-horizontal" id="open-category-form" method="post" action="">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Open</label>
                                <div class="col-xs-2">
                                    <input type="text" class="form-control" value="@{{openCategory_data.total_slot}}" maxlength="4" ng-readonly="openCategory_data ? 1:0" name="total_slot" placeholder="Slots">
                                </div>
                                <div class="col-xs-2">
                                    <input type="text" class="form-control" name="slot_name_prefix" value="@{{openCategory_data.slot_name_prefix}}" ng-readonly="openCategory_data ? 1:0" placeholder="Initials">
                                </div>
<!--                                <div class="col-xs-2">
                                    <input type="text" class="form-control" name="slot_charges" value="@{{openCategory_data.slot_charges}}" ng-readonly="openCategory_data ? 1:0" placeholder="Slot Charges">
                                </div>-->
                                <div class="col-xs-2">
                                     <button type="submit" ng-disabled="openCategory_data ? 1:0" class="btn btn-primary">Add</button>
<!--                                     </div>
                                <div class="col-xs-2">
                                     <button type="submit" ng-enabled="openCategory_data ? 1:0" class="btn btn-primary">Delete</button>-->
                                     <input type="hidden" name="category_id" value="1">
                                </div>
                            </div>
                            </form>
                            <hr>
                            <form class="form-horizontal" id="still-category-form" method="post" action="">
                                <div class="form-group">
                                  <label class="col-sm-2 control-label">Stilt</label>
                                  <div class="col-xs-2">
                                      <input type="text" class="form-control" name="total_slot" maxlength="4" value="@{{stillCategory_data.total_slot}}" ng-readonly="stillCategory_data ? 1:0" placeholder="Slots">
                                  </div>
                                  <div class="col-xs-2">
                                      <input type="text" class="form-control" name="slot_name_prefix" value="@{{stillCategory_data.slot_name_prefix}}" ng-readonly="stillCategory_data ? 1:0" placeholder="Initials">
                                  </div>
<!--                                  <div class="col-xs-2">
                                      <input type="text" class="form-control" name="slot_charges" value="@{{stillCategory_data.slot_charges}}" ng-readonly="stillCategory_data ? 1:0" placeholder="Slot Charges">
                                  </div>-->
                                  <div class="col-xs-2">
                                         <button type="submit" ng-disabled="stillCategory_data ? 1:0" class="btn btn-primary">Add</button>
                                         <input type="hidden" name="category_id" value="2">
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <form class="form-horizontal" id="garage-category-form" method="post" action="">
                                <div class="form-group">
                                  <label class="col-sm-2 control-label">Garage</label>
                                  <div class="col-xs-2">
                                      <input type="text" class="form-control" name="total_slot" maxlength="4" value="@{{garageCategory_data.total_slot}}" ng-readonly="isset(garageCategory_data)? 1:0" placeholder="Slots">
                                  </div>
                                  <div class="col-xs-2">
                                      <input type="text" class="form-control" name="slot_name_prefix" value="@{{garageCategory_data.slot_name_prefix}}" ng-readonly="isset(garageCategory_data)? 1:0" placeholder="Initials">
                                  </div>
<!--                                   <div class="col-xs-2">
                                      <input type="text" class="form-control" name="slot_charges" value="@{{garageCategory_data.slot_charges}}" ng-readonly="garageCategory_data ? 1:0" placeholder="Slot Charges">
                                  </div>-->
                                  <div class="col-xs-2">
                                         <button type="submit" ng-disabled="garageCategory_data ? 1:0" class="btn btn-primary">Add</button>
                                         <input type="hidden" name="category_id" value="3">
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <form class="form-horizontal" id="single-category-form" method="post" action="">
                                <div class="form-group">
                                  <label class="col-sm-2 control-label">Single Stacked</label>
                                  <div class="col-xs-2">
                                      <input type="text" id="single_alots" class="form-control" name="total_slot" maxlength="4" value="@{{singleCategory_data.total_slot}}" placeholder="Slots" readonly="readonly">
                                  </div>
                                  <div class="col-xs-2">
                                      <input type="text" class="form-control" name="slot_name_prefix" value="@{{singleCategory_data.slot_name_prefix}}" ng-readonly="singleCategory_data ? 1:0" placeholder="Initials">
                                  </div>
                                  <div class="col-xs-2">
                                      <input type="text" id="single_col" name="stack_column" class="form-control" value = "1" maxlength="4" readonly="readonly" placeholder="Cols">
                                  </div>
                                  </div>
                                   <div class="form-group">
                                       <label class="col-sm-2 control-label"></label>
                                  <div class="col-xs-2">
                                      <input type="text" id="single_rows" name="stack_row" class="form-control" maxlength="4" value="@{{singleCategory_data.stack_row}}" ng-readonly="singleCategory_data ? 1:0" placeholder="Rows" >
                                  </div>
<!--                                  <div class="col-xs-2">
                                      <input type="text" name="slot_charges" class="form-control" value="@{{singleCategory_data.slot_charges}}" ng-readonly="singleCategory_data ? 1:0" placeholder="Slot Charges" >
                                  </div>-->
                                  <div class="col-xs-2">
                                         <button type="submit" ng-disabled="singleCategory_data ? 1:0" class="btn btn-primary">Add</button>
                                         <input type="hidden" name="category_id" value="5">
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <form class="form-horizontal" id="multiple-category-form" method="post" action="">
                                <div class="form-group">
                                  <label class="col-sm-2 control-label">Multiple Stacked</label>
                                    <div class="col-xs-2">
                                        <input type="text" id="multiple_slots" class="form-control" placeholder="Slots" maxlength="4" name="total_slot" value="@{{multipleCategory_data.total_slot}}" readonly="readonly">
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="text" class="form-control" name="slot_name_prefix" value="@{{multipleCategory_data.slot_name_prefix}}" placeholder="Initials">
                                    </div>
                                    <div class="col-xs-2">
                                        <input type="text" id="multiple_cols" class="form-control" name="stack_column" maxlength="4" value="@{{multipleCategory_data.stack_column}}" ng-readonly="multipleCategory_data ? 1:0" placeholder="Cols">
                                    </div>
                                    </div>
                                     <div class="form-group">
                                          <label class="col-sm-2 control-label"></label>
                                    <div class="col-xs-2">
                                        <input type="text" id="multiple_rows" class="form-control" name="stack_row" maxlength="4" value="@{{multipleCategory_data.stack_row}}" ng-readonly="multipleCategory_data ? 1:0" placeholder="Rows">
                                    </div>
<!--                                     <div class="col-xs-2">
                                        <input type="text" class="form-control" name="slot_charges" value="@{{multipleCategory_data.slot_charges}}" ng-readonly="multipleCategory_data ? 1:0" placeholder="Slot Charges">
                                    </div>-->
                                    <div class="col-xs-2">
                                         <button type="submit" ng-disabled="multipleCategory_data ? 1:0" class="btn btn-primary">Add</button>
                                         <input type="hidden" name="category_id" value="6">
                                    </div>
                                </div>
                            </form>
                            
                            
<!--                                <div class="form-group">
                                     <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Add</button>
                            <a href="{{route('conversations')}}"><button class="btn btn-default" type="button"  >Cancel</button></a>
                           </div></div>-->
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="google_map">
                
            </div>
        </div>
      </div>
    <script>
    $('document').ready(function(){
        jQuery(':input[type="text"], textarea').change(function() {
                jQuery(this).val(jQuery(this).val().trim());
            });
    $("#society-form").validate({
                rules: {
                      // simple rule, converted to {required:true}
                    name: "required",
                    address: "required",
                    // email: "required",
//                    contact_no : "required",
                    state_id : {
                      required:true,
                    },
                     city_id : {
                      required:true,
                    },
                   pincode : {
                        required:true,
                        number: true,
                        minlength: 6,
                        maxlength: 6,
                     },
                  
                },
                 });
             });
             </script>
    <script>
        $('document').ready(function(){
            jQuery(':input[type="text"], textarea').change(function() {
                jQuery(this).val(jQuery(this).val().trim());
            });
            
           $( "#single_rows" ).keyup(function() {
                var single_rows = $('#single_rows').val();
                $('#single_alots').val(single_rows);
            });
            $("#multiple_rows,#multiple_cols").keyup(function () {
                var multiple_rows = $('#multiple_rows').val();
                var multiple_cols = $('#multiple_cols').val();
                if(multiple_rows!='' && multiple_cols!='')
                {
                    var total_slots =  multiple_rows * multiple_cols
                    $('#multiple_slots').val(total_slots);
                }
        
            });
            $("#open-category-form").validate({
                rules: {
                    total_slot:
                    {
                        required:true,
                        number: true,
                    },
                    slot_name_prefix:"required",
//                    slot_charges:
//                            {
//                                required:true,
//                                number: true,
//                            }
                    
                },
            });
            $("#still-category-form").validate({
                rules: {
                    total_slot:
                    {
                        required:true,
                        number: true,
                    },
                    slot_name_prefix:"required",
//                    slot_charges:
//                            {
//                                required:true,
//                                number: true,
//                            }
                    
                },
            });
            $("#garage-category-form").validate({
                rules: {
                    total_slot:
                    {
                        required:true,
                        number: true,
                    },
                    slot_name_prefix:"required",
//                    slot_charges:
//                            {
//                                required:true,
//                                number: true,
//                            }
                    
                },
            });
            $("#single-category-form").validate({
                rules: {
                    total_slot:
                    {
                        required:true,
                        number: true,
                    },
                    slot_name_prefix:"required",
                    stack_column:
                            {
                               required:true,
                               number: true, 
                            },
                    stack_row:
                            {
                               required:true,
                               number: true,
                               
                            },
//                    slot_charges:
//                            {
//                                required:true,
//                                number: true,
//                            }
                    
                    
                },
            });
            $("#multiple-category-form").validate({
                rules: {
                    total_slot:
                    {
                        required:true,
                        number: true,
                    },
                    slot_name_prefix:"required",
                    stack_column:
                            {
                                required:true,
                                number: true,
                            },
                    stack_row:
                            {
                               required:true,
                               number: true,
                            },
//                    slot_charges:
//                            {
//                                required:true,
//                                number: true,
//                            }
                    
                },
            });
        });
    </script>
@stop
