@extends('layouts.default')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
@endpush

@section('content')
<h1>Sunny days in cities</h1>
<div id="app">
<select name="city" v-model="selectedCity" v-on:change="getSunshine">
<option value="">- Select city -</option>
@foreach ($cities as $city)
<option value="{{ $city->id }}">{{ $city->name }}</option>
@endforeach
</select>
<p v-if="longestSunshinePeriod">
Historical longest period of sunny days in given city: @{{ longestSunshinePeriod }}<br>
Longest period in current month: @{{ monthMaxSunshinePeriod }}<br>
Length of current period of sunshine: @{{ currentSunshinePeriod }}<br>
Script execution time: @{{ time }}
</p>
</div>
<script src="{{ asset('js/welcome.js') }}"></script>
@endsection