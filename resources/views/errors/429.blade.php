@extends('errors::minimal')

@section('title', __('general.too_many_requests'))
@section('code', '429')
@section('message', __('general.too_many_requests'))
