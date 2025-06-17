					<nav id="navigation" class="navigation navigation-landscape">
						<div class="nav-header">
							<a class="nav-brand static-logo" href="#"><img src="{{asset('assets/client/img/logo.png')}}" class="logo" alt="" /></a>
							<a class="nav-brand fixed-logo" href="#"><img src="{{asset('assets/client/img/logo.png')}}" class="logo" alt="" /></a>
							<div class="nav-toggle"></div>
						</div>
						<div class="nav-menus-wrapper" style="transition-property: none;">
							<ul class="nav-menu">
							
								<li class="active"><a href="#">Home<span class="submenu-indicator"></span></a>
									<ul class="nav-dropdown nav-submenu">
										<li><a href="index.html">Home 1</a></li>
										
									</ul>
								</li>
								 <li class="nav-item">
    <a href="{{ route('status.agreement') }}" class="nav-link alio_green">
    <i class="fas fa-sign-in-alt me-1"></i>
    <span class="dn-lg">Status Agreement</span>
</a>
</li>

								<li><a href="#">Listings<span class="submenu-indicator"></span></a>
									<ul class="nav-dropdown nav-submenu">
										<li><a href="#">Listing Grid<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="grid-layout-with-sidebar.html">Grid Style 1</a></li>
												<li><a href="grid-layout-2.html">Grid Style 2</a></li>
												<li><a href="grid-layout-3.html">Grid Style 3</a></li>
											</ul>
										</li>
										<li><a href="#">Listing List<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="list-layout-with-sidebar.html">List Style 1</a></li>
												<li><a href="list-layout-with-map-2.html">List Style 2</a></li>
											</ul>
										</li>
										<li><a href="#">Listing Map<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="half-map.html">Half Map</a></li>
												<li><a href="half-map-2.html">Half Map 2</a></li>
												<li><a href="classical-layout-with-map.html">Classical With Sidebar</a></li>
												<li><a href="list-layout-with-map.html">List Sidebar Map</a></li>
												<li><a href="grid-layout-with-map.html">Grid Sidebar Map</a></li>
											</ul>
										</li>
										<li><a href="#">Agents View<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="agents.html">Agent Grid Style</a></li>
												<li><a href="agents-2.html">Agent Grid 2</a></li>
												<li><a href="agent-page.html">Agent Detail Page</a></li>
											</ul>
										</li>
										<li><a href="#">Agency View<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="agencies.html">Agency Grid Style</a></li>
												<li><a href="agency-page.html">Agency Detail Page</a></li>
											</ul>
										</li>
									</ul>
								</li>
								
								<li><a href="#">Property<span class="submenu-indicator"></span></a>
									<ul class="nav-dropdown nav-submenu">
										<li class=""><a href="#">User Admin<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="dashboard.html">User Dashboard</a></li>
												<li><a href="my-profile.html">My Profile</a></li>
												<li><a href="my-property.html">My Property</a></li>
												<li><a href="messages.html">Messages</a></li>
												<li><a href="bookmark-list.html">Bookmark Property</a></li>
												<li><a href="submit-property.html">Submit Property</a></li>
											</ul>
										</li>
										<li><a href="#">Single Property<span class="submenu-indicator"></span></a>
											<ul class="nav-dropdown nav-submenu">
												<li><a href="single-property-1.html">Single Property 1</a></li>
												<li><a href="single-property-2.html">Single Property 2</a></li>
												<li><a href="single-property-3.html">Single Property 3</a></li>
												<li><a href="single-property-4.html">Single Property 4</a></li>
											</ul>
										</li>
										<li><a href="compare-property.html">Compare Property</a></li>
									</ul>
								</li>
								
								<li><a href="#">Pages<span class="submenu-indicator"></span></a>
									<ul class="nav-dropdown nav-submenu">
										<li><a href="blog.html">Blog Style</a></li>
										<li><a href="about-us.html">About Us</a></li>
										<li><a href="pricing.html">Pricing</a></li>
										<li><a href="404.html">404 Page</a></li>
										<li><a href="checkout.html">Checkout</a></li>
										<li><a href="contact.html">Contact</a></li>
										<li><a href="component.html">Elements</a></li>
										<li><a href="privacy.html">Privacy Policy</a></li>
										<li><a href="faq.html">FAQs</a></li>
									</ul>
								</li>
								
							</ul>
							
							<ul class="nav-menu nav-menu-social align-to-right">
								
								<li>
									<a href="{{ route('auth.login') }}" class="alio_green" data-bs-toggle="modal" data-bs-target="#login">
										<i class="fas fa-sign-in-alt me-1"></i><span class="dn-lg">Sign In</span>
									</a>
								</li>
								<li class="add-listing">
									<a href="submit-property.html"  class="bg-danger">
										<i class="fas fa-plus-circle me-1"></i>Add Property
									</a>
								</li>
							</ul>
						</div>
					</nav>