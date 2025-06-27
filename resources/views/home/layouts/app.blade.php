<!DOCTYPE html>
<html lang="en">
	
<!-- Mirrored from shreethemes.net/rentup-demo/rentup/home-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 May 2025 03:53:28 GMT -->

	@include('home.blocks.head')
    <body class="yellow-skin">
	
		 <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
       <div class="preloader"></div>
		
        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <div id="main-wrapper">
		
            <!-- ============================================================== -->
            <!-- Top header  -->
            <!-- ============================================================== -->
            <!-- Start Navigation -->
			<div class="header header-transparent change-logo">
				<div class="container">
               @include('home.blocks.aside')
				</div>
			</div>
			<!-- End Navigation -->
			<div class="clearfix"></div>
			<!-- ============================================================== -->
			<!-- Top header  -->
			<!-- ============================================================== -->
			
			
			<!-- ============================ Hero Banner  Start================================== -->
			<div class="hero-banner vedio-banner">
				
				
				<div class="container">
					
					<div class="row justify-content-center">
						<div class="col-xl-12 col-lg-12 col-md-12">

							<h1 class="big-header-capt mb-0 text-light">Tìm kiếm phòng trọ </h1>
							<p class="text-center mb-4 text-light">Phòng trọ phù hợp theo sở thích của rieeng của bạn </p>
					
						</div>
					</div>
					
					<div class="row">
						<div class="col-xl-12 col-lg-12 col-md-12">
							<div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="simple_tab_search center">
                            <form action="#" method="GET"
                                style="display: flex; gap: 15px; align-items: center; width: 100%; max-width: 1200px; margin: 20px auto;">
                                <input type="text" name="keyword" placeholder="Nhập từ khóa tìm kiếm"
                                    style="flex: 1; padding: 12px 15px; font-size: 18px; border-radius: 5px; border: 1px solid #ccc;">
                                     
                                   
                                </>

                                <button type="submit"
                                    style="padding: 12px 25px; font-size: 18px; background-color: #cd1010; color: white; border: none; border-radius: 5px; cursor: pointer; min-width: 150px; transition: background-color 0.3s;">
                                    Tìm kiếm
                                </button>
                            </form>

										</div>
									</div>
									
									<!-- Tab for Sell -->
									<div class="tab-pane fade" id="sell" role="tabpanel" aria-labelledby="sell-tab">
										<div class="full_search_box nexio_search lightanic_search hero_search-radius modern">
											<div class="search_hero_wrapping">
										
												<div class="row">
												
													<div class="col-lg-3 col-sm-12 d-md-none d-lg-block">
														<div class="form-group">
															<label>Price Range</label>
															<input type="text" class="form-control search_input border-0" placeholder="ex. Neighborhood" />
														</div>
													</div>
													
													<div class="col-lg-3 col-md-3 col-sm-12">
														<div class="form-group">
															<label>City/Street</label>
															<div class="input-with-icon">
																<select id="lot-1" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">New York City</option>
																	<option value="2">Honolulu, Hawaii</option>
																	<option value="3">California</option>
																	<option value="4">New Orleans</option>
																	<option value="5">Washington</option>
																	<option value="6">Charleston</option>
																</select>
															</div>
														</div>
													</div>
													
													<div class="col-lg-2 col-md-3 col-sm-12">
														<div class="form-group">
															<label>Property Type</label>
															<div class="input-with-icon">
																<select id="ptype-1" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">All categories</option>
																	<option value="2">Apartment</option>
																	<option value="3">Villas</option>
																	<option value="4">Commercial</option>
																	<option value="5">Offices</option>
																	<option value="6">Garage</option>
																</select>
															</div>
														</div>
													</div>
													
													<div class="col-lg-2 col-md-3 col-sm-12">
														<div class="form-group none">
															<a class="collapsed ad-search" data-bs-toggle="collapse" data-parent="#search1" data-bs-target="#advance-search-1" href="javascript:void(0);" aria-expanded="false" aria-controls="advance-search"><i class="fa fa-sliders-h me-2"></i>Advance Filter</a>
														</div>
													</div>
													
													<div class="col-lg-2 col-md-3 col-sm-12 small-padd">
														<div class="form-group none">
															<a href="#" class="btn btn-danger full-width">Search Property</a>
														</div>
													</div>
												</div>
												
												<!-- Collapse Advance Search Form -->
												<div class="collapse" id="advance-search-1" aria-expanded="false" role="banner">
													
													<!-- row -->
													<div class="row">
													
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none style-auto">
																<select id="bedrooms1" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																	<option value="5">5</option>
																</select>
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none style-auto">
																<select id="bathrooms1" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																	<option value="5">5</option>
																</select>
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none">
																<input type="text" class="form-control" placeholder="min sqft" />
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none">
																<input type="text" class="form-control" placeholder="max sqft" />
															</div>
														</div>
														
													</div>
													<!-- /row -->
													
													<!-- row -->
													<div class="row">
														<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
															<h6>Advance Price</h6>
															<div class="rg-slider">
																 <input type="text" class="js-range-slider" name="my_range" value="" />
															</div>
														</div>
													</div>
													<!-- /row -->
													
													<!-- row -->
													<div class="row">
													
														<div class="col-lg-12 col-md-12 col-sm-12 mt-3">
															<h4 class="text-dark">Amenities & Features</h4>
															<ul class="no-ul-list third-row">
																<li>
																	<input id="a-1a" class="form-check-input" name="a-1a" type="checkbox">
																	<label for="a-1a" class="form-check-label">Air Condition</label>
																</li>
																<li>
																	<input id="a-2b" class="form-check-input" name="a-2b" type="checkbox">
																	<label for="a-2b" class="form-check-label">Bedding</label>
																</li>
																<li>
																	<input id="a-3c" class="form-check-input" name="a-3c" type="checkbox">
																	<label for="a-3c" class="form-check-label">Heating</label>
																</li>
																<li>
																	<input id="a-4d" class="form-check-input" name="a-4d" type="checkbox">
																	<label for="a-4d" class="form-check-label">Internet</label>
																</li>
																<li>
																	<input id="a-5e" class="form-check-input" name="a-5e" type="checkbox">
																	<label for="a-5e" class="form-check-label">Microwave</label>
																</li>
																<li>
																	<input id="a-6f" class="form-check-input" name="a-6f" type="checkbox">
																	<label for="a-6f" class="form-check-label">Smoking Allow</label>
																</li>
																<li>
																	<input id="a-7g" class="form-check-input" name="a-7g" type="checkbox">
																	<label for="a-7g" class="form-check-label">Terrace</label>
																</li>
																<li>
																	<input id="a-8h" class="form-check-input" name="a-8h" type="checkbox">
																	<label for="a-8h" class="form-check-label">Balcony</label>
																</li>
																<li>
																	<input id="a-9i" class="form-check-input" name="a-9i" type="checkbox">
																	<label for="a-9i" class="form-check-label">Icon</label>
																</li>
																<li>
																	<input id="a-10j" class="form-check-input" name="a-10j" type="checkbox">
																	<label for="a-10j" class="form-check-label">Wi-Fi</label>
																</li>
																<li>
																	<input id="a-11k" class="form-check-input" name="a-11k" type="checkbox">
																	<label for="a-11k" class="form-check-label">Beach</label>
																</li>
																<li>
																	<input id="a-12l" class="form-check-input" name="a-12l" type="checkbox">
																	<label for="a-12l" class="form-check-label">Parking</label>
																</li>
															</ul>
														</div>
														
													</div>
													<!-- /row -->
													
												</div>
												
											</div>
										</div>

									</div>
									
									<!-- Tab for Rent -->
									<div class="tab-pane fade" id="rent" role="tabpanel" aria-labelledby="rent-tab">
										<div class="full_search_box nexio_search lightanic_search hero_search-radius modern">
											<div class="search_hero_wrapping">
										
												<div class="row">
												
													<div class="col-lg-3 col-sm-12 d-md-none d-lg-block">
														<div class="form-group">
															<label>Price Range</label>
															<input type="text" class="form-control search_input border-0" placeholder="ex. Neighborhood" />
														</div>
													</div>
													
													<div class="col-lg-3 col-md-3 col-sm-12">
														<div class="form-group">
															<label>City/Street</label>
															<div class="input-with-icon">
																<select id="lot-2" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">New York City</option>
																	<option value="2">Honolulu, Hawaii</option>
																	<option value="3">California</option>
																	<option value="4">New Orleans</option>
																	<option value="5">Washington</option>
																	<option value="6">Charleston</option>
																</select>
															</div>
														</div>
													</div>
													
													<div class="col-lg-2 col-md-3 col-sm-12">
														<div class="form-group">
															<label>Property Type</label>
															<div class="input-with-icon">
																<select id="ptype-2" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">All categories</option>
																	<option value="2">Apartment</option>
																	<option value="3">Villas</option>
																	<option value="4">Commercial</option>
																	<option value="5">Offices</option>
																	<option value="6">Garage</option>
																</select>
															</div>
														</div>
													</div>
													
													<div class="col-lg-2 col-md-3 col-sm-12">
														<div class="form-group none">
															<a class="collapsed ad-search" data-bs-toggle="collapse" data-parent="#search" data-bs-target="#advance-search-2" href="javascript:void(0);" aria-expanded="false" aria-controls="advance-search"><i class="fa fa-sliders-h me-2"></i>Advance Filter</a>
														</div>
													</div>
													
													<div class="col-lg-2 col-md-3 col-sm-12 small-padd">
														<div class="form-group none">
															<a href="#" class="btn btn-danger full-width">Search Property</a>
														</div>
													</div>
												</div>
												
												<!-- Collapse Advance Search Form -->
												<div class="collapse" id="advance-search-2" aria-expanded="false" role="banner">
													
													<!-- row -->
													<div class="row">
													
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none style-auto">
																<select id="bedrooms2" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																	<option value="5">5</option>
																</select>
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none style-auto">
																<select id="bathrooms2" class="form-control">
																	<option value="">&nbsp;</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																	<option value="5">5</option>
																</select>
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none">
																<input type="text" class="form-control" placeholder="min sqft" />
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-sm-6">
															<div class="form-group none">
																<input type="text" class="form-control" placeholder="max sqft" />
															</div>
														</div>
														
													</div>
													<!-- /row -->
													
													<!-- row -->
													<div class="row">
														<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
															<h6>Advance Price</h6>
															<div class="rg-slider">
																 <input type="text" class="js-range-slider" name="my_range" value="" />
															</div>
														</div>
													</div>
													<!-- /row -->
													
													<!-- row -->
													<div class="row">
													
														<div class="col-lg-12 col-md-12 col-sm-12 mt-3">
															<h4 class="text-dark">Amenities & Features</h4>
															<ul class="no-ul-list third-row">
																<li>
																	<input id="a-a1" class="form-check-input" name="a-a1" type="checkbox">
																	<label for="a-a1" class="form-check-label">Air Condition</label>
																</li>
																<li>
																	<input id="a-b2" class="form-check-input" name="a-b2" type="checkbox">
																	<label for="a-b2" class="form-check-label">Bedding</label>
																</li>
																<li>
																	<input id="a-c3" class="form-check-input" name="a-c3" type="checkbox">
																	<label for="a-c3" class="form-check-label">Heating</label>
																</li>
																<li>
																	<input id="a-d4" class="form-check-input" name="a-d4" type="checkbox">
																	<label for="a-d4" class="form-check-label">Internet</label>
																</li>
																<li>
																	<input id="a-e5" class="form-check-input" name="a-e5" type="checkbox">
																	<label for="a-e5" class="form-check-label">Microwave</label>
																</li>
																<li>
																	<input id="a-f6" class="form-check-input" name="a-f6" type="checkbox">
																	<label for="a-f6" class="form-check-label">Smoking Allow</label>
																</li>
																<li>
																	<input id="a-g7" class="form-check-input" name="a-g7" type="checkbox">
																	<label for="a-g7" class="form-check-label">Terrace</label>
																</li>
																<li>
																	<input id="a-h8" class="form-check-input" name="a-h8" type="checkbox">
																	<label for="a-h8" class="form-check-label">Balcony</label>
																</li>
																<li>
																	<input id="a-i9" class="form-check-input" name="a-i9" type="checkbox">
																	<label for="a-i9" class="form-check-label">Icon</label>
																</li>
																<li>
																	<input id="a-j10" class="form-check-input" name="a-j10" type="checkbox">
																	<label for="a-j10" class="form-check-label">Wi-Fi</label>
																</li>
																<li>
																	<input id="a-k11" class="form-check-input" name="a-k11" type="checkbox">
																	<label for="a-k11" class="form-check-label">Beach</label>
																</li>
																<li>
																	<input id="a-l12" class="form-check-input" name="a-l12" type="checkbox">
																	<label for="a-l12" class="form-check-label">Parking</label>
																</li>
															</ul>
														</div>
														
													</div>
													<!-- /row -->
													
												</div>
												
											</div>
										</div>

									</div>
									
								</div>
								
							</div>
							
							
						</div>
					</div>
				</div>
			</div>
			<!-- ============================ Hero Banner End ================================== -->
			
			<!-- ============================ Latest Property For Sale Start ================================== -->
			<section>
				@yield('content')
			</section>
			<!-- ============================ Latest Property For Sale End ================================== -->
			
			<!-- ============================ Top Agents ================================== -->
		
			<!-- ============================ Top Agents End ================================== -->
			
			<!-- ============================ Property Location ================================== -->
		
			<!-- ============================ Property Location End ================================== -->
			
			<!-- ============================ Smart Testimonials ================================== -->
			
			<!-- ============================ Smart Testimonials End ================================== -->
			
			<!-- ============================ Our Partner Start ================================== -->
			
			
			<!-- ============================ Our Partner End ================================== -->
			
			<!-- ============================ Price Table Start ================================== -->
			<section class="min">
				<div class="container">
				
					<div class="row justify-content-center">
						<div class="col-lg-7 col-md-10 text-center">
							<div class="sec-heading center">
								<h2>Select your Package</h2>
								<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores</p>
							</div>
						</div>
					</div>
					
					<div class="row align-items-center">
					
						<!-- Single Package -->
						<div class="col-lg-4 col-md-4">
							<div class="pricing_wrap">
								<div class="prt_head">
									<h4>Basic</h4>
								</div>
								<div class="prt_price">
									<h2><span>$</span>29</h2>
									<span>per user, per month</span>
								</div>
								<div class="prt_body">
									<ul>
										<li>99.5% Uptime Guarantee</li>
										<li>120GB CDN Bandwidth</li>
										<li>5GB Cloud Storage</li>
										<li class="none">Personal Help Support</li>
										<li class="none">Enterprise SLA</li>
									</ul>
								</div>
								<div class="prt_footer">
									<a href="#" class="btn choose_package">Start Basic</a>
								</div>
							</div>
						</div>
						
						<!-- Single Package -->
						<div class="col-lg-4 col-md-4">
							<div class="pricing_wrap">
								<div class="prt_head">
									<div class="recommended">Best Value</div>
									<h4>Standard</h4>
								</div>
								<div class="prt_price">
									<h2><span>$</span>49</h2>
									<span>per user, per month</span>
								</div>
								<div class="prt_body">
									<ul>
										<li>99.5% Uptime Guarantee</li>
										<li>150GB CDN Bandwidth</li>
										<li>10GB Cloud Storage</li>
										<li>Personal Help Support</li>
										<li class="none">Enterprise SLA</li>
									</ul>
								</div>
								<div class="prt_footer">
									<a href="#" class="btn choose_package active">Start Standard</a>
								</div>
							</div>
						</div>
						
						<!-- Single Package -->
						<div class="col-lg-4 col-md-4">
							<div class="pricing_wrap">
								<div class="prt_head">
									<h4>Platinum</h4>
								</div>
								<div class="prt_price">
									<h2><span>$</span>79</h2>
									<span>2 user, per month</span>
								</div>
								<div class="prt_body">
									<ul>
										<li>100% Uptime Guarantee</li>
										<li>200GB CDN Bandwidth</li>
										<li>20GB Cloud Storage</li>
										<li>Personal Help Support</li>
										<li>Enterprise SLA</li>
									</ul>
								</div>
								<div class="prt_footer">
									<a href="#" class="btn choose_package">Start Platinum</a>
								</div>
							</div>
						</div>
						
					</div>
					
				</div>	
			</section>
			<!-- ============================ Price Table End ================================== -->						
			
			<!-- ============================ Call To Action ================================== -->
			
			<!-- ============================ Call To Action End ================================== -->
			
			<!-- ============================ Footer Start ================================== -->
		@include("home.blocks.footer")
			<!-- ============================ Footer End ================================== -->
			
			<!-- Log In Modal -->
		
		<!-- ============================================================== -->
		<!-- End Wrapper -->
		<!-- ============================================================== -->

		<!-- ============================================================== -->
		<!-- All Jquery -->
		<!-- ============================================================== -->
	@include("home.blocks.js")
		<!-- ============================================================== -->
		<!-- This page plugins -->
		<!-- ============================================================== -->

	</body>

<!-- Mirrored from shreethemes.net/rentup-demo/rentup/home-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 May 2025 03:54:52 GMT -->
</html>