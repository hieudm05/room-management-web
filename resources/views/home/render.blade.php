
@extends('home.layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">

				
					<div class="row justify-content-center">
						<div class="col-lg-7 col-md-10 text-center">
							<div class="sec-heading center mb-4">
								<h2>Recent Listed Property</h2>
								<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores</p>
							</div>
						</div>
					</div>
					
					<div class="row justify-content-center g-4">
						
						<!-- Single Property -->
						<div class="col-lg-4 col-md-6 col-sm-12">
							<div class="property-listing property-2 h-100">
								
								<div class="listing-img-wrapper">
									<div class="_exlio_125">For Sale</div>
									<div class="list-img-slide">
										<div class="click">
											<div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-1.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-1.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-1.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="assets/img/p-1.png" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-2.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-2.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-2.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="assets/img/p-2.png" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-3.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-3.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="{{ asset('assets/client/img/p-3.png') }}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"><img src="assets/img/p-3.png" class="img-fluid mx-auto" alt="" /></a></div>
										</div>
									</div>
								</div>
								
								<div class="listing-detail-wrapper">
									<div class="listing-short-detail-wrap">
										<div class="_card_list_flex mb-2">
											<div class="_card_flex_01">
												<span class="_list_blickes _netork">6 Network</span>
												<span class="_list_blickes types">Family</span>
											</div>
											<div class="_card_flex_last">
												<div class="prt_saveed_12lk">
													<label class="toggler toggler-danger"><input type="checkbox"><i class="ti-heart"></i></label>
												</div>
											</div>
										</div>
										<div class="_card_list_flex">
											<div class="_card_flex_01">
												<h4 class="listing-name verified"><a href="single-property-1.html" class="prt-link-detail">7012 Shine Sehu Street, Liverpool London, LC345AC</a></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="price-features-wrapper">
									<div class="list-fx-features">
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="assets/img/bed.svg" width="15" alt="" /></div>3 Beds
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="assets/img/bathtub.svg" width="15" alt="" /></div>1 Bath
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="" /></div><div class="inc-fleat-icon"><img src="assets/img/move.svg" width="15" alt="" /></div>800 sqft
										</div>
									</div>
								</div>
								
								<div class="listing-detail-footer">
									<div class="footer-first">
										<h6 class="listing-card-info-price mb-0 p-0">$7,000</h6>
									</div>
									<div class="footer-flex">
										<a href="property-detail.html" class="prt-view">View Detail</a>
									</div>
								</div>
								
							</div>
						</div>
						<!-- End Single Property -->
						
						<!-- Single Property -->
						<div class="col-lg-4 col-md-6 col-sm-12">
							<div class="property-listing property-2 h-100">
								
								<div class="listing-img-wrapper">
									<div class="_exlio_125">For Rent</div>
									<div class="list-img-slide">
										<div class="click">
											<div><a href="single-property-1.html"><img src="assets/img/p-4.png" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="assets/img/p-5.png" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="assets/img/p-6.png" class="img-fluid mx-auto" alt="" /></a></div>
										</div>
									</div>
								</div>
								
								<div class="listing-detail-wrapper">
									<div class="listing-short-detail-wrap">
										<div class="_card_list_flex mb-2">
											<div class="_card_flex_01">
												<span class="_list_blickes _netork">4 Network</span>
												<span class="_list_blickes types">Condos</span>
											</div>
											<div class="_card_flex_last">
												<div class="prt_saveed_12lk">
													<label class="toggler toggler-danger"><input type="checkbox"><i class="ti-heart"></i></label>
												</div>
											</div>
										</div>
										<div class="_card_list_flex">
											<div class="_card_flex_01">
												<h4 class="listing-name verified"><a href="single-property-1.html" class="prt-link-detail">5689 Resot Relly Market, Montreal Canada, HAQC445</a></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="price-features-wrapper">
									<div class="list-fx-features">
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="assets/img/bed.svg" width="15" alt="" /></div>4 Beds
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="assets/img/bathtub.svg" width="15" alt="" /></div>2 Bath
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="assets/img/move.svg" width="15" alt="" /></div>740 sqft
										</div>
									</div>
								</div>
								
								<div class="listing-detail-footer">
									<div class="footer-first">
										<h6 class="listing-card-info-price mb-0 p-0">$8,200</h6>
									</div>
									<div class="footer-flex">
										<a href="property-detail.html" class="prt-view">View Detail</a>
									</div>
								</div>
								
							</div>
						</div>
						<!-- End Single Property -->
						
						<!-- Single Property -->
						<div class="col-lg-4 col-md-6 col-sm-12">
							<div class="property-listing property-2 h-100">
								
								<div class="listing-img-wrapper">
									<div class="_exlio_125">For Sale</div>
									<div class="list-img-slide">
										<div class="click">
											<div><a href="single-property-1.html"><img src="assets/img/p-7.png" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="assets/img/p-8.png" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="assets/img/p-9.png" class="img-fluid mx-auto" alt="" /></a></div>
										</div>
									</div>
								</div>
								
								<div class="listing-detail-wrapper">
									<div class="listing-short-detail-wrap">
										<div class="_card_list_flex mb-2">
											<div class="_card_flex_01">
												<span class="_list_blickes _netork">5 Network</span>
												<span class="_list_blickes types">Offices</span>
											</div>
											<div class="_card_flex_last">
												<div class="prt_saveed_12lk">
													<label class="toggler toggler-danger"><input type="checkbox"><i class="ti-heart"></i></label>
												</div>
											</div>
										</div>
										<div class="_card_list_flex">
											<div class="_card_flex_01">
												<h4 class="listing-name verified"><a href="single-property-1.html" class="prt-link-detail">9632 New Green Garden, Huwai Denever USA, AWE789O</a></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="price-features-wrapper">
									<div class="list-fx-features">
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="assets/img/bed.svg" width="15" alt="" /></div>5 Beds
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="assets/img/bathtub.svg" width="15" alt="" /></div>2 Bath
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="assets/img/move.svg" width="15" alt="" /></div>900 sqft
										</div>
									</div>
								</div>
								
								<div class="listing-detail-footer">
									<div class="footer-first">
										<h6 class="listing-card-info-price mb-0 p-0">$9,500</h6>
									</div>
									<div class="footer-flex">
										<a href="property-detail.html" class="prt-view">View Detail</a>
									</div>
								</div>
								
							</div>
						</div>
						<!-- End Single Property -->
						
						<!-- Single Property -->
						<div class="col-lg-4 col-md-6 col-sm-12">
							<div class="property-listing property-2 h-100">
								
								<div class="listing-img-wrapper">
									<div class="_exlio_125">For Rent</div>
									<div class="list-img-slide">
										<div class="click">
											<div><a href="single-property-1.html"><img src="{{(asset('assets/img/p-9.png'))}}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html">></a></div>
											<div><a href="single-property-1.html"><img src="{{(asset('assets/img/p-11.png'))}}" class="img-fluid mx-auto" alt="" /></a></div><div><a href="single-property-1.html"></a></div>
											<div><a href="single-property-1.html"><img src="assets/img/p-12.png" class="img-fluid mx-auto" alt="" /></a></div>
										</div>
									</div>
								</div>
								
								<div class="listing-detail-wrapper">
									<div class="listing-short-detail-wrap">
										<div class="_card_list_flex mb-2">
											<div class="_card_flex_01">
												<span class="_list_blickes _netork">7 Network</span>
												<span class="_list_blickes types">Apartment</span>
											</div>
											<div class="_card_flex_last">
												<div class="prt_saveed_12lk">
													<label class="toggler toggler-danger"><input type="checkbox"><i class="ti-heart"></i></label>
												</div>
											</div>
										</div>
										<div class="_card_list_flex">
											<div class="_card_flex_01">
												<h4 class="listing-name verified"><a href="single-property-1.html" class="prt-link-detail">8512 Red Reveals Market, Montreal Canada, SHQT45O</a></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="price-features-wrapper">
									<div class="list-fx-features">
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{(asset('assets/client/img/bed.svg'))}}" width="15" alt="" /></div>4 Beds
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{(asset('assets/client/img/bathtub.svg'))}}" width="15" alt="" /></div>2 Bath
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{(asset('assets/client/img/move.svg'))}}" width="15" alt="" /></div>920 sqft
										</div>
									</div>
								</div>
								
								<div class="listing-detail-footer">
									<div class="footer-first">
										<h6 class="listing-card-info-price mb-0 p-0">$10,400</h6>
									</div>
									<div class="footer-flex">
										<a href="property-detail.html" class="prt-view">View Detail</a>
									</div>
								</div>
								
							</div>
						</div>
						<!-- End Single Property -->
						
						<!-- Single Property -->
						<div class="col-lg-4 col-md-6 col-sm-12">
							<div class="property-listing property-2 h-100">
								
								<div class="listing-img-wrapper">
									<div class="_exlio_125">For Sale</div>
									<div class="list-img-slide">
										<div class="click">
											<div><a href="single-property-1.html"><img src="{{(asset('assets/client/img/p-12.png'))}}" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="{{(asset('assets/client/img/p-13.png'))}}" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="{{(asset('assets/client/img/p-14.png'))}}" class="img-fluid mx-auto" alt="" /></a></div>
										</div>
									</div>
								</div>
								
								<div class="listing-detail-wrapper">
									<div class="listing-short-detail-wrap">
										<div class="_card_list_flex mb-2">
											<div class="_card_flex_01">
												<span class="_list_blickes _netork">7 Network</span>
												<span class="_list_blickes types">Villas</span>
											</div>
											<div class="_card_flex_last">
												<div class="prt_saveed_12lk">
													<label class="toggler toggler-danger"><input type="checkbox"><i class="ti-heart"></i></label>
												</div>
											</div>
										</div>
										<div class="_card_list_flex">
											<div class="_card_flex_01">
												<h4 class="listing-name verified"><a href="single-property-1.html" class="prt-link-detail">7298 Rani Market Near Saaket, Henever Canada, QWUI98</a></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="price-features-wrapper">
									<div class="list-fx-features">
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{('assets/client/img/bed.svg')}}" width="15" alt="" /></div>4 Beds
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{('assets/client/img/bathtub.svg')}}" width="15" alt="" /></div>3 Bath
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src="{{('assets/client/img/move.svg')}}" width="15" alt="" /></div>850 sqft
										</div>
									</div>
								</div>
								
								<div class="listing-detail-footer">
									<div class="footer-first">
										<h6 class="listing-card-info-price mb-0 p-0">$9,200</h6>
									</div>
									<div class="footer-flex">
										<a href="property-detail.html" class="prt-view">View Detail</a>
									</div>
								</div>
								
							</div>
						</div>
						<!-- End Single Property -->
						
						<!-- Single Property -->
						<div class="col-lg-4 col-md-6 col-sm-12">
							<div class="property-listing property-2 h-100">
								
								<div class="listing-img-wrapper">
									<div class="_exlio_125">For Rent</div>
									<div class="list-img-slide">
										<div class="click">
											<div><a href="single-property-1.html"><img src="{{(asset('assets/client/img/p-16.png'))}}" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="{{(asset('assets/client/img/p-17.png'))}}" class="img-fluid mx-auto" alt="" /></a></div>
											<div><a href="single-property-1.html"><img src="{{(asset('assets/client/img/p-18.png'))}}" class="img-fluid mx-auto" alt="" /></a></div>
										</div>
									</div>
								</div>
								
								<div class="listing-detail-wrapper">
									<div class="listing-short-detail-wrap">
										<div class="_card_list_flex mb-2">
											<div class="_card_flex_01">
												<span class="_list_blickes _netork">10 Network</span>
												<span class="_list_blickes types">Family</span>
											</div>
											<div class="_card_flex_last">
												<div class="prt_saveed_12lk">
													<label class="toggler toggler-danger"><input type="checkbox"><i class="ti-heart"></i></label>
												</div>
											</div>
										</div>
										<div class="_card_list_flex">
											<div class="_card_flex_01">
												<h4 class="listing-name verified"><a href="single-property-1.html" class="prt-link-detail">7264 Green Glelcer Street, Barghimbar USA, ERIO098</a></h4>
											</div>
										</div>
									</div>
								</div>
								
								<div class="price-features-wrapper">
									<div class="list-fx-features">
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src=" {{ asset('assets/client/img/bed.svg') }} "width="15" alt="" /></div>4 Beds
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src={{ asset('assets/client/img/bathtub.svg') }} width="15" alt="" /></div>2 Bath
										</div>
										<div class="listing-card-info-icon">
											<div class="inc-fleat-icon"><img src={{ asset('assets/client/img/move.svg') }} width="15" alt="" /></div>750 sqft
										</div>
									</div>
								</div>
								
								<div class="listing-detail-footer">
									<div class="footer-first">
										<h6 class="listing-card-info-price mb-0 p-0">$9,100</h6>
									</div>
									<div class="footer-flex">
										<a href="property-detail.html" class="prt-view">View Detail</a>
									</div>
								</div>
								
							</div>
						</div>
						<!-- End Single Property -->
						
					</div>
					
					<!-- Pagination -->
					<div class="row mt-5">
						<div class="col-lg-12 col-md-12 col-sm-12 text-center">
							<a href="list-layout-with-map.html" class="btn btn-light-danger">Explore More Properties</a>
						</div>
					</div>
					
				</div>
            </div>
        </div>
    </div>
</div>
@endsection
    