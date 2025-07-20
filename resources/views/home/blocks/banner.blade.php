 <div class="clearfix"></div>
 <!-- ============================================================== -->
 <!-- Top header  -->
 <!-- ============================================================== -->


 <!-- ============================ Hero Banner  Start================================== -->
 @unless (!Request::is('/'))
 <div class="hero-banner vedio-banner">
     <div class="overlay"></div>

     <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
         <source src="{{asset('assets/client/img/banners.mp4')}}" type="video/mp4">
     </video>

     <div class="container">

         <div class="row justify-content-center">
             <div class="col-xl-12 col-lg-12 col-md-12">
                 <h1 class="big-header-capt mb-0 text-light">Search Your Next Home</h1>
                 <p class="text-center mb-4 text-light">Find new & featured property located in your local city.</p>
             </div>
         </div>

         <div class="row">
             <div class="col-xl-12 col-lg-12 col-md-12">
                 <div class="simple_tab_search center">
                     <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                         <li class="nav-item">
                             <a class="nav-link active" id="buy-tab" data-bs-toggle="tab" href="#buy"
                                 role="tab" aria-controls="buy" aria-selected="true">Buy</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link" id="sell-tab" data-bs-toggle="tab" href="#sell" role="tab"
                                 aria-controls="sell" aria-selected="false">Sell</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link" id="rent-tab" data-bs-toggle="tab" href="#rent" role="tab"
                                 aria-controls="rent" aria-selected="false">Rent</a>
                         </li>
                     </ul>

                     <div class="tab-content" id="myTabContent">

                         <!-- Tab for Buy -->
                         <div class="tab-pane fade show active" id="buy" role="tabpanel"
                             aria-labelledby="buy-tab">
                             <div class="full_search_box nexio_search lightanic_search hero_search-radius modern">
                                 <div class="search_hero_wrapping">

                                     <div class="row">

                                         <div class="col-lg-3 col-sm-12 d-md-none d-lg-block">
                                             <div class="form-group">
                                                 <label>Price Range</label>
                                                 <input type="text" class="form-control search_input border-0"
                                                     placeholder="ex. Neighborhood" />
                                             </div>
                                         </div>

                                         <div class="col-lg-3 col-md-3 col-sm-12">
                                             <div class="form-group">
                                                 <label>City/Street</label>
                                                 <div class="input-with-icon">
                                                     <select id="location" class="form-control">
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
                                                     <select id="ptypes" class="form-control">
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
                                                 <a class="collapsed ad-search" data-bs-toggle="collapse"
                                                     data-parent="#search" data-bs-target="#advance-search"
                                                     href="javascript:void(0);" aria-expanded="false"
                                                     aria-controls="advance-search"><i
                                                         class="fa fa-sliders-h me-2"></i>Advance Filter</a>
                                             </div>
                                         </div>

                                         <div class="col-lg-2 col-md-3 col-sm-12 small-padd">
                                             <div class="form-group none">
                                                 <a href="#" class="btn btn-danger full-width">Search Property</a>
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Collapse Advance Search Form -->
                                     <div class="collapse" id="advance-search" aria-expanded="false" role="banner">

                                         <!-- row -->
                                         <div class="row">

                                             <div class="col-lg-3 col-md-6 col-sm-6">
                                                 <div class="form-group none style-auto">
                                                     <select id="bedrooms" class="form-control">
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
                                                     <select id="bathrooms" class="form-control">
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
                                                     <input type="text" class="form-control"
                                                         placeholder="min sqft" />
                                                 </div>
                                             </div>

                                             <div class="col-lg-3 col-md-6 col-sm-6">
                                                 <div class="form-group none">
                                                     <input type="text" class="form-control"
                                                         placeholder="max sqft" />
                                                 </div>
                                             </div>

                                         </div>
                                         <!-- /row -->

                                         <!-- row -->
                                         <div class="row">
                                             <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
                                                 <h6>Advance Price</h6>
                                                 <div class="rg-slider">
                                                     <input type="text" class="js-range-slider" name="my_range"
                                                         value="" />
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
                                                         <input id="a-1" class="form-check-input"
                                                             name="a-1" type="checkbox">
                                                         <label for="a-1" class="form-check-label">Air
                                                             Condition</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-2" class="form-check-input"
                                                             name="a-2" type="checkbox">
                                                         <label for="a-2"
                                                             class="form-check-label">Bedding</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-3" class="form-check-input"
                                                             name="a-3" type="checkbox">
                                                         <label for="a-3"
                                                             class="form-check-label">Heating</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-4" class="form-check-input"
                                                             name="a-4" type="checkbox">
                                                         <label for="a-4"
                                                             class="form-check-label">Internet</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-5" class="form-check-input"
                                                             name="a-5" type="checkbox">
                                                         <label for="a-5"
                                                             class="form-check-label">Microwave</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-6" class="form-check-input"
                                                             name="a-6" type="checkbox">
                                                         <label for="a-6" class="form-check-label">Smoking
                                                             Allow</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-7" class="form-check-input"
                                                             name="a-7" type="checkbox">
                                                         <label for="a-7"
                                                             class="form-check-label">Terrace</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-8" class="form-check-input"
                                                             name="a-8" type="checkbox">
                                                         <label for="a-8"
                                                             class="form-check-label">Balcony</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-9" class="form-check-input"
                                                             name="a-9" type="checkbox">
                                                         <label for="a-9" class="form-check-label">Icon</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-10" class="form-check-input"
                                                             name="a-10" type="checkbox">
                                                         <label for="a-10" class="form-check-label">Wi-Fi</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-11" class="form-check-input"
                                                             name="a-11" type="checkbox">
                                                         <label for="a-11" class="form-check-label">Beach</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-12" class="form-check-input"
                                                             name="a-12" type="checkbox">
                                                         <label for="a-12"
                                                             class="form-check-label">Parking</label>
                                                     </li>
                                                 </ul>
                                             </div>

                                         </div>
                                         <!-- /row -->

                                     </div>

                                 </div>
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
                                                 <input type="text" class="form-control search_input border-0"
                                                     placeholder="ex. Neighborhood" />
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
                                                 <a class="collapsed ad-search" data-bs-toggle="collapse"
                                                     data-parent="#search1" data-bs-target="#advance-search-1"
                                                     href="javascript:void(0);" aria-expanded="false"
                                                     aria-controls="advance-search"><i
                                                         class="fa fa-sliders-h me-2"></i>Advance Filter</a>
                                             </div>
                                         </div>

                                         <div class="col-lg-2 col-md-3 col-sm-12 small-padd">
                                             <div class="form-group none">
                                                 <a href="#" class="btn btn-danger full-width">Search
                                                     Property</a>
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Collapse Advance Search Form -->
                                     <div class="collapse" id="advance-search-1" aria-expanded="false"
                                         role="banner">

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
                                                     <input type="text" class="form-control"
                                                         placeholder="min sqft" />
                                                 </div>
                                             </div>

                                             <div class="col-lg-3 col-md-6 col-sm-6">
                                                 <div class="form-group none">
                                                     <input type="text" class="form-control"
                                                         placeholder="max sqft" />
                                                 </div>
                                             </div>

                                         </div>
                                         <!-- /row -->

                                         <!-- row -->
                                         <div class="row">
                                             <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
                                                 <h6>Advance Price</h6>
                                                 <div class="rg-slider">
                                                     <input type="text" class="js-range-slider" name="my_range"
                                                         value="" />
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
                                                         <input id="a-1a" class="form-check-input"
                                                             name="a-1a" type="checkbox">
                                                         <label for="a-1a" class="form-check-label">Air
                                                             Condition</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-2b" class="form-check-input"
                                                             name="a-2b" type="checkbox">
                                                         <label for="a-2b"
                                                             class="form-check-label">Bedding</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-3c" class="form-check-input"
                                                             name="a-3c" type="checkbox">
                                                         <label for="a-3c"
                                                             class="form-check-label">Heating</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-4d" class="form-check-input"
                                                             name="a-4d" type="checkbox">
                                                         <label for="a-4d"
                                                             class="form-check-label">Internet</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-5e" class="form-check-input"
                                                             name="a-5e" type="checkbox">
                                                         <label for="a-5e"
                                                             class="form-check-label">Microwave</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-6f" class="form-check-input"
                                                             name="a-6f" type="checkbox">
                                                         <label for="a-6f" class="form-check-label">Smoking
                                                             Allow</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-7g" class="form-check-input"
                                                             name="a-7g" type="checkbox">
                                                         <label for="a-7g"
                                                             class="form-check-label">Terrace</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-8h" class="form-check-input"
                                                             name="a-8h" type="checkbox">
                                                         <label for="a-8h"
                                                             class="form-check-label">Balcony</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-9i" class="form-check-input"
                                                             name="a-9i" type="checkbox">
                                                         <label for="a-9i" class="form-check-label">Icon</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-10j" class="form-check-input"
                                                             name="a-10j" type="checkbox">
                                                         <label for="a-10j" class="form-check-label">Wi-Fi</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-11k" class="form-check-input"
                                                             name="a-11k" type="checkbox">
                                                         <label for="a-11k" class="form-check-label">Beach</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-12l" class="form-check-input"
                                                             name="a-12l" type="checkbox">
                                                         <label for="a-12l"
                                                             class="form-check-label">Parking</label>
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
                                                 <input type="text" class="form-control search_input border-0"
                                                     placeholder="ex. Neighborhood" />
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
                                                 <a class="collapsed ad-search" data-bs-toggle="collapse"
                                                     data-parent="#search" data-bs-target="#advance-search-2"
                                                     href="javascript:void(0);" aria-expanded="false"
                                                     aria-controls="advance-search"><i
                                                         class="fa fa-sliders-h me-2"></i>Advance Filter</a>
                                             </div>
                                         </div>

                                         <div class="col-lg-2 col-md-3 col-sm-12 small-padd">
                                             <div class="form-group none">
                                                 <a href="#" class="btn btn-danger full-width">Search
                                                     Property</a>
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Collapse Advance Search Form -->
                                     <div class="collapse" id="advance-search-2" aria-expanded="false"
                                         role="banner">

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
                                                     <input type="text" class="form-control"
                                                         placeholder="min sqft" />
                                                 </div>
                                             </div>

                                             <div class="col-lg-3 col-md-6 col-sm-6">
                                                 <div class="form-group none">
                                                     <input type="text" class="form-control"
                                                         placeholder="max sqft" />
                                                 </div>
                                             </div>

                                         </div>
                                         <!-- /row -->

                                         <!-- row -->
                                         <div class="row">
                                             <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
                                                 <h6>Advance Price</h6>
                                                 <div class="rg-slider">
                                                     <input type="text" class="js-range-slider" name="my_range"
                                                         value="" />
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
                                                         <input id="a-a1" class="form-check-input"
                                                             name="a-a1" type="checkbox">
                                                         <label for="a-a1" class="form-check-label">Air
                                                             Condition</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-b2" class="form-check-input"
                                                             name="a-b2" type="checkbox">
                                                         <label for="a-b2"
                                                             class="form-check-label">Bedding</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-c3" class="form-check-input"
                                                             name="a-c3" type="checkbox">
                                                         <label for="a-c3"
                                                             class="form-check-label">Heating</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-d4" class="form-check-input"
                                                             name="a-d4" type="checkbox">
                                                         <label for="a-d4"
                                                             class="form-check-label">Internet</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-e5" class="form-check-input"
                                                             name="a-e5" type="checkbox">
                                                         <label for="a-e5"
                                                             class="form-check-label">Microwave</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-f6" class="form-check-input"
                                                             name="a-f6" type="checkbox">
                                                         <label for="a-f6" class="form-check-label">Smoking
                                                             Allow</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-g7" class="form-check-input"
                                                             name="a-g7" type="checkbox">
                                                         <label for="a-g7"
                                                             class="form-check-label">Terrace</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-h8" class="form-check-input"
                                                             name="a-h8" type="checkbox">
                                                         <label for="a-h8"
                                                             class="form-check-label">Balcony</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-i9" class="form-check-input"
                                                             name="a-i9" type="checkbox">
                                                         <label for="a-i9" class="form-check-label">Icon</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-j10" class="form-check-input"
                                                             name="a-j10" type="checkbox">
                                                         <label for="a-j10" class="form-check-label">Wi-Fi</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-k11" class="form-check-input"
                                                             name="a-k11" type="checkbox">
                                                         <label for="a-k11" class="form-check-label">Beach</label>
                                                     </li>
                                                     <li>
                                                         <input id="a-l12" class="form-check-input"
                                                             name="a-l12" type="checkbox">
                                                         <label for="a-l12"
                                                             class="form-check-label">Parking</label>
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
     @endunless
 </div>
