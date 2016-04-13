@extends('layouts.default') @section('panel_title')My Adda @stop
@section('head')
<script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
@stop @section('panel_subtitle','Topic from admin forum')
@section('content')


<script>
app.controller('topicController',function($scope,$http){

$scope.data=null;

$scope.submit=function(){

	var $data = {
			title:this.title,
			text:CKEDITOR.instances.editor1.getData()
		};

	$http.post(generateUrl('adminforum/topic/save'), $data).
	  then(function(response) {

		  console.log(response.data.response.msg);
		  alert(response.data.response.msg);
		  window.location = "http://localhost/apartment/public/index.php/admin/adminforum";
	  });
}

})
</script>


<div ng-controller="topicController" class="col-md-12">



	<div class="media">
		<div class="media-left">


			<a href="#"> <img class="media-object" src="..." alt="Image">
			</a>
			<p>Name</p>
			<p>flat details</p>
			<p>time and date</p>


		</div>
		<div class="media-body" ng-repeat="topic in topics">
				<h4 class="media-heading">@{{topic.title} }</h4>
		</div>
	</div>

	<form ng-submit="submit()">
		<input ng-model="title" type="text" class="form-control"
			placeholder="">
		<table>
			<tr>
				<td><textarea ng-model="text" name="editor1" id="editor1" rows="10"
						cols="80">
                			This is my textarea to be replaced with CKEditor.
            				</textarea></td>
				<td><script>
                			CKEDITOR.replace( 'editor1' );
            				</script></td>

			</tr>
			<tr>
				<td><p>Attachment 1 ( Max size : 0.5 MB )</p></td>
				<td>
					<div class="form-group">
						<input type="file" id="exampleInputFile">
					</div>
				</td>
			</tr>

			<tr>
				<td><button type="button" class="btn btn-default">Preview</button></td>
				<td><button type="submit" class="btn btn-default">Post</button></td>
			</tr>
		</table>

	</form>

</div>

@stop
