@extends('layouts.app')

@section('title', 'Dashboard | Inggar ')

@section('content')
<!-- Start here.... -->
<div class="row">
    <div class="col-xxl-5">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary text-truncate mb-3" role="alert">
                    Selamat datang di Dashboard Admin!
                </div>
            </div>

            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <iconify-icon icon="solar:cart-5-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Total Orders</p>
                                <h3 class="text-dark mt-1 mb-0">13, 647</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success"> <i class="bx bxs-up-arrow fs-12"></i> 2.3%</span>
                                <span class="text-muted ms-1 fs-12">Last Week</span>
                            </div>
                            <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <i class="bx bx-award avatar-title fs-24 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">New Leads</p>
                                <h3 class="text-dark mt-1 mb-0">9, 526</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success"> <i class="bx bxs-up-arrow fs-12"></i> 8.1%</span>
                                <span class="text-muted ms-1 fs-12">Last Month</span>
                            </div>
                            <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <i class="bx bxs-backpack avatar-title fs-24 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Deals</p>
                                <h3 class="text-dark mt-1 mb-0">976</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-danger"> <i class="bx bxs-down-arrow fs-12"></i> 0.3%</span>
                                <span class="text-muted ms-1 fs-12">Last Month</span>
                            </div>
                            <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="avatar-md bg-soft-primary rounded">
                                    <i class="bx bx-dollar-circle avatar-title text-primary fs-24"></i>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted mb-0 text-truncate">Booked Revenue</p>
                                <h3 class="text-dark mt-1 mb-0">$123.6k</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-danger"> <i class="bx bxs-down-arrow fs-12"></i> 10.6%</span>
                                <span class="text-muted ms-1 fs-12">Last Month</span>
                            </div>
                            <a href="#!" class="text-reset fw-semibold fs-12">View More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Performance</h4>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-light">ALL</button>
                        <button type="button" class="btn btn-sm btn-outline-light">1M</button>
                        <button type="button" class="btn btn-sm btn-outline-light">6M</button>
                        <button type="button" class="btn btn-sm btn-outline-light active">1Y</button>
                    </div>
                </div>

                <div dir="ltr">
                    <div id="dash-performance-chart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Recent Orders</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#RB3625</td>
                                <td>John Doe</td>
                                <td>Product A</td>
                                <td>$150.00</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>21 April 2024</td>
                            </tr>
                            <tr>
                                <td>#RB3626</td>
                                <td>Jane Smith</td>
                                <td>Product B</td>
                                <td>$200.00</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>22 April 2024</td>
                            </tr>
                            <tr>
                                <td>#RB3627</td>
                                <td>Bob Johnson</td>
                                <td>Product C</td>
                                <td>$350.00</td>
                                <td><span class="badge bg-primary">Processing</span></td>
                                <td>23 April 2024</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/vendor/apexcharts.min.js') }}"></script>
<script>
</script>

@endpush