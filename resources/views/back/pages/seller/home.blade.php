@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Page Title')
@section('content')
<div class="d-flex justify-content-center">
    <div class="col-lg-8 col-md-12 col-sm-12 mb-20 ">
        <div class="card-box mb-20">
            <div class="clearfix pd-20">
                <div class="pull-left">
                    <h4 class="h4">What do you need to be done?</h4>
                </div>
            </div>
            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleCaptions" data-slide-to="0" class=""></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="1" class="active"></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="2" class=""></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="3" class=""></li>
                    <li data-target="#carouselExampleCaptions" data-slide-to="4" class=""></li>
                </ol>
                <div class="carousel-inner" style="">
                    <div class="carousel-item">
                        <img class="d-block w-100" src="/back/vendors/images/img10.jpg" alt="First slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="color-white">Technicians</h5>
                            <p>

                            </p>
                        </div>
                    </div>
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="/back/vendors/images/img13.jpg" alt="Second slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="color-white">Auto Mechanic</h5>
                            <p>

                            </p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="/back/vendors/images/img12.jpg" alt="Third slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="color-white">Electricians</h5>
                            <p>

                            </p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="/back/vendors/images/img11.jpg" alt="forth slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="color-white">House Painting</h5>
                            <p>

                            </p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="/back/vendors/images/img14.jpg" alt="forth slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="color-white">AC Repairs</h5>
                            <p>

                            </p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
            {{-- <div class="collapse-box collapse" id="carousel4" style="">
                <div class="code-box">
                    <div class="clearfix">
                        <a href="javascript:;" class="btn btn-primary btn-sm code-copy pull-left" data-clipboard-target="#copy-carousel4"><i class="fa fa-clipboard"></i> Copy Code</a>
                        <a href="#carousel4" class="btn btn-primary btn-sm pull-right collapsed" rel="content-y" data-toggle="collapse" role="button" aria-expanded="false"><i class="fa fa-eye-slash"></i> Hide Code</a>
                    </div>
                    <pre><code class="xml copy-pre" id="copy-carousel4">
                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleCaptions" data-slide-to="1" class=""></li>
                        <li data-target="#carouselExampleCaptions" data-slide-to="2" class=""></li>
                    </ol>
                    <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img class="d-block w-100" src="vendors/images/img1.jpg" alt="First slide">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 class="color-white">First slide label</h5>
                                    <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                                </div>
                            </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="vendors/images/img3.jpg" alt="Second slide">
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="color-white">Second slide label</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="vendors/images/img2.jpg" alt="Third slide">
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="color-white">Third slide label</h5>
                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                    </a>
                </div>
                </code></pre>
                </div>
            </div> --}}


        </div>
    </div>
</div>


<div class="row justify-content-center">
    <div class="col-lg-8">
        <a type="button" href="{{ route('seller.job') }}" class="btn btn-success btn-lg btn-block">
            post a job
        </a>
    </div>
</div>

@endsection
