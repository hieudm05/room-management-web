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

			<!-- ============================ Latest Property For Sale Start ================================== -->
			<section>
				@yield('content')
			</section>
			<!-- ============================ Latest Property For Sale End ================================== -->


			<!-- ============================ Call To Action End ================================== -->

			<!-- ============================ Footer Start ================================== -->
		@include("home.blocks.footer")
			<!-- ============================ Footer End ================================== -->

			<!-- Log In Modal -->

			<a id="back2Top" class="top-scroll" title="Back to top" href="#"><i class="ti-arrow-up"></i></a>


		</div>
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