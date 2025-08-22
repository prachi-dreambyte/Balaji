<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id']))  {
     header("Location: auth-signin.php");
     exit;
 }
?>

<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from techzaa.in/larkon/admin/pages-review.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:20:05 GMT -->
<head>
     <!-- Title Meta -->
     <meta charset="utf-8" />
     <title>Reviews List | Larkon - Responsive Admin Dashboard Template</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="description" content="A fully responsive premium admin dashboard template" />
     <meta name="author" content="Techzaa" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />

     <!-- App favicon -->
     <link rel="shortcut icon" href="assets/images/favicon.ico">

     <!-- Vendor css (Require in all Page) -->
     <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />

     <!-- Icons css (Require in all Page) -->
     <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

     <!-- App css (Require in all Page) -->
     <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

     <!-- Theme Config js (Require in all Page) -->
     <script src="assets/js/config.js"></script>
</head>

<body>

     <!-- START Wrapper -->
     <div class="wrapper">

          <!-- ========== Topbar Start ========== -->
         <?php
         include 'header.php';
         ?>

          <!-- ==================================================== -->
          <!-- Start right Content here -->
          <!-- ==================================================== -->
          <div class="page-content">

               <!-- Start Container Fluid -->
               <div class="container-xxl">
                    <div class="row">
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in U.S.A on 21 December 2023</p>
                                        <p class="mb-0">" I recently purchased a t-shirt that I was quite excited about, and I must say, there are several aspects that I really appreciate about it. Firstly, the material is absolutely wonderful."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star-half"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Excellent Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1">Michael B. Coch</h4>
                                             <p class="text-white mb-0">Kaika Hill, CEO / Hill & CO</p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in Canada on 16 March 2023</p>
                                        <p class="mb-0">"I purchased a pair of jeans Firstly, the fabric is fantastic—it's both durable and comfortable. The denim is soft yet sturdy, making it perfect for everyday wear."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star-half"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Best Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1">Theresa T. Brose</h4>
                                             <p class="text-white mb-0">Millenia Life, / General internist</p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in Germany on 23 October 2023</p>
                                        <p class="mb-0">"The fit is perfect, hugging in all the right places while allowing for ease of movement. Overall, this dress exceeded my expectations and has quickly become a favorite in my wardrobe."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Good Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-4.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1"> James L. Erickson</h4>
                                             <p class="text-white mb-0">Omni Tech Solutions / Founder</p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in Germany on 23 October 2023</p>
                                        <p class="mb-0">"The fit is perfect, hugging in all the right places while allowing for ease of movement. Overall, this dress exceeded my expectations and has quickly become a favorite in my wardrobe."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star-half"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Good Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-5.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1"> Lily W. Wilson</h4>
                                             <p class="text-white mb-0">Grade A Investment / Manager </p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in Canada on 29 May 2023</p>
                                        <p class="mb-0">"Additionally, the fit is perfect, providing great support and comfort for all-day wear. These boots have quickly become a staple in my wardrobe, and I couldn't be happier with my purchase."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Excellent Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1">  Sarah M. Brooks</h4>
                                             <p class="text-white mb-0">Metro / Counseling  </p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in U.S.A on 18 August 2023</p>
                                        <p class="mb-0">"The color is rich and vibrant, making it a standout piece in my wardrobe. Overall, this sweater has exceeded my expectations and has quickly become one of my favorite pieces to wear."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star-half"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Best Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-7.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1"> Joe K. Hall</h4>
                                             <p class="text-white mb-0">Atlas Realty / Media specialist </p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in Iceland on 12 May 2023</p>
                                        <p class="mb-0">"I ordered my usual size, but the shoes are either too small or too big, making them uncomfortable to wear.  I would not recommend them to others not buy product, I couldn't be happier with my purchase"</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Bad Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-9.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1">Jennifer Schafer</h4>
                                             <p class="text-white mb-0">Red Bears Tavern / Director </p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                         <div class="col-xl-3 col-md-6">
                              <div class="card overflow-hidden">
                                   <div class="card-body">
                                        <p class="mb-2 text-dark fw-semibold fs-15">Reviewed in Arabic on 18 September 2023</p>
                                        <p class="mb-0">"irstly, the quality of the fabric is exceptional. It's soft, luxurious, and drapes beautifully, giving the dress an elegant and sophisticated look. The design is simply stunning I couldn't be happier with my purchase."</p>
                                        <div class="d-flex align-items-center gap-2 mt-2 mb-1">
                                             <ul class="d-flex text-warning m-0 fs-20 list-unstyled">
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bxs-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                                  <li>
                                                       <i class="bx bx-star"></i>
                                                  </li>
                                             </ul>
                                             <p class="fw-medium mb-0 text-dark fs-15">Best Quality</p>
                                        </div>
                                   </div>
                                   <div class="card-footer bg-primary position-relative mt-3">
                                        <div class="position-absolute top-0 start-0 translate-middle-y ms-3">
                                             <img src="assets/images/users/avatar-10.jpg" alt="" class="avatar-lg border border-light border-3 rounded-circle">
                                        </div>
                                        <div class="position-absolute top-0 end-0 translate-middle-y me-3">
                                             <img src="assets/images/double.png" alt="" class="avatar-md">
                                        </div>
                                        <div class="mt-4">
                                             <h4 class="text-white mb-1">Nashida  Ulfah </h4>
                                             <p class="text-white mb-0">Platinum Interior / Manager </p>
                                        </div>

                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               <!-- End Container Fluid -->

               <!-- ========== Footer Start ========== -->
               <footer class="footer">
                   <div class="container-fluid">
                       <div class="row">
                           <div class="col-12 text-center">
                               <script>document.write(new Date().getFullYear())</script> &copy; Larkon. Crafted by <iconify-icon icon="iconamoon:heart-duotone" class="fs-18 align-middle text-danger"></iconify-icon> <a
                                   href="https://1.envato.market/techzaa" class="fw-bold footer-text" target="_blank">Techzaa</a>
                           </div>
                       </div>
                   </div>
               </footer>
               <!-- ========== Footer End ========== -->

          </div>
          <!-- ==================================================== -->
          <!-- End Page Content -->
          <!-- ==================================================== -->

     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="assets/js/app.js"></script>

</body>


<!-- Mirrored from techzaa.in/larkon/admin/pages-review.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 20 Mar 2025 09:20:05 GMT -->
</html>