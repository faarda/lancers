@extends('layouts.app')
<!-- Select Project -->

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{asset('css/step1.css')}}"/>
@endsection

@section('content')
@include('partials.header_stage1')

<div class="contaner">
    <div class="clearfix"></div>
    <br/>  <br/>

    <h3 class="text-center"><strong>What project are you estimating?</strong></h3>
    @if(session()->has('message.alert'))
    <div class="text-center">
        <button class=" alert alert-{{ session('message.alert') }}"> 
            {!! session('message.content') !!}
        </button>
    </div>
    @endif
    <form id="create-project" method="post"action="/guest/save/step1" >
        @csrf
        <div class="row ml-auto box justify-content-center">
           
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><strong>A new project</strong></h5>
                        <p class="card-text">Create a new estimate and set up a new project based on the
                            information.
                        </p>
                        <input type="text" class="form-control" name="project_name" type="text" placeholder="Project Name" id="createProject">           
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row ml-auto box justify-content-center mt-20" style="margin-top: 20px;">
            <div class="col-sm-4">
                <input class="disabled" id="ext" type="submit" value="NEXT">
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')
    
<script>
        function verifyPath() {
        let a_next =  document.querySelector('.a-next');
        let next = document.querySelector('.next');
        let bt = document.getElementById('btne');


        
        if (createProject.value !== "" && createProject.value.length >= 4 ) {
             a_next.style.background = '#0ABAB5';
             next.style.background = '#0ABAB5';
              bt.disabled = false;


            document.querySelector('.a-next').classList.remove('disabled');
            document.querySelector('.next').classList.remove('disabled');
        } else {

            //console.log('here works');
            document.querySelector('.next').style.background = 'rgba(207, 204, 204, 0.4)';
            document.querySelector('.next').classList.add('disabled');
            document.querySelector('.a-next').style.background = 'rgba(207, 204, 204, 0.4)';
            document.querySelector('.a-next').classList.add('disabled');
             bt.disabled = true;
              bt.preventDefault();
            
            
             
        }
    }
    
    let createProject = document.getElementById('createProject');
    window.onload=function(){
         createProject.addEventListener('keyup', verifyPath);
    }
   

     function manage(createProject) {
        let bt = document.getElementById('btne');
        if (createProject.value != '') {
            bt.disabled = false;
        }
        else {
            bt.disabled = true;
             bt.preventDefault(); 
        }
    }    
    </script>
@endsection

