<!doctype html>
<html class="no-js" lang="">
   <!--<![endif]-->
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>3mm</title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="apple-touch-icon" href="apple-touch-icon.png">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
      <link rel="stylesheet" href="{{url('/')}}/assets/web-style/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://jqueryvalidation.org/files/demo/site-demos.css">
      <link rel="stylesheet" href="{{url('/')}}/assets/web-style/css/bootstrap-theme.min.css">
      <link rel="stylesheet" href="{{url('/')}}/assets/web-style/css/material-design-iconic-font.min.css">
      <link rel="stylesheet" href="{{url('/')}}/assets/web-style/css/main.css">
      <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
      <script src="{{url('/')}}/assets/web-style/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
   </head>
   <body>
      <section class="container-fluid ">
         <div class="row">
            <div class="col-md-8 ">
               <div class="LiftBox">
                  <div class="row">
                     <div class="col-md-8 col-md-offset-1 col-sx-4">
                        <div class="LiftContent">
                           <div class="formContent">
                              <form class=""  method="post">
                                 <input class="InputNumber" type="text" name="" value="" required  placeholder="أدخل رقم الهاتف ">
                                 <button class="Nsubmit" type="submit" name="button">تسجيل دخول</button>
                              </form>
                              <div class="NewUser">
                                 <a href="#" data-toggle="modal" data-target="#myModal1"   >مستخدم جديد</a>
                              </div>
                           </div>
                           <!-- <div class="clearfix"></div> -->
                           <!--  Start  swamp first  -->
                           <div class="Swap-Content">
                              <div class="AmammInfo">
                                 <img class="ttt" src="img/logo.png" alt="">
                                 <br>
                                 <h4>إمكانية إرسال التعميم بكل سهولة إلى أكثر من وجهة، إلى مجموعة أو عدة مجموعات، أو إلى عدد محدد من الأعضاء في أي مجموعة
                                 </h4>
                                 <span>. بادر بالانضمام إلى عمم اليوم</span>
                                 <form class="butSin" action="index.html" method="post">
                                    <button  class="SelectButt" type="button" name="button" data-toggle="modal" data-target="#myModal1">مستخدم جديد</button>
                                    <button  class="SelectBut" type="button" name="button">تسجيل الدخول</button>
                                 </form>
                              </div>
                           </div>
                           <!--  End  swamp first  -->
                           <!--  Start  swamp 2nd  -->
                           <div class="PhoneCode">
                              <h4>تاكيد رمز التحقق</h4>
                              <br>
                              <span>.ادخل رمز التحقق لدخول الي الحساب الخاص بك
                              </span>
                              <br>
                              <form class="EnterCodePhone"  method="post">
                                 <input class="EnterNumber" type="text" name="" value="" placeholder="أدخل رمز التحقق">
                              </form>
                              <br>
                              <br>
                              <br>
                              <br>
                              <br>
                              <br>
                              <p>اعادة إراسل   00:59 ث</p>
                              <br>
                              <button  class="SelectBut" href="index.html" type="button" name="button">تأكيد </button>
                           </div>
                           <!--  End  swamp 2nd  -->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="RigehtBox">

                     <div class="row">
                        <div class="col-md-4">
                           <div class="AmmFeatures">
                              <ui>
                                 <li><i class="zmdi zmdi-accounts"></i> <span> تخصيص الإرسال
                                    </span> 
                                 </li>
                                 <li><i class="zmdi zmdi-accounts"></i><span>تفاعل المستخدم
                                    </span>
                                 </li>
                                 <li><i class="zmdi zmdi-accounts"></i><span>إضافة الاستبيان</span></li>
                                 <li><i class="zmdi zmdi-accounts"></i><span>واجهة بسيطة</span></li>
                              </ui>
                           </div>
                        </div>

                  </div>
               </div>
            </div>
         </div>
      </section>
      <div class="modal fade " id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content Edit-new-user">
               <div class="hade-new-user">
                  <h3>انشاء حساب</h3>
                  <img src="img/new-user-logo.png" alt="">
                  <button type="button" class="Edit-user-Btn-Next" id="signin">التالي</button>
                  <div class="Enter-User-info">
                     <meta name="csrf-token" content="{{ csrf_token() }}">

                     <input required  type="text" id="username"  placeholder="أدخل اسم المستخدم">
                     <label for="mobile" id="mobile-error"></label>
                     <input required  type="text" id="mobile" placeholder="أدخل رقم المستخدم">
                     <select required  class="form-control" id="gender" >
                       <option value="" hidden >الجنس</option>
                       <option value="male">ذكر</option>
                       <option value="female">أنثى</option>
                     </select>
                  </div>
               </div>
            </div>
         </div>
      </div>
      </div>
      <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content submit-new-info">
               <h2>توثيق الهاتف</h2>
               <br>
               <span id="to_mobile_number">سوف نرسل رسالة نصية تختوي على زمز التوثيق الى
               </span>
               <div class="conten-but">
                  <button type="button" id="post_new_user" class="btn btn-default sent-info" data-dismiss="modal"> أرسل</button>
                  <br>
                  <button type="button" id="edit_info" class="btn btn-default change-info" data-toggle="modal" data-target="#myModal1" data-dismiss="modal" >تعديل</button>
               </div>
            </div>
         </div>
      </div>
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
      <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
      <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
      <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
      <script src="{{url('/')}}/assets/web-style/js/vendor/bootstrap.min.js"></script>
      <script src="{{url('/')}}/assets/web-style/js/bubbly-bg.js"></script>
      <script src="{{url('/')}}/assets/web-style/js/clipboard.minjs"></script>
      <script src="{{url('/')}}/assets/web-style/js/main.js"></script>
      <script src="{{url('/')}}/assets/web-style/js/login.js"></script>
   </body>
</html>