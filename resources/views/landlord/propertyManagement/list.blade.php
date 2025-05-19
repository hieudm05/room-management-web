@extends('landlord.layouts.app')

@section('title', 'List Properties ')

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">List Properties</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="live-preview">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap table-striped-columns mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 46px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck">
                                            <label class="form-check-label" for="cardtableCheck"></label>
                                        </div>
                                    </th>
                                    <th scope="col">#</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Created_at</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck01">
                                            <label class="form-check-label" for="cardtableCheck01"></label>
                                        </div>
                                    </td>
                                    <td><a href="#" class="fw-medium">#VL2110</a></td>
                                    <td>William Elmore</td>
                                    <td>07 Oct, 2021</td>
                                    <td>$24.05</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-light">Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck02">
                                            <label class="form-check-label" for="cardtableCheck02"></label>
                                        </div>
                                    </td>
                                    <td><a href="#" class="fw-medium">#VL2109</a></td>
                                    <td>Georgie Winters</td>
                                    <td>07 Oct, 2021</td>
                                    <td>$26.15</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-light">Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck03">
                                            <label class="form-check-label" for="cardtableCheck03"></label>
                                        </div>
                                    </td>
                                    <td><a href="#" class="fw-medium">#VL2108</a></td>
                                    <td>Whitney Meier</td>
                                    <td>06 Oct, 2021</td>
                                    <td>$21.25</td>
                                    <td><span class="badge bg-danger">Refund</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-light">Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck04">
                                            <label class="form-check-label" for="cardtableCheck04"></label>
                                        </div>
                                    </td>
                                    <td><a href="#" class="fw-medium">#VL2107</a></td>
                                    <td>Justin Maier</td>
                                    <td>05 Oct, 2021</td>
                                    <td>$25.03</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-light">Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- end card-body -->

        </div><!-- end card -->
    </div><!-- end col -->
@endsection
