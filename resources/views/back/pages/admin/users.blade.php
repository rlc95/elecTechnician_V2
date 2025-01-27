@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'users')
@section('content')
<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="h4 text-blue">Customers Details</h4>
            </div>
            {{-- <div class="pull-right">
                <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm" type="button">
                    <i class="fa fa-plus"></i>
                    Add Category
                </a>
            </div> --}}
        </div>
        <div class="table-responsive mt-4">
            <table class="table table-borderless table-striped">
                <thead class="bg-secondary text-white">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Province</th>
                        <th>District</th>
                        <th>City</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="sortable_categories">
                    @forelse ($client as $row)
                    <tr data-index="" data-ordering="">
                        <td>
                            {{ $row->name }}
                        </td>
                        <td>
                            {{ $row->email }}
                        </td>
                        <td>
                            {{ $row->phone }}
                        </td>
                        <td>
                            {{ $row->address }}
                        </td>
                        <td>
                            {{ $row->province }}
                        </td>
                        <td>
                            {{ $row->district }}
                        </td>
                        <td>
                            {{ $row->city }}
                        </td>
                        {{-- <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.manage-categories.edit-category',['id'=>$item->id]) }}" class="text-primary">
                                    <i class="dw dw-edit2"></i>
                                </a>
                                <a href="javascript:;" class="text-danger deleteCategoryBtn" data-id="{{ $item->id }}">
                                    <i class="dw dw-delete-3"></i>
                                </a>
                            </div>
                        </td> --}}
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <span class="text-danger">No users found!</span>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        <div class="d-block mt-2">
            {{-- {{ $categories->links('livewire::simple-bootstrap') }} --}}
        </div>
    </div>
</div>
@endsection
