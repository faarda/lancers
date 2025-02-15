@extends('layouts.app')
<!-- Select Project -->

@section('styles')
    <link rel="stylesheet" href="{{asset('css/step2.css')}}"/>
@endsection


@section('content')
<form id="form" onsubmit="submitEvent(event)" method="post" action="/guest/save/step2">
    @csrf

    @include('partials.header_stage2')

    <div class="container-fluid main-section">

        <div id="word">
            <h1>Evaluation</h1>
            <h5>Please Input the required fields in the form below</h5>
            <br>
        </div>

        <div class="section1">
        <p style="color:red;">@if(null !== session('error')) {{session('error')}} @endif</p>

    <div class="section1">
            @php
                if(!empty($errors))
                {
                    foreach($errors as $error)
                    {
                        echo'<p style="color:red;">'.$error.'</p>';
                    }


                }
            @endphp
            <div id="glac">
                <h2 class="pull-left">{{$project['project_name']}}</h2>
                <hr />
            </div>
            <hr />

            <div id="bill">
                <h3>Billing</h3>

                <div class="hour">
                    <p>How long (in hours) will it take you to complete this project <i class="fa fa-question-circle" aria-hidden="true"></i></p>
                    <input type="number" min="0" required maxlength="10000" name="time" placeholder="Hours" style="width: 83% !important;" />
                </div>
                <div class="hour" style="display: none;">
                    <p>How much do you charge per hour <i class="fa fa-question-circle" aria-hidden="true"></i></p>
                    <input type="number" min="0" maxlength="10000000" value="5000" name="cost_per_hour" placeholder="NGN 0.00" style="width: 83% !important;" />
                </div>

                <div class="hours">
                    <p>Project starts/ends</p>
                    <i class="fa fa-calendar start"><input required type="text" onfocus="(this.type='date')" name="start" placeholder=" Set start date" /></i>
                    <i class="fa fa-calendar end"><input required type="text" onfocus="(this.type='date')" name="end" placeholder="Set end date" /></i>
                </div>

            </div>

            <hr />
            <div>
                <h3>Expenses</h3>
                <div class="hour">
                    <p id="cost">
                        How much would it cost you to power your devices or equipment for this project <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </p>
                    <input type="number" min="0" required name="equipment_cost" id="equipment_cost" placeholder="NGN 0.00" style="width: 83% !important;" />
                </div>

                <div class="hour">
                    <p id="sub">Sub contractors (If any)</p>
                    <input type="text" required name="sub_contractors" id="sub_contractors" style="width: 83% !important;" placeholder="E.g. Illustrator, Consulting..." class="sub" />
                </div>
                <br />

                <div class="hour">
                    <p id="pay">How much would they be paid <i class="fa fa-question-circle" aria-hidden="true"></i></p>
                    <input type="number" min="0" required name="sub_contractors_cost" id="sub_contractors_cost" placeholder="NGN 0.00" style="width: 83% !important;" />
                </div>
            </div>
            <br>
            <div>
                <h3>Expertise</h3>

                <div class="hour">
                    <p id="proj">How many similar projects have you done before <i class="fa fa-question-circle" aria-hidden="true"></i></p>
                    <input type="number"  min="0" max="10" maxlength="2"  required name="similar_projects" id="similar_projects" placeholder="0" style="width: 30% !important;">
                </div>

                <div class="hour">
                    <p id="rate">How would you rate your experience level in executing this project <i class="fa fa-question-circle" aria-hidden="true"></i><p>
                    <input required type="number" min="0" max="5" maxlength="1" name="rating" id="rating" placeholder="0" style="width: 30% !important;"> /5
                </div>

            </div>
            <hr />

            <div id="currency">
                Currency:
                <select class="hiddeselect" name="currency_id" required>
                    <option value="">Select Currency</option>
                    @foreach($currencies as $currency)
                    <option {{$currency->code == 'NGN' ? 'selected' : ''}} value="{{$currency->id}}">{{$currency->code}}</option>
                    @endforeach
                </select>

            </div>

        </div class="nex">

        <!-- <button class="btn">NEXT</button> -->
        <div class="row ml-auto box justify-content-center mt-20" style="margin-top: 20px;">
            <div class="col-sm-6">
                <button class="btn" name="next_btn">NEXT</button>
                <!-- <input class="disabled" id="ext" type="submit" value="NEXT"> -->
            </div>
        </div>
    </div>

    </div>
</form>
@endsection

@section('script')
<script>

    let form = document.querySelector('#form');
    let form_children = {};
    ['next_page', 'next_btn', 'time', 'start', 'end', 'rating', 'currency_id', 'similar_projects', 'sub_contractors_cost', 'sub_contractors', 'equipment_cost']
    .forEach(e=>form_children[e] = document.querySelector(`[name="${e}"]`));
    let {next_btn, start, end, next_page, time, rating, currency_id, similar_projects, sub_contractors_cost, sub_contractors, equipment_cost} = form_children;

    if(typeof(form_children['start']) !== 'undefined' &&  typeof(form_children['end']) !== 'undefined'){
        form_children['end'].addEventListener('change', function(e){
            if(form_children['start'].value > form_children['end'].value ){
                next_page.disabled = true;
                next_btn.disabled = true;
                next_page.classList.remove('validated');
                next_btn.classList.remove('validated');
                alert('Start date cannot be greater than end date')
                form_children['end'].value = '';
                return; // remove the validated toggle
            }
        });
        form_children['start'].addEventListener('change', function(e){
            if(form_children['end'].value !== '' && form_children['start'].value > form_children['end'].value ){
                next_page.disabled = true;
                next_btn.disabled = true;
                next_page.classList.remove('validated');
                next_btn.classList.remove('validated');
                alert('Start date cannot be greater than end date')
                form_children['end'].value = '';
                return; // remove the validated toggle
            }
        });
    }


    window.onload=function(){
        ['keyup', 'click']
        .forEach(e=>{form.addEventListener(e, validate)});
    }

    function validate(){
        for(let i in form_children){
            if(form_children[i] !== next_btn && form_children[i] !== next_page && falsy(form_children[i])) {
                // console.log(form_children[i])
                next_page.disabled = true;
                next_btn.disabled = true;
                next_page.classList.remove('validated');
                next_btn.classList.remove('validated');
                return; // remove the validated toggle
            }
        }

        // if(form_children['start'].value > form_children['end'].value ){
        //     console.log('ggg')
        //     next_page.disabled = true;
        //     next_btn.disabled = true;
        //     next_page.classList.remove('validated');
        //     next_btn.classList.remove('validated');
        //     alert('Start date cannot be greater than end date')
        //     return; // remove the validated toggle
        // }

        next_page.disabled = false;
        next_btn.disabled = false;
        next_page.classList.add('validated');
        next_btn.classList.add('validated');
    }

    function falsy(el){
        if(typeof el.selected !== 'undefined'){
            if(el.selected != '' && el.selected == null) return false;
        }else if(typeof el.value !== 'undefined'){
            if(el.value !== '' && el.value !== null) return false;
        }
        return true;
    }


</script>
@endsection
