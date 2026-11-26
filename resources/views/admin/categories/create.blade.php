@extends('layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
    <h1>Thêm danh mục</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories.partials._form')
        <button type="submit">Lưu danh mục</button>
        <a href="{{ route('admin.categories.index') }}">Quay lại</a>
    </form>
@endsection