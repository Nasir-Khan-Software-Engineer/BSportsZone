<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Meher Art">
    <link rel="shortcut icon" href="favicon.png">

    <meta name="description" content="" />
    <meta name="keywords" content="bootstrap, bootstrap4" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('website/css/tiny-slider.css') }}">

    <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide-extension-auto-scroll@0.5.3/dist/js/splide-extension-auto-scroll.min.js"></script>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Delius&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">




    <script>
    var Website = {};
    </script>




    @yield('styles')
    @yield('seo')


    <title>Meher Art - Enjoys Arabic Calligraphy</title>
</head>

<body>

    <!-- Start Header/Navigation -->
    <nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark fixed-top" arial-label="Furni navigation bar">

        <div class="container">
            <a class="navbar-brand" href="{{ route('index') }}">BZportsZone<span>.</span></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsFurni">
                <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                    <li><a class="nav-link" href="contact.html">Shop</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('index') }}">Portugal</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('shop') }}">Italy</a></li>
                    <li><a class="nav-link" href="about.html">Germany</a></li>
                    <li><a class="nav-link" href="services.html">Football</a></li>
                    <li><a class="nav-link" href="blog.html">Cricket</a></li>
                    <li><a class="nav-link" href="contact.html">Club</a></li>
                </ul>

                <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
                    <li class="check-out-btn"><a class="nav-link" href="{{ route('checkout') }}">
                        <img src="{{asset('website/images/cart.svg') }}"></a>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
    <!-- End Header/Navigation -->

    <div id="toast-container"></div>

    @yield('content')


    	<!-- Start Testimonial Slider -->
	<div class="testimonial-section d-none">
		<div class="container">
			<div class="row">
				<div class="col-lg-7 mx-auto text-center">
					<h2 class="section-title">What Our Customers Say 💬</h2>
				</div>
			</div>

			<div class="row justify-content-center">
				<div class="col-lg-12">
					<div class="review-slider-section">
						<div id="reviewSplide" class="splide">
							<div class="splide__track">
								<ul class="splide__list">

									<li class="splide__slide">
										<div class="review-card">
											<img src="{{ asset('website/images/review/review (1).jpeg') }}" alt="">
										</div>
									</li>

									<li class="splide__slide">
										<div class="review-card">
											<img src="{{ asset('website/images/review/review (2).jpeg') }}" alt="">
										</div>
									</li>

									<li class="splide__slide">
										<div class="review-card">
											<img src="{{ asset('website/images/review/review (3).jpeg') }}" alt="">
										</div>
									</li>

									<li class="splide__slide">
										<div class="review-card">
											<img src="{{ asset('website/images/review/review (4).jpeg') }}" alt="">
										</div>
									</li>

									<li class="splide__slide">
										<div class="review-card">
											<img src="{{ asset('website/images/review/review (5).jpeg') }}" alt="">
										</div>
									</li>

								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Testimonial Slider -->

    <!-- Start Footer Section -->
    <footer class="footer-section">
        <div class="container relative">

            <div class="sofa-img">
                <img src="http://127.0.0.1:8000/images/1/Product/argentina-back_2026-01-16_17-37-23.png" alt="Image" class="img-fluid">
            </div>

            <div class="row g-5 mb-5">
                <div class="col-lg-4">
                    <div class="mb-4 footer-logo-wrap"><a href="#" class="footer-logo">BZportsZone<span>.</span></a></div>
                    <p class="mb-4 font-roboto-regular">We are committed to delivering premium-quality sports jerseys with reliable service and customer satisfaction at the core. From product quality to post-purchase support, we make sure your shopping experience is smooth, secure, and enjoyable.</p>

                    <ul class="list-unstyled custom-social">
                        <li><a href="#"><span class="fa fa-brands fa-facebook-f"></span></a></li>
                        <li><a href="#"><span class="fa fa-brands fa-twitter"></span></a></li>
                        <li><a href="#"><span class="fa fa-brands fa-instagram"></span></a></li>
                        <li><a href="#"><span class="fa fa-brands fa-linkedin"></span></a></li>
                    </ul>
                </div>

                <div class="col-lg-8">
                    <div class="row links-wrap">
                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">About us</a></li>
                                <li><a href="#">Contact us</a></li>
                                <li><a href="#">Blog</a></li>
                                <li><a href="#">Return Policy</a></li>
                            </ul>
                        </div>

                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">Football Jersey</a></li>
                                <li><a href="#">Cricket Jersey</a></li>
                                <li><a href="#">Counter Jersey</a></li>
                                <li><a href="#">Club Jersey</a></li>
                            </ul>
                        </div>

                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">Barcelona Jersey</a></li>
                                <li><a href="#">Real Madrid Jersey</a></li>
                                <li><a href="#">Liverpool Jersey</a></li>
                                <li><a href="#">Arsenal Jersey</a></li>
                            </ul>
                        </div>

                        <div class="col-6 col-sm-6 col-md-3">
                            <ul class="list-unstyled">
                                <li><a href="#">Manchester United Jersey</a></li>
                                <li><a href="#">Mancity Jersey</a></li>
                                <li><a href="#">AC Milan Jersey</a></li>
                                <li><a href="#">PSG Jersey</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <div class="border-top copyright">
                <div class="row pt-4">
                    <div class="col-lg-6">
                        <p class="mb-2 text-center text-lg-start">Copyright &copy;
                            <span id="copyright-year"></span>. All Rights Reserved. &mdash; BSportsZone
                            <!-- License information: https://untree.co/license/ -->
                        </p>
                    </div>

                    <div class="col-lg-6 text-center text-lg-end">
                        <ul class="list-unstyled d-inline-flex ms-auto">
                            <li class="me-4"><a href="#">Terms &amp; Conditions</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </footer>
    <!-- End Footer Section -->


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('website/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('website/js/tiny-slider.js') }}"></script>
    <script src="{{ asset('website/js/custom.js') }}"></script>

    <script>
        var websiteData = {
            currentYear: new Date().getFullYear()
        }
    </script>

    <script src="{{ asset('website/js/common.js') }}"></script>
    <script src="{{ asset('website/js/add-to-cart.js') }}"></script>

    <script>
        $(document).ready(function() {
            Website.Common.updateCopyRightYear();
            Website.AddToCart.setWebsiteCartCount();


            $('.add-to-cart-btn').on('click', function() {
                let selectedProduct = {
                    id: $(this).data('product-id'),
                    name: $(this).data('product-name'),
                    image: $(this).data('product-image'),

                    quantity: 1,

                    variation_id: $(this).data('variation-id'),
                    variation_tagline: $(this).data('variation-tagline'),
                    variation_price_after_discount: $(this).data('variation-price-after-discount'),
                    variation_selling_price: $(this).data('variation-selling-price'),
                    variation_discount_type: $(this).data('variation-discount-type'),
                    variation_discount_value: $(this).data('variation-discount-value'),
                };

                let isAdded = Website.AddToCart.addProductToWebsiteCart(selectedProduct);
                if(isAdded){
                    Website.Common.showToastMessage('success', 'The '+selectedProduct.variation_tagline + ' added to cart successfully!');
                    Website.AddToCart.setWebsiteCartCount();
                }

            })

            // Website.Common.showToastMessage('success', 'Product added to cart!');
            // Website.Common.showToastMessage('error', 'Something went wrong!');
            // Website.Common.showToastMessage('warning', 'Please select a size first!');

        });
    </script>
<!-- 
    <script>
		new Splide('#reviewSplide', {
			type: 'loop',
			perPage: 6,
			gap: '5px',
			arrows: true,
			pagination: false,
			drag: true,
			autoScroll: {
				speed: 1.2,
			},
			breakpoints: {
				768: { perPage: 5 },
				480: { perPage: 4 },
				320: { perPage: 3 }
			}
		}).mount(window.splide.Extensions);
	</script> -->


    @yield('scripts')
</body>

</html>