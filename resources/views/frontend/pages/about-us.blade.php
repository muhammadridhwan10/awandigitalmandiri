@extends('frontend.layouts.master')

@section('title','ADM - About Us')
<style>
.step-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: #e6f5fc;
    border-radius: 50%;
    margin-right: 20px;
    position: relative;
}

.step-number {
    color: #004aad;
    font-size: 20px;
    font-weight: bold;
}

.step-icon::after {
    content: "";
    width: 2px;
    height: 100%;
    background-color: #004aad;
    position: absolute;
    top: 60px;
    left: 50%;
    transform: translateX(-50%);
    z-index: -1;
}

.step-content {
    flex-grow: 1;
}

.step-content h5 {
    margin-bottom: 10px;
}

.step-content p {
    margin: 0;
    color: #6c757d;
}

/* Remove the connector line for the last step */
.col-12:last-child .step-icon::after {
    display: none;
}

</style>

@section('main-content')

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index1.html">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active"><a href="blog-single.html">About Us</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- About Us -->
	<section class="about-us section">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-12">
						<div class="about-content">
							@php
								$settings=DB::table('settings')->get();
							@endphp
							<h3 class="mb-4"><span style="color:#004aad">About Us</span></h3>
    						<p>@foreach($settings as $data) {!! $data->description !!} @endforeach</p>

							<h3 class="mt-5 mb-4"><span style="color:#004aad">Our Best Service</span></h3>
							<div class="row">
								<div class="col-md-4 text-center mb-4">
									<div class="service-box">
										<i class="ti-desktop fa-3x" style="color: #004aad;"></i>
										<h4 class="mt-3">Website Development</h4>
										<p>Branding is an image so that a product can attract.</p>
									</div>
								</div>
								<div class="col-md-4 text-center mb-4">
									<div class="service-box">
										<i class="ti-mobile fa-3x" style="color: #004aad;"></i>
										<h4 class="mt-3">Mobile App Development</h4>
										<p>Creatives that you will promote to the target market.</p>
									</div>
								</div>
								<div class="col-md-4 text-center mb-4">
									<div class="service-box">
										<i class="ti-stats-up fa-3x" style="color: #004aad;"></i>
										<h4 class="mt-3">Digital Marketing Services</h4>
										<p>A fundamental tool designed to achieve a company's objectives.</p>
									</div>
								</div>
							</div>

							<div class="row mt-5">
								<div class="col-md-6 mb-4">
									<div class="service-box">
										<h3><span style="color:#004aad">Our Vision</span></h3>
										<p>To become the go-to IT partner for businesses globally, recognized for delivering innovative, reliable, and high-quality digital solutions that transform industries and enhance user experiences.</p>
									</div>
								</div>
								<div class="col-md-6 mb-4">
									<div class="service-box">
										<h3><span style="color:#004aad">Our Mission</span></h3>
										<p>To craft high-quality, custom IT solutions that address the unique needs of each client, foster enduring partnerships built on transparency, trust, and mutual success, and continuously innovate to adapt to the ever-changing technological landscape.</p>
									</div>
								</div>
							</div>

							<h3 class="mt-5 mb-4"><span style="color:#004aad">Why Choose Us</span></h3>
							<div class="row">
								<div class="col-md-4 text-center mb-4">
									<div class="service-box">
										<h4 class="mt-3">Innovative Solutions</h4>
										<p>We deliver cutting-edge IT services tailored to your unique business needs.</p>
									</div>
								</div>
								<div class="col-md-4 text-center mb-4">
									<div class="service-box">
										<h4 class="mt-3">Experienced Team</h4>
										<p>Our skilled professionals ensure exceptional quality and reliability.</p>
									</div>
								</div>
								<div class="col-md-4 text-center mb-4">
									<div class="service-box">
										<h4 class="mt-3">Comprehensive Support</h4>
										<p>From start to finish, we provide seamless assistance every step of the way.</p>
									</div>
								</div>
							</div>

							<h3 class="mt-5 mb-4"><span style="color:#004aad">Industries We Serve</span></h3>
							<div class="row">
								<!-- E-commerce -->
								<div class="col-md-6 d-flex align-items-center mb-4">
									<div>
										<div>
											<h4><b>E-Commerce</b></h4>
											<p>treamline online retail operations with custom platforms designed for scalability and superior user experiences, driving increased conversions and customer loyalty.</p>
										</div>
									</div>
									<img src="{{asset('backend/img/ecommerce.png')}}" alt="E-commerce" class="img-fluid" style="max-height: 200px; max-width: 200px;">
								</div>
								<!-- Inventory Management -->
								<div class="col-md-6 d-flex align-items-center mb-4">
									<div>
										<div>
											<h4><b>Tour and Travel</b></h4>
											<p>Enhance the travel experience with innovative apps and websites, offering seamless booking systems, travel guides, and personalized itineraries.</p>
										</div>
									</div>
									<img src="{{asset('backend/img/travel.png')}}" alt="Inventory Management" class="img-fluid" style="max-height: 200px; max-width: 200px;">
								</div>
								<!-- E-learning -->
								<div class="col-md-6 d-flex align-items-center mb-4">
									<div>
										<div>
											<h4><b>E-learning</b></h4>
											<p>Transform education with robust e-learning platforms that facilitate interactive learning, video conferencing, and real-time progress tracking.</p>
										</div>
									</div>
									<img src="{{asset('backend/img/elearning.png')}}" alt="E-learning" class="img-fluid" style="max-height: 200px; max-width: 200px;">
								</div>
								<!-- Inventory Management -->
								<div class="col-md-6 d-flex align-items-center mb-4">
									<div>
										<div>
											<h4><b>Inventory Management</b></h4>
											<p>Optimize supply chain processes with advanced systems for inventory tracking, stock management, and reporting, ensuring efficiency and cost-effectiveness.</p>
										</div>
									</div>
									<img src="{{asset('backend/img/inventory.png')}}" alt="Inventory Management" class="img-fluid" style="max-height: 200px; max-width: 200px;">
								</div>
								<!-- Accounting -->
								<div class="col-md-6 d-flex align-items-center mb-4">
									<div>
										<div>
											<h4><b>Accounting</b></h4>
											<p>Simplify financial operations with comprehensive accounting systems, offering automated reporting, payroll management, and real-time analytics for better decision-making.</p>
										</div>
									</div>
									<img src="{{asset('backend/img/accounting.png')}}" alt="Accounting" class="img-fluid" style="max-height: 200px; max-width: 200px;">
								</div>
								<!-- Point of Sale (POS) -->
								<div class="col-md-6 d-flex align-items-center mb-4">
									<div>
										<div>
											<h4><b>Point of Sale (POS)</b></h4>
											<p>Deliver seamless retail experiences with reliable POS systems that integrate inventory, sales, and customer data for improved decision-making.</p>
										</div>
									</div>
									<img src="{{asset('backend/img/pos.png')}}" alt="Point of Sale" class="img-fluid" style="max-height: 200px; max-width: 200px;">
								</div>
							</div>

							<h3 class="mt-5 mb-4"><span style="color:#004aad">Our Process</span></h3>
							<div class="row">
								<!-- Step 01 -->
								<div class="col-12 d-flex mb-4">
									<div class="step-icon">
										<div class="step-number">01</div>
									</div>
									<div class="step-content">
										<h5 style="color:#004aad; font-weight: bold;">Send Us Your Order Request</h5>
										<p>Choose the package you need by visiting our website or sending us an email. Provide details of the source code you want to purchase, including any specific requirements or preferences.</p>
									</div>
								</div>
								<!-- Step 02 -->
								<div class="col-12 d-flex mb-4">
									<div class="step-icon">
										<div class="step-number">02</div>
									</div>
									<div class="step-content">
										<h5 style="color:#004aad; font-weight: bold;">Complete Your Order</h5>
										<p>Our team will begin customizing or preparing the source code based on your chosen package and requirements. This phase ensures the product is tailored to meet your needs.</p>
									</div>
								</div>
								<!-- Step 03 -->
								<div class="col-12 d-flex mb-4">
									<div class="step-icon">
										<div class="step-number">03</div>
									</div>
									<div class="step-content">
										<h5 style="color:#004aad; font-weight: bold;">Development Phase</h5>
										<p>Our team will begin customizing or preparing the source code based on your chosen package and requirements. This phase ensures the product is tailored to meet your needs.</p>
									</div>
								</div>
								<!-- Step 04 -->
								<div class="col-12 d-flex mb-4">
									<div class="step-icon">
										<div class="step-number">04</div>
									</div>
									<div class="step-content">
										<h5 style="color:#004aad; font-weight: bold;">Receive Your Product</h5>
										<p>Once the development is complete, we will send the final product to your email. This delivery will include all necessary files and instructions for deployment or use.</p>
									</div>
								</div>
								<!-- Step 05 -->
								<div class="col-12 d-flex mb-4">
									<div class="step-icon">
										<div class="step-number">05</div>
									</div>
									<div class="step-content">
										<h5 style="color:#004aad; font-weight: bold;">Ready to Launch</h5>
										<p>With the product in hand, you’re ready to deploy or use it as intended. If you need technical assistance or further guidance, our team is available to support your launch.</p>
									</div>
								</div>
							</div>

							<h3 class="mt-5 mb-4"><span style="color:#004aad">Join Us in Transforming the Digital World</span></h3>
							<div class="row">
								<div class="col-md-12 mb-4">
									<div class="service-box">
										<p>At PT Awan Digital Mandiri, we believe that the future belongs to those who innovate. Partner with us to unlock new opportunities and redefine your business’s digital journey.
Contact us today and take the first step toward unparalleled success!</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	</section>
	<!-- End About Us -->
@endsection
