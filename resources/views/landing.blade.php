@extends('layouts.app')

@section('title', 'UKM MCI — Mahasiswa Creative & Innovation')

@section('content')
    @include('landing._hero')
    @include('landing._about')
    @include('landing._programs')
    @include('landing._berita')
    @include('landing._gallery')
    @include('landing._pengurus')
    @include('landing._daftar')
@endsection
