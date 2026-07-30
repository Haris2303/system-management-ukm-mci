@extends('layouts.app')

@section('title', 'UKM MCI | Media Creative Informations')
@section('description', Str::limit(($profil->deskripsi ?? null) ?: 'UKM MCI (Media Creative Informations) adalah Unit Kegiatan Mahasiswa yang mengeksplorasi dunia teknologi melalui pelatihan, proyek, dan kompetisi. Bergabunglah bersama kami.', 160))

@section('content')
    @include('landing._hero')
    @include('landing._about')
    @include('landing._divisis')
    @include('landing._berita')
    @include('landing._gallery')
@endsection
