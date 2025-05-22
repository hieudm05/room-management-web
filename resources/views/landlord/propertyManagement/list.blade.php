@extends('landlord.layouts.app')

@section('title', 'List Properties ')

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex justify-content-between">
                <h4 class="card-title mb-0">List Properties</h4>
                <a href="{{ route('landlords.properties.create')}}" class="btn btn-success btn-sm">
                    + Add Property
                </a>
            </div>

            <!-- end card header -->

            <div class="card-body">
                <div class="live-preview">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap table-striped-columns mb-0">
                            <thead class="table-light">
                                <tr>

                                    <th scope="col">Id</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Created_at</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listProperties as $key => $Property)
                                    @php
                                        $status = $Property->status;
                                        $badgeClass = 'badge badge-light';
                                        switch ($status) {
                                            case 'Pending':
                                                $badgeClass = 'badge bg-warning';
                                                break;
                                            case 'Approved':
                                                $badgeClass = 'badge bg-success';
                                                break;
                                            case 'Rejected':
                                                $badgeClass = 'badge bg-danger';
                                                break;
                                            case 'Suspended':
                                                $badgeClass = 'badge bg-secondary';
                                                break;
                                        }
                                    @endphp
                                    <tr>

                                        <td><a href="#" class="fw-medium">{{ $Property->id }}</a></td>
                                        <td>{{ $Property->name }}</td>
                                        <td>{{ $Property->address }}</td>
                                        <td>{{ $Property->created_at }}</td>
                                        <td><span class="{{ $badgeClass }}">{{ $status }}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-light">Details</button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- end card-body -->

        </div><!-- end card -->
    </div><!-- end col -->

    {{-- Paginate --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $listProperties->links() }}
    </div>

@endsection
